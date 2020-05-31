<?php


namespace Khalidmsheet\Ktoken\Traits;


use Khalidmsheet\Ktoken\Ktoken;

trait HasApiKtokens
{

    /**
     * @var Ktoken
     */
    private $ktoken;

    public function __construct()
    {
        $this->ktoken = new Ktoken();
    }


    public function createToken()
    {
        return $this->ktoken->encrypt([
            'id' => $this->getAuthenticateIdentifier()
        ]);
    }


    public function invalidateToken($token)
    {
        return $this->ktoken->invalidateToken($token);
    }


    protected function getAuthenticateIdentifier()
    {
        if ( method_exists(parent::class, 'getAuthenticateIdentifier') )
            if ( parent::getAuthenticateIdentifier() == null )
                return parent::getAuthenticateIdentifier();

        return $this->id;
    }
}

