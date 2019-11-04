<?php

namespace SilverCart\API\Client;

use SoapClient as PHPSoapClient;

/**
 * Main handler for SOAP client calls.
 *
 * @package SilverCart
 * @subpackage API\Client
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.01.2019
 * @license see license file in modules root directory
 * @copyright 2019 pixeltricks GmbH
 */
class SOAPClient extends Client
{
    /**
     * The SOAP server location. Optional value to set if the accessible SOAP
     * server location differs from the WSDL target namespace.
     *
     * @var string
     */
    private static $soap_server_force_location = '';
    /**
     * SOAP clients.
     *
     * @var PHPSoapClient[] 
     */
    private $soapClients = [];

    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                       API Connection Section                         **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Returns the SOAP client.
     * 
     * @param string $url URL to WSDL.
     * 
     * @return PHPSoapClient
     */
    public function getSoapClient(string $url = null) : PHPSoapClient
    {
        if (is_null($url)) {
            $url = $this->getAPIURL();
        }
        if (!array_key_exists($url, $this->soapClients)) {
            $options = [
                'login'      => $this->getAPIUsername(),
                'password'   => $this->getAPIPassword(),
                'trace'      => 1,
                'exception'  => 0,
                'features'   => SOAP_SINGLE_ELEMENT_ARRAYS,
                'cache_wsdl' => WSDL_CACHE_NONE,
            ];
            $forceLocation = $this->soapServerForceLocation();
            if (!is_null($forceLocation)
             && !empty($forceLocation)
            ) {
                $options['location'] = $forceLocation;
            }
            if ($this->disableSSLVerification() === true) {
                $context = stream_context_create([
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ],
                ]);
                $options['stream_context'] = $context;
            }
            $this->soapClients[$url] = new PHPSoapClient("{$url}?wsdl", $options);
        }
        return $this->soapClients[$url];
    }

    /**
     * Sets the SOAP client.
     * 
     * @param PHPSoapClient $soapClient SOAP client
     * 
     * @return Client
     */
    public function setSoapClient(PHPSoapClient $soapClient) : Client
    {
        $this->soapClient = $soapClient;
        return $this;
    }
    
    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                         API Helper Section                           **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Returns the optional forced soap server location.
     * Alias for @see $this->getSoapServerForceLocation().
     * 
     * @return string
     */
    public function soapServerForceLocation() : string
    {
        return $this->getSoapServerForceLocation();
    }
    
    /**
     * Returns the optional forced soap server location.
     * 
     * @return string
     */
    public function getSoapServerForceLocation() : string
    {
        return (string) $this->config()->soap_server_force_location;
    }
    
    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                           Logging Section                            **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/
    
    /**
     * Triggers an error.
     * 
     * @param string        $message    Error message
     * @param string        $code       Error code
     * @param PHPSoapClient $soapClient SOAP Client
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.12.2014
     */
    protected function triggerError(string $message, string $code, PHPSoapClient $soapClient = null) : void
    {
        $this->log("An error occurred!", 'ERROR');
        $this->log("Code: {$code}", 'ERROR');
        $this->log("Message: {$message}", 'ERROR');
        if (!is_null($soapClient)) {
            $this->log('', 'ERROR');
            $this->log((string) $soapClient->__getLastRequest(), 'ERROR-REQUEST');
            $this->log('', 'ERROR');
            $this->log((string) $soapClient->__getLastResponse(), 'ERROR-RESPONSE');
            $this->log('', 'ERROR');
        }
    }
}