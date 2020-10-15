<?php

namespace SilverCart\View;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Trait to provide messages to render in a template.
 * 
 * @package SilverCart
 * @subpackage View
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait MessageProvider
{
    public static $MESSAGE_TYPE_ERROR   = 'error';
    public static $MESSAGE_TYPE_INFO    = 'info';
    public static $MESSAGE_TYPE_SUCCESS = 'success';
    public static $MESSAGE_TYPE_WARNING = 'warning';
    /**
     * Append the message with the given $sessionKey with the given $message.
     * 
     * @param string $sessionKey Session key
     * @param string $message    Message
     *
     * @return DBHTMLText
     */
    public function appendMessage(string $sessionKey, string $message) : DBHTMLText
    {
        $messageSeperator = Tools::config()->message_separator;
        $originalMessage = Tools::Session()->get($sessionKey);
        $newMessage      = "{$originalMessage}{$messageSeperator}{$message}";
        return $this->setMessage($sessionKey, $newMessage);
    }

    /**
     * Append the error message with the given $message.
     *
     * @param string $message Error message
     * 
     * @return object
     */
    public function appendErrorMessage(string $message) : object
    {
        return $this->appendMessage(Tools::SESSION_KEY_MESSAGE_ERROR, $message);
    }

    /**
     * Append the info message with the given $message.
     *
     * @param string $message Info message
     * 
     * @return object
     */
    public function appendInfoMessage(string $message) : object
    {
        return $this->appendMessage(Tools::SESSION_KEY_MESSAGE_INFO, $message);
    }

    /**
     * Append the success message with the given $message.
     *
     * @param string $message Success message
     * 
     * @return object
     */
    public function appendSuccessMessage(string $message) : object
    {
        return $this->appendMessage(Tools::SESSION_KEY_MESSAGE_SUCCESS, $message);
    }

    /**
     * Append the warning message with the given $message.
     *
     * @param string $message Success message
     * 
     * @return object
     */
    public function appendWarningMessage(string $message) : object
    {
        return $this->appendMessage(Tools::SESSION_KEY_MESSAGE_WARNING, $message);
    }
    
    /**
     * Get the message with the given $sessionKey out of session and delete it (from session).
     * 
     * @param string $sessionKey Session key
     *
     * @return DBHTMLText
     */
    public function getMessage(string $sessionKey) : DBHTMLText
    {
        $message = Tools::Session()->get($sessionKey);
        Tools::Session()->clear($sessionKey);
        Tools::saveSession();
        return DBHTMLText::create()->setValue($message);
    }
    
    /**
     * Get the error message out of session and delete it (from session).
     *
     * @return DBHTMLText
     */
    public function getErrorMessage() : DBHTMLText
    {
        return $this->getMessage(Tools::SESSION_KEY_MESSAGE_ERROR);
    }
    
    /**
     * Get the info message out of session and delete it (from session).
     *
     * @return DBHTMLText
     */
    public function getInfoMessage() : DBHTMLText
    {
        return $this->getMessage(Tools::SESSION_KEY_MESSAGE_INFO);
    }
    
    /**
     * Get the success message out of session and delete it (from session).
     *
     * @return DBHTMLText
     */
    public function getSuccessMessage() : DBHTMLText
    {
        return $this->getMessage(Tools::SESSION_KEY_MESSAGE_SUCCESS);
    }
    
    /**
     * Get the warning message out of session and delete it (from session).
     *
     * @return DBHTMLText
     */
    public function getWarningMessage() : DBHTMLText
    {
        return $this->getMessage(Tools::SESSION_KEY_MESSAGE_WARNING);
    }

    /**
     * Set the given $errorMessage into the session with the given $sessionKey.
     *
     * @param string $sessionKey Session key
     * @param string $message    Message
     * 
     * @return object
     */
    public function setMessage(string $sessionKey, string $message) : object
    {
        Tools::Session()->set($sessionKey, $message);
        Tools::saveSession();
        return $this;
    }

    /**
     * Set the error message into the session.
     *
     * @param string $message Error message
     * 
     * @return object
     */
    public function setErrorMessage(string $message) : object
    {
        return $this->setMessage(Tools::SESSION_KEY_MESSAGE_ERROR, $message);
    }

    /**
     * Set the info message into the session.
     *
     * @param string $message Info message
     * 
     * @return object
     */
    public function setInfoMessage(string $message) : object
    {
        return $this->setMessage(Tools::SESSION_KEY_MESSAGE_INFO, $message);
    }

    /**
     * Set the success message into the session.
     *
     * @param string $message Success message
     * 
     * @return object
     */
    public function setSuccessMessage(string $message) : object
    {
        return $this->setMessage(Tools::SESSION_KEY_MESSAGE_SUCCESS, $message);
    }

    /**
     * Set the success message into the session.
     *
     * @param string $message Success message
     * 
     * @return object
     */
    public function setWarningMessage(string $message) : object
    {
        return $this->setMessage(Tools::SESSION_KEY_MESSAGE_WARNING, $message);
    }
}