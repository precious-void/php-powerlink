<?php

namespace PowerLink;

use PowerLink\Exceptions\PowerLinkException;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class PowerLink
{
    /** @var string Package version */
    const VERSION = '1.0.0';

    /** @var string The API access token */
    protected static $token = null;

    /**
     * @param string|null $token Token ID for authorization
     * @param Client|null $client Custom Client
     * @throws PowerLinkException When no token is provided
     */
    public function __construct($token = null, $client = null)
    {
        if ($token === null) {
            if (self::$token === null) {
                $msg = 'No token provided, and none is globally set. ';
                $msg .= 'Use PowerLink::setToken, or instantiate the Diffbot class with a $token parameter.';
                throw new PowerLinkException($msg);
            }
        } else {
            self::setToken($token);
            $this->setHttpClient($client);
        }
    }

    /**
     * Sets the token for all future new instances
     * @param string $token The API access token, as obtained on diffbot.com/dev
     * @return void
     */
    public static function setToken($token)
    {
        self::validateToken($token);
        self::$token = $token;
    }

    private static function validateToken($token)
    {
        if (!is_string($token)) {
            throw new \InvalidArgumentException('Token is not a string.');
        }

        if (strlen($token) < 36) {
            throw new \InvalidArgumentException('Token "' . $token . '" is too short, and thus invalid.');
        }

        return true;
    }

    /**
     * Returns the token that has been defined.
     * @return null|string
     */
    public function getToken()
    {
        return self::$token;
    }


    /**
     * Sets the client to be used for querying the API endpoints
     *
     * @param Client|null $client
     * @return $this
     */
    public function setHttpClient(Client $client = null)
    {
        if ($client === null) {
            $client = new Client(self::$token);
        }

        $this->client = $client->getClient();
        return $this;
    }

    /**
     * Returns either the instance of the Guzzle client that has been defined, or null
     * @return Client|null
     */
    public function getHttpClient()
    {
        return $this->client;
    }

    public function query()
    {
        try {
            $response = $this->client->post('/query', [
                'json' => [
                    'objecttype' => 'asdsdasdfgasddf',
                    'page_size' => 50,
                    'page_number' => 1,
                    'fields' => '*',
                ]
            ]);
        } catch (RequestException $e) {
            echo Psr7\Message::toString($e->getRequest());
            if ($e->hasResponse()) {
                echo Psr7\Message::toString($e->getResponse());
            }
        }
    }
}
