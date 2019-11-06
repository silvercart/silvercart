<?php

namespace SilverCart\API\Client;

use ReflectionClass;
use SilverCart\API\Response\Response;
use SilverCart\Dev\Tools;
use SilverCart\Model\ShopEmail;

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
class Client
{
    use \SilverStripe\Core\Config\Configurable;
    use \SilverStripe\Core\Injector\Injectable;
    /**
     * API target URL.
     *
     * @var string
     */
    private static $api_url = '';
    /**
     * API proxy URL (optional).
     *
     * @var string
     */
    private static $api_proxy_url = '';
    /**
     * API user name.
     *
     * @var string
     */
    private static $api_username = '';
    /**
     * API password.
     *
     * @var string
     */
    private static $api_password = '';
    /**
     * API error recipient email address.
     *
     * @var string
     */
    private static $api_error_recipient = '';
    /**
     * API timeout in seconds.
     * Caution: may not work for every kind of API.
     *
     * @var int
     */
    private static $api_timeout = 30;
    /**
     * Set to true to disable the server SSL certificate verification.
     * Caution: Avoid to go for this property in production environments.
     *
     * @var bool
     */
    private static $disable_ssl_verification = false;
    /**
     * List of occurred errors responded by the API
     *
     * @var array
     */
    protected $errorList = [];
    /**
     * File name to use for logging.
     *
     * @var string
     */
    protected $logFileName = null;
    
    /**
     * Constructor.
     * Calls the parent constructor and sets the log file name.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.02.2019
     */
    public function __construct()
    {
        $reflection = new ReflectionClass($this);
        $shortName  = $reflection->getShortName();
        $this->setLogFileName("APIClient.{$shortName}");
    }

    /**
     * Returns whether the API is enabled.
     * Alias for self::isEnabled().
     * 
     * @return bool
     */
    public function isActive() : bool
    {
        return self::isEnabled();
    }
    
    /**
     * Returns whether the API is enabled.
     * 
     * @return bool
     */
    public static function isEnabled() : bool
    {
        $client = static::create();
        $url    = $client->getAPIURL();
        $user   = $client->getAPIUsername();
        $pass   = $client->getAPIPassword();
        return !(empty($url) || empty($user) || empty($pass));
    }
    
    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                         API Helper Section                           **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Returns whether to disable the SSL verification.
     * Alias for @see $this->getDisableSSLVerification().
     * 
     * @return bool
     */
    public function disableSSLVerification() : bool
    {
        return $this->getDisableSSLVerification();
    }
    
    /**
     * Returns whether to use a proxy.
     * 
     * @return bool
     */
    public function getAPIProxyURL() : string
    {
        return $this->config()->api_proxy_url;
    }
    
    /**
     * Returns whether to disable the SSL verification.
     * 
     * @return bool
     */
    public function getDisableSSLVerification() : bool
    {
        return $this->config()->disable_ssl_verification;
    }
    
    /**
     * Returns whether to use a proxy.
     * 
     * @return bool
     */
    public function useAPIProxy() : bool
    {
        return !empty($this->config()->api_proxy_url);
    }
    
    /**
     * Returns whether one or more errors occurred.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2018
     */
    public function errorOccurred() : bool
    {
        return count($this->errorList) > 0;
    }
    
    /**
     * Sends an error notification email.
     * 
     * @param string $subject Subject
     * @param string $content Content
     * 
     * @return Client
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2019
     */
    public function sendErrorNotificationEmail(string $subject = 'An error occurred while trying to deal with the API', string $content = '') : Client
    {
        $recipient = $this->getAPIErrorRecipient();
        if (!empty($recipient)
         && $this->errorOccurred()
        ) {
            if (!empty($content)) {
                $content .= PHP_EOL;
                $content .= PHP_EOL;
                $content .= PHP_EOL;
            }
            foreach ($this->getErrorList() as $error) {
                $content .= $error . PHP_EOL;
            }
            ShopEmail::send_email($recipient, $subject, $content);
        }
        return $this;
    }

    /**
     * Adds a single error message to the list of occurred errors responded by 
     * the API.
     * 
     * @param string $error Error message
     * 
     * @return $this
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2018
     */
    public function addError(string $error) : Client
    {
        $this->errorList[] = $error;
        return $this;
    }

    /**
     * Adds an error list to the list of occurred errors responded by the API.
     * 
     * @param array $errorList List of errors
     * 
     * @return $this
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2018
     */
    public function addErrorList(array $errorList) : array
    {
        $this->errorList = array_merge(
                $this->errorList,
                $errorList
        );
        return $this;
    }

    /**
     * Returns the list of occurred errors responded by the API.
     * 
     * @return array
     */
    public function getErrorList() : array
    {
        return $this->errorList;
    }

    /**
     * Sets the list of occurred errors responded by the API.
     * 
     * @param array $errorList List of errors
     * 
     * @return $this
     */
    public function setErrorList($errorList) : Client
    {
        $this->errorList = $errorList;
        return $this;
    }
    
    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                       API Properties Section                         **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Returns the API URL.
     * 
     * @return string
     */
    public function getAPIURL() : string
    {
        return $this->config()->api_url;
    }
    
    /**
     * Returns the API user name.
     * 
     * @return string
     */
    public function getAPIUsername() : string
    {
        return $this->config()->api_username;
    }
    
    /**
     * Returns the API password.
     * 
     * @return string
     */
    public function getAPIPassword() : string
    {
        return $this->config()->api_password;
    }
    
    /**
     * Returns the API password.
     * 
     * @return string
     */
    public function getAPIErrorRecipient() : string
    {
        $recipient = $this->config()->api_error_recipient;
        if (empty($recipient)) {
            $recipient = '';
        }
        return $recipient;
    }
    
    /**
     * Returns the API timeout.
     * 
     * @return int
     */
    public function getAPITimeout() : int
    {
        return $this->config()->api_timeout;
    }
    
    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                          Response Section                            **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Returns the default error response object.
     * 
     * @param string $message Error message
     * @param string $code    Error code
     * 
     * @return Response
     */
    protected function getErrorResponse(string $message, string $code) : Response
    {
        return Response::create($this, '', null, true, $message, $code);
    }
    
    /**
     * Returns the default success response object.
     * 
     * @param string $body Response body string
     * @param object $data Response data object
     * 
     * @return Response
     */
    protected function getSuccessResponse(string $body = '', object $data = null) : Response
    {
        return Response::create($this, $body, $data);
    }
    
    /**
     * Returns the default unknown error response object.
     * 
     * @return Response
     */
    protected function getUnknownErrorResponse() : Response
    {
        return $this->getErrorResponse('UnknownError', '-1');
    }
    
    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                           Logging Section                            **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Returns the log file name.
     * 
     * @return string
     */
    public function getLogFileName() : string
    {
        if (is_null($this->logFileName)) {
            $this->logFileName = self::class;
        }
        return $this->logFileName;
    }
    
    /**
     * Sets the log file name.
     * 
     * @param string $logFileName Log file name
     * 
     * @return Client
     */
    public function setLogFileName(string $logFileName) : Client
    {
        $this->logFileName = $logFileName;
        return $this;
    }
    
    /**
     * Logs the given message.
     * 
     * @param string $message Message to log
     * @param string $type    Type of message
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.02.2019
     */
    protected function log(string $message, string $type = 'notice') : void
    {
        Tools::Log(strtoupper($type), $message, $this->getLogFileName());
    }
}