<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright 2010 pixeltricks GmbH
 * @since 24.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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