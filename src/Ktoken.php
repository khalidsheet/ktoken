<?php


namespace Khalidmsheet\Ktoken;


use Carbon\Carbon;
use Exception;
use Ramsey\Uuid\Uuid;

class Ktoken extends KTokenStorage
{
    private $_options = [
        'password' => null,
        'cipher' => "AES-256-GCM",
        "add" => [
            'iss' => '',
            'expires' => 0
        ],
    ];


    private $TOKEN_PREFIX = 'kt';

    /**
     * Encryption constructor.
     * @param array $options
     * @throws Exception
     */
    public function __construct($options = [])
    {
        $this->TOKEN_PREFIX = config('ktoken.prefix', 'kt');
        $this->_options['password'] = config('ktoken.password');
        $this->_options['add']['expires'] = Carbon::now()->addMinutes(isset($options['ttl']) ? $options['ttl'] : 60)->getTimestamp();
        $this->_options['add']['iss'] = request()->fullUrl();
    }


    /**
     * @param array $data The data you want to encrypt
     * @return array
     */
    public function encrypt($data)
    {
        // Add some parameters to the data
        $data = array_merge([], [
            'data' => array_merge([], $data, $this->_options['add']),

            // kti is the token identifier
            'kti' => strtr(Uuid::uuid4(), '-', '1')
        ]);

        // Get IV length of the Cipher.
        $ivLength = openssl_cipher_iv_length($this->_options['cipher']);

        // Generate IV.
        $iv = openssl_random_pseudo_bytes($ivLength);

        // Encrypt the provided data with Openssl.
        $encryptedRaw = openssl_encrypt(json_encode($data), $this->_options['cipher'], $this->_options['password'], OPENSSL_RAW_DATA, $iv, $tag);

        // Generate a Hash HMAC from a SHA256.
        $hmac = hash_hmac('sha256', $encryptedRaw, $this->_options['password'], true);

        // Combine all of the data together to generate a new Token and returning it.
        $accessToken = $this->TOKEN_PREFIX . rtrim(strtr(base64_encode($tag), '+/=', '-_,'), ',') . '.' . rtrim(strtr(base64_encode($iv . $hmac . $encryptedRaw), '+/=', '-_,'), ',');


        // Store the token
        $this->store($data['kti'], $this->_options['add']['iss'], $this->_options['add']['expires']);

        return [
            'accessToken' => $accessToken,
            'expiresAt' => $this->_options['add']['expires']
        ];
    }

    public function generateKey($length = 64)
    {
        return base64_encode(openssl_random_pseudo_bytes($length));
    }

    public function invalidateToken($token)
    {
        $data = $this->decrypt($token);

        $revoked = $this->revoke($data->kti);

        if ( !$revoked ) {
            return [
                "revoked" => false
            ];
        }


        return [
            "revoked" => true
        ];
    }

    /**
     * @param $token
     * @return bool|mixed
     */
    public function decrypt($token)
    {
        if ( substr($token, 0, strlen($this->TOKEN_PREFIX)) !== $this->TOKEN_PREFIX )
            return false;

        // remove the prefix from the provided token
        $token = substr(strtr($token, '-_,', '+/='), strlen($this->TOKEN_PREFIX));

        $tag = explode('.', $token)[0];
        $token = explode('.', $token)[1];


        // decode the token from base64
        $base64Decoded = base64_decode($token);

        // get the cipher length
        $ivLength = openssl_cipher_iv_length($this->_options['cipher']);

        // extract the IV from the decoded token
        $iv = substr($base64Decoded, 0, $ivLength);

        // extract the HMAC from the decoded token
        $hmac = substr($base64Decoded, $ivLength, 32);

        $planText = substr($base64Decoded, $ivLength + 32);

        $originalData = openssl_decrypt($planText, $this->_options['cipher'], $this->_options['password'], $options = OPENSSL_RAW_DATA, $iv, base64_decode($tag));

        $calcmac = hash_hmac('sha256', $planText, $this->_options['password'], true);

        if ( !hash_equals($hmac, $calcmac) )
            return false;

        return json_decode($originalData);

    }

    protected function getOptions()
    {
        return $this->_options;
    }


}