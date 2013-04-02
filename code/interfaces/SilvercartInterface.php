<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Interfaces
 */

/**
 * Base class for Interfaces. Provides some attributes and methods which are
 * common for nearly all interfaces.
 *
 * @package Silvercart
 * @subpackage Interfaces
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 24.03.2011
 * @license see license file in modules root directory
 */
class SilvercartInterface {

    /**
     * API-URL
     *
     * @var string
     */
    protected $api;

    /**
     * API-Password
     *
     * @var string
     */
    protected $password;

    /**
     * API-SessionID
     *
     * @var string
     */
    protected $sessionID;

    /**
     * API-User
     *
     * @var string
     */
    protected $user;

    /**
     * Default constructor of an interface.
     *
     * @param string $user     Username
     * @param string $password Password
     * @param string $api      API-URL
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.03.2011
     */
    public function __construct($user = null, $password = null, $api = null) {
        if (!is_null($user)) {
            $this->setUser($user);
        }
        if (!is_null($password)) {
            $this->setPassword($password);
        }
        if (!is_null($api)) {
            $this->setApi($api);
        }
    }

    /**
     * Returns the API-URL
     *
     * @return string
     */
    public function getApi() {
        return $this->api;
    }

    /**
     * Sets the API-URL
     *
     * @param string $api API-URL
     *
     * @return void
     */
    public function setApi($api) {
        $this->api = $api;
    }

    /**
     * Returns the password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Sets the password
     *
     * @param string $password Password
     *
     * @return void
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Returns the session ID
     *
     * @return string
     */
    public function getSessionID() {
        return $this->sessionID;
    }

    /**
     * Sets the session ID
     *
     * @param string $sessionID Session ID
     *
     * @return void
     */
    public function setSessionID($sessionID) {
        $this->sessionID = $sessionID;
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the username
     *
     * @param string $user Username
     *
     * @return void
     */
    public function setUser($user) {
        $this->user = $user;
    }

}