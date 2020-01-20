<?php

namespace SilverCart\API\Response;

use SilverCart\API\Client\Client;
use stdClass;

/**
 * Default response object for API requests.
 * 
 * @package SilverCart
 * @subpackage API\Response
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.10.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Response
{
    /**
     * Client of the requested response
     *
     * @var Client
     */
    protected $client = null;
    /**
     * Error code
     *
     * @var string
     */
    protected $errorCode = null;
    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage = null;
    /**
     * Response is an error?
     *
     * @var bool
     */
    protected $isError = false;
    /**
     * Body of the requested response
     *
     * @var string
     */
    protected $body = '';
    /**
     * Data of the requested response
     *
     * @var object
     */
    protected $data = null;
    
    /**
     * Returns a new instance of Response.
     * 
     * @param Client $client       The API client
     * @param string $body         The requested responses body
     * @param object $data         The requested responses data
     * @param bool   $isError      Is the response an error?
     * @param string $errorMessage Error message
     * @param string $errorCode    Error code
     * 
     * @return Response
     */
    public static function create(Client $client = null, string $body = '', object $data = null, bool $isError = false, string $errorMessage = '', string $errorCode = '') : Response
    {
        $response = new Response($client, $body, $data, $isError, $errorMessage, $errorCode);
        return $response;
    }

    /**
     * Constructor.
     * 
     * @param Client $client       The API client
     * @param string $body         The requested responses body
     * @param object $data         The requested responses data
     * @param bool   $isError      Is the response an error?
     * @param string $errorMessage Error message
     * @param string $errorCode    Error code
     * 
     * @return Response
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.02.2019
     */
    public function __construct(Client $client = null, string $body = '', object $data = null, bool $isError = false, string $errorMessage = '', string $errorCode = '')
    {
        if (is_null($client)) {
            $client = Client::create();
        }
        if (is_null($data)) {
            $data = new stdClass();
        }
        $this->setClient($client);
        $this->setBody($body);
        $this->setData($data);
        $this->setIsError($isError);
        $this->setErrorMessage($errorMessage);
        $this->setErrorCode($errorCode);
    }
    
    /**
     * Alias for self::getIsError().
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.02.2019
     */
    public function isError() : bool
    {
        return $this->getIsError();
    }

    /**
     * Returns the API client.
     * 
     * @return Client
     */
    public function getClient() : Client
    {
        if (is_null($this->client)) {
            $this->client = Client::create();
        }
        return $this->client;
    }

    /**
     * Returns the error code.
     * 
     * @return string
     */
    public function getErrorCode() : string
    {
        return $this->errorCode;
    }

    /**
     * Returns the error message.
     * 
     * @return string
     */
    public function getErrorMessage() : string
    {
        return $this->errorMessage;
    }

    /**
     * Returns whether this response is an error.
     * 
     * @return bool
     */
    public function getIsError() : bool
    {
        return $this->isError;
    }

    /**
     * Returns the body.
     * 
     * @return stdClass
     */
    public function getBody() : string
    {
        return $this->body;
    }

    /**
     * Returns the data.
     * 
     * @return object
     */
    public function getData() : object
    {
        if (is_null($this->data)) {
            $this->data = new stdClass();
        }
        return $this->data;
    }

    /**
     * Sets the API client.
     * 
     * @param Client $client API client
     * 
     * @return Response
     */
    public function setClient(Client $client) : Response
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Sets the error code.
     * 
     * @param string $errorCode Error code
     * 
     * @return Response
     */
    public function setErrorCode(string $errorCode) : Response
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * Sets the error message.
     * 
     * @param string $errorMessage Error message
     * 
     * @return Response
     */
    public function setErrorMessage(string $errorMessage) : Response
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * Sets whether this response is an error.
     * 
     * @param bool $isError Respose is error?
     * 
     * @return Response
     */
    public function setIsError(bool $isError) : Response
    {
        $this->isError = $isError;
        return $this;
    }

    /**
     * Sets the body.
     * 
     * @param string $body Body
     * 
     * @return Response
     */
    public function setBody(string $body) : Response
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Sets the data.
     * 
     * @param object $data Data
     * 
     * @return Response
     */
    public function setData(object $data) : Response
    {
        $this->data = $data;
        return $this;
    }
}