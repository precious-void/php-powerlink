<?php

namespace PowerLink;

use GuzzleHttp\Client as HTTPClient;

abstract class Client
{
    /**
     * Host 
     */
    const BASE_URL = 'https://api.powerlink.co.il/api/record/';

    /**
     * Error messages
     */
    const ERROR_INVALID_API_KEY = 'Invalid TokenID.';

    /**
     * @var string
     */
    protected $token_id;

    /**
     * @var HTTPClient
     */
    protected $client;

    /**
     * Constructor method for Base class.
     *
     * @param string $token_id Token id for PowerLink account
     * @param string $base_uri (optional) Base URL for PowerLink API
     */
    public function __construct($token_id, $base_uri = self::BASE_URL)
    {
        $this->token_id = $token_id;
        $this->client = new HTTPClient([
            'base_uri' => $base_uri,
            'headers' => [
                'User-Agent' => "stelzer/php-powerlink/" . PowerLink::VERSION,
                'Accept'     => 'application/json'
            ]
        ]);
    }
}
