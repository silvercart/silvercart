<?php

namespace SilverCart\API\Client;

use SilverCart\API\Response\Response;
use SilverStripe\Control\Director;
use SimpleXMLElement;

/**
 * Main handler for CURL client calls.
 *
 * @package SilverCart
 * @subpackage API\Client
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.10.2019
 * @license see license file in modules root directory
 * @copyright 2019 pixeltricks GmbH
 */
class CURLClient extends Client
{
    const HEADERS_JSON_CONTENT = [
        "Content-Type: application/json",
        "Accept: application/json",
    ];
    const HEADERS_XML_CONTENT = [
        "Content-Type: application/xml",
        "Accept: application/xml",
    ];
    /**
     * Optional list of custom headers.
     *
     * @var array
     */
    private static $headers = [];
    /**
     * Use the HTTP based authentification using the CURL parameter CURLOPT_USERPWD.
     *
     * @var bool
     */
    private static $use_curl_authentification = true;
    /**
     * Last API request string
     *
     * @var string
     */
    protected $lastRequest = '';
    /**
     * Last API request data array
     *
     * @var string
     */
    protected $lastRequestData = '';
    /**
     * Last API response
     *
     * @var string
     */
    protected $lastResponse = '';
    /**
     * Last API response CURL info
     *
     * @var array
     */
    protected $lastResponseInfo = [];
    /**
     * Last API response CURL error
     *
     * @var string
     */
    protected $lastResponseError = '';
    /**
     * Last API response CURL errno
     *
     * @var string
     */
    protected $lastResponseErrno = '';

    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                       API Connection Section                         **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Sends an API GET request.
     * 
     * @param string $target                API target endpoint
     * @param array  $additionalCURLOptions Additional CURL options
     * 
     * @return Response
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2018
     */
    protected function sendGetRequest(string $target, array $additionalCURLOptions = []) : Response
    {
        return $this->sendRequest($target, 'GET', [], '', $additionalCURLOptions);
    }
    
    /**
     * Sends an API POST request.
     * 
     * @param string $target                API target endpoint
     * @param array  $postFields            Post fields to submit
     * @param string $requestString         Request string to use instead of $postFields
     * @param array  $additionalCURLOptions Additional CURL options
     * 
     * @return Response
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2018
     */
    protected function sendPostRequest(string $target, array $postFields = [], string $requestString = '', array $additionalCURLOptions = []) : Response
    {
        return $this->sendRequest($target, 'POST', $postFields, $requestString, $additionalCURLOptions);
    }
    
    /**
     * Sends an API PUT request.
     * 
     * @param string $target                API target endpoint
     * @param array  $postFields            Post fields to submit
     * @param string $requestString         Request string to use instead of $postFields
     * @param array  $additionalCURLOptions Additional CURL options
     * 
     * @return Response
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2018
     */
    protected function sendPutRequest(string $target, array $postFields = [], string $requestString = '', array $additionalCURLOptions = []) : Response
    {
        return $this->sendRequest($target, 'PUT', $postFields, $requestString, $additionalCURLOptions);
    }
    
    /**
     * Sends an API DELETE request.
     * 
     * @param string $target                API target endpoint
     * @param array  $postFields            Post fields to submit
     * @param string $requestString         Request string to use instead of $postFields
     * @param array  $additionalCURLOptions Additional CURL options
     * 
     * @return Response
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2018
     */
    protected function sendDeleteRequest(string $target, array $postFields = [], string $requestString = '', array $additionalCURLOptions = []) : Response
    {
        return $this->sendRequest($target, 'DELETE', $postFields, $requestString, $additionalCURLOptions);
    }
    
    /**
     * Sends an API request.
     * 
     * @param string $target                API target endpoint
     * @param string $method                HTTP method (GET/POST/PUT/DELETE)
     * @param array  $postFields            Post fields to submit
     * @param string $requestString         Request string to use instead of $postFields
     * @param array  $additionalCURLOptions Additional CURL options
     * 
     * @return Response
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2018
     */
    protected function sendRequest(string $target, string $method = "GET", array $postFields = [], string $requestString = '', array $additionalCURLOptions = []) : Response
    {
        if (!self::isEnabled()) {
            $isError      = true;
            $errorMessage = "Error: API is not enabled. Please make sure the API URL, username and password are set properly in the environment configuration.";
            $errorCode    = 'SND-0001';
            return Response::create($this, '', null, $isError, $errorMessage, $errorCode);
        }
        if (empty($postFields)
         && !empty($requestString)
        ) {
            $postFields = $requestString;
        }
        $url     = "{$this->getAPIURL()}{$target}";
        $ch      = curl_init($url);
        $headers = $this->getHeaders();
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->getAPITimeout());
        if ($this->useCURLAuthentification()) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->getAPIUsername() . ":" . $this->getAPIPassword());
        }
        if ($this->useAPIProxy()) {
            curl_setopt($ch, CURLOPT_PROXY, $this->getAPIProxyURL());
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_PUT, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($additionalCURLOptions)) {
            foreach ($additionalCURLOptions as $option => $value) {
                curl_setopt($ch, $option, $value);
            }
        }
        if (Director::isDev()
         || $this->disableSSLVerification()
        ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        $response = curl_exec($ch);
        $info     = curl_getinfo($ch);
        $error    = curl_error($ch);
        $errno    = curl_errno($ch);
        curl_close($ch);
        $this->setLastRequest($url)
                ->setLastResponse($response)
                ->setLastResponseInfo($info)
                ->setLastResponseError($error)
                ->setLastResponseErrno($errno);
        return $this->handleResponse($response);
    }
    
    /**
     * Default response handling.
     * 
     * @param string $response Response
     * 
     * @return Response
     */
    protected function handleResponse(string $response = null) : Response
    {
        $data         = null;
        $isError      = false;
        $errorMessage = '';
        $errorCode    = '';
        $headers      = $this->getHeaders();
        $diffJSON     = array_diff($headers, self::HEADERS_JSON_CONTENT);
        $diffXML      = array_diff($headers, self::HEADERS_XML_CONTENT);
        if (!empty($response)) {
            if (empty($diffJSON)) {
                $data = json_decode($response);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $isError      = true;
                    $errorMessage = "Error: Response format couldn't be parsed as JSON.";
                    $errorCode    = 'JSON-0001';
                } elseif (is_object($data)
                       && property_exists($data, 'error')
                       && property_exists($data, 'message')
                ) {
                    $isError      = true;
                    $errorMessage = $data->message;
                    $errorCode    = $data->error;
                }
            } elseif (empty($diffXML)) {
                $data = new SimpleXMLElement($response);
            }
        }
        if (!$isError
         && is_null($data)
        ) {
            $isError      = true;
            $errorMessage = "Error: Could not handle response.";
            $errorCode    = 'HNDL-0001';
        }
        if (!empty($errorMessage)) {
            if (!empty($errorCode)) {
                $this->addError("{$errorCode}: {$errorMessage}");
            } else {
                $this->addError($errorMessage);
            }
        }
        return Response::create($this, $response, $data, $isError, $errorMessage, $errorCode);
    }
    
    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                         API Helper Section                           **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Returns the default headers.
     * 
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->config()->headers;
    }
    
    /**
     * Returns whether to use the HTTP based CURL authentification.
     * 
     * @return bool
     */
    public function getUseCURLAuthentification() : bool
    {
        return $this->config()->use_curl_authentification;
    }
    
    /**
     * Returns whether to use the HTTP based CURL authentification.
     * Alias for @see $this->getUseCurlAuthentification().
     * 
     * @return bool
     */
    public function useCURLAuthentification() : bool
    {
        return $this->getUseCURLAuthentification();
    }
    
    /**
     * Returns the last request.
     * 
     * @return string
     */
    public function getLastRequest() : string
    {
        return $this->lastRequest;
    }

    /**
     * Returns the last response.
     * 
     * @return string
     */
    public function getLastResponse() : string
    {
        return $this->lastResponse;
    }

    /**
     * Returns the last response CURL info.
     * 
     * @return array
     */
    public function getLastResponseInfo() : array
    {
        return $this->lastResponseInfo;
    }

    /**
     * Returns the last response CURL error.
     * 
     * @return string
     */
    public function getLastResponseError() : string
    {
        return $this->lastResponseError;
    }

    /**
     * Returns the last response CURL errno.
     * 
     * @return string
     */
    public function getLastResponseErrno() : string
    {
        return $this->lastResponseErrno;
    }

    /**
     * Sets the last request.
     * 
     * @param string $lastRequest Last request
     * 
     * @return $this
     */
    public function setLastRequest($lastRequest) : Client
    {
        $this->lastRequest = $lastRequest;
        return $this;
    }

    /**
     * Sets the last response.
     * 
     * @param string $lastResponse Last response
     * 
     * @return $this
     */
    public function setLastResponse($lastResponse) : Client
    {
        $this->lastResponse = $lastResponse;
        return $this;
    }

    /**
     * Sets the last response CURL info.
     * 
     * @param array $lastResponseInfo Last response CURL info
     * 
     * @return $this
     */
    public function setLastResponseInfo($lastResponseInfo) : Client
    {
        $this->lastResponseInfo = $lastResponseInfo;
        return $this;
    }

    /**
     * Sets the last response CURL error.
     * 
     * @param string $lastResponseError Last response CURL error
     * 
     * @return $this
     */
    public function setLastResponseError($lastResponseError) : Client
    {
        $this->lastResponseError = $lastResponseError;
        return $this;
    }

    /**
     * Sets the last response CURL errno.
     * 
     * @param string $lastResponseErrno Last response CURL errno
     * 
     * @return $this
     */
    public function setLastResponseErrno($lastResponseErrno) : Client
    {
        $this->lastResponseErrno = $lastResponseErrno;
        return $this;
    }
}