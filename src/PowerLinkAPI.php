<?php

namespace PowerLink;

abstract class KlaviyoAPI
{
    /**
     * Host 
     */
    const BASE_URL = 'https://api.powerlink.co.il/api/';

    /**
     * Request methods
     */
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    const HTTP_PUT = 'PUT';
    const HTTP_DELETE = 'DELETE';

    /**
     * Error messages
     */
    const ERROR_INVALID_API_KEY = 'Invalid TokenID.';

    /**
     * Request options
     */
    const DATA = 'data';
    const HEADERS = 'headers';
    const JSON = 'json';
    const PROPERTIES = 'properties';
    const QUERY = 'query';
    const TOKEN = 'token';
    const USER_AGENT = 'User-Agent';

    /**
     * @var string
     */
    protected $token_id;

    /**
     * @var string
     */
    protected $client;

    /**
     * Constructor method for Base class.
     *
     * @param string $token_id Token id for PowerLink account
     */
    public function __construct($token_id)
    {
        $this->token_id = $token_id;
    }


    /**
     * Make API request using HTTP client
     *
     * @param string $path Endpoint to call
     * @param array $options API params to add to request
     * @param string $method HTTP method for request
     * @param bool $isPublic to determine if public request
     * @param bool $isV1 to determine if V1 API request
     *
     * @throws KlaviyoException
     */
    private function request($method, $path, $options)
    {
        $options = $this->prepareAuthentication();

        $setopt_array = ($this->getDefaultCurlOptions($method) +
            $this->getCurlOptUrl($path, $options) +
            $this->getSpecificCurlOptions($options));

        $curl = curl_init();
        curl_setopt_array($curl, $setopt_array);

        $response = curl_exec($curl);
        $phpVersionHttpCode =  version_compare(phpversion(), '5.5.0', '>') ? CURLINFO_RESPONSE_CODE : CURLINFO_HTTP_CODE;
        $statusCode = curl_getinfo($curl, $phpVersionHttpCode);
        curl_close($curl);

        return $this->handleResponse($response, $statusCode, $isPublic);
    }


    /**
     * Handle response from API call
     */
    private function handleResponse($response, $statusCode, $isPublic)
    {
        if ($statusCode == 403) {
            throw new KlaviyoAuthenticationException(self::ERROR_INVALID_API_KEY, $statusCode);
        } else if ($statusCode == 404) {
            throw new KlaviyoResourceNotFoundException(self::ERROR_RESOURCE_DOES_NOT_EXIST, $statusCode);
        } else if ($statusCode == 429) {
            throw new KlaviyoRateLimitException(
                $this->returnRateLimit($this->decodeJsonResponse($response), $statusCode)
            );
        } else if ($statusCode != 200) {
            throw new KlaviyoApiException($this->decodeJsonResponse($response)['detail'], $statusCode);
        }

        if ($isPublic) {
            return $response;
        }

        return $this->decodeJsonResponse($response);
    }

    /**
     * Handle authentication by updating $options passed into request method
     * based on type of API request.
     *
     * @param array $params Options configuration for Request Interface
     * @param bool $isPublic Request type - public
     * @param bool $isV1 Request API version - V1
     *
     * @return array|array[]
     */
    private function prepareAuthentication($params, $isPublic, $isV1)
    {
        if ($isPublic) {
            $params = $this->publicAuth($params);
            return $params;
        }

        if ($isV1) {
            $params = $this->v1Auth($params);
            return $params;
        } else {
            $params = $this->v2Auth($params);
            return $params;
        }
    }
}
