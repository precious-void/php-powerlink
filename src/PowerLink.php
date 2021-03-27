<?php

namespace PowerLink;

use PowerLink\Exceptions\PowerLinkException;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

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
    public function setHttpClient($client = null)
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

    /**
     * Function to resolve data from response
     */
    private function resolveResult(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $method Request Method
     * @param string $path Path
     * @param array $params Query params
     * 
     * @throws PowerLinkException When respose returned with error
     */
    private function request(string $method, string $path, array $params)
    {
        try {
            $response = $this->client->request($method, $path, [
                'json' => $params
            ]);

            $body = $this->resolveResult($response);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $body = $this->resolveResult($response);
            $msg = $body->Message;
            throw new PowerLinkException($msg);
        }

        return $body;
    }

    /**
     * Query request
     * @param array $params 
     * 
     * @return object
     */
    public function query(array $params)
    {
        return $this->request("POST", "query", $params);
    }

    /**
     * Create request
     * @param string $object_type 
     * @param array $params 
     * 
     * @return object
     */
    public function create(string $object_type, array $params)
    {
        return $this->request("POST", "record/$object_type", $params);
    }

    /**
     * Update request
     * @param string $object_type 
     * @param int $id
     * @param array $params 
     * 
     * @return object
     */
    public function update(string $object_type, int $id, array $params)
    {
        return $this->request("PUT", "record/$object_type/$id", $params);
    }

    /**
     * Delete request
     * @param string $object_type 
     * @param int $id
     * @param array $params 
     * 
     * @return object
     */
    public function delete(string $object_type, int $id, array $params)
    {
        return $this->request("DELETE", "record/$object_type/$id", $params);
    }
}
