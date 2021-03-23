<?php

namespace PowerLink;

class PowerLink
{
    /**
     * @var string
     */
    protected $token_id;

    /**
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Constructor for PowerLink.
     *
     * @param string $token_id Token ID for authorization
     */
    public function __construct($token_id)
    {
        $this->token_id = $token_id;
    }

    /**
     * @return string
     */
    public function getTokenID()
    {
        return $this->token_id;
    }
}
