<?php


namespace Khalidmsheet\Ktoken;


use Illuminate\Support\Facades\Cache;

class KTokenStorage
{
    /**
     * Define the prefix for cache.
     */
    private $CACHE_PREFIX = "kt.tokens";

    public function __construct()
    {
        $this->CACHE_PREFIX = config('ktoken.storage_path');
    }

    public function revoke($identifier)
    {
        if ( !$this->find($identifier) || $this->find($identifier)['revoked'] )
            return false;


        $cachedTokens = collect($this->getTokens())->map(function ($token) use ($identifier) {
            if ( $token['id'] === $identifier )
                $token['revoked'] = 1;

            return $token;
        });

        Cache::forget($this->CACHE_PREFIX);

        Cache::rememberForever($this->CACHE_PREFIX, function () use ($cachedTokens) {
            return $cachedTokens;
        });

        return true;
    }

    private function getTokens()
    {
        return Cache::get($this->CACHE_PREFIX);
    }

    public function find($identifier)
    {
        if ( Cache::has($this->CACHE_PREFIX) )
            return collect($this->getTokens())
                ->where('id', $identifier)
                ->first();

        return false;
    }

    public function store($identifier, $iss, $ttl = 60, $isRevoked = 0): void
    {
        $tokens = collect([]);

        if ( Cache::has($this->CACHE_PREFIX) )
            $tokens = collect(Cache::get($this->CACHE_PREFIX));


        $tokens->add([
            'id' => $identifier,
            'iss' => $iss,
            'ttl' => $ttl,
            'revoked' => $isRevoked,
        ]);

        Cache::forget($this->CACHE_PREFIX);

        Cache::rememberForever($this->CACHE_PREFIX, function () use ($tokens) {
            return $tokens;
        });
    }

}