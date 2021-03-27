<?php

namespace PowerLink;

use GuzzleHttp\Client as HTTPClient;

class Client
{
    /**
     * Host 
     */
    const BASE_URL = 'https://api.powerlink.co.il/api/';

    /**
     * @var HTTPClient
     */
    protected $client;

    /**
     * Constructor method for Base class.
     *
     * @param string $token_id Token id for PowerLink account
     * @param string $base_uri (optional) Base URL for PowerLink API
     * 
     */
    public function __construct($token_id, $base_url = self::BASE_URL)
    {
        $this->client = new HTTPClient([
            'base_uri' => $base_url,
            'headers' => [
                'tokenid'    => $token_id,
                'User-Agent' => "stelzer/php-powerlink/" . PowerLink::VERSION,
                'Accept'     => 'application/json'
            ]
        ]);
    }

    /**
     * Get Initialized Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
