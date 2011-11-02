<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Base
 */

/**
 * Used to register and manage SilverCartNotificationChannels.
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 09.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartTaskNotificationHandler {
    
    /**
     * Contains all registered notification channels.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.10.2011
     */
    protected $registeredNotificationChannels = array();
    
    /**
     * Registers a new notification channel.
     *
     * @param string $channelId The identificator of the channel that'll be
     *                          used by the channel partners to communicate.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.10.2011
     */
    public static function registerNotificationChannel($channelId) {
        
    }
    
    /**
     * Returns an array with the last x notifications.
     *
     * @param int $numberOfNotifications The number of notifications to return
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.10.2011
     */
    public static function getNotifications($numberOfNotifications = 10) {
        
    }
    
    /**
     * Returns an array with the last x notifications for the given channel.
     *
     * @param string $channelId             The identificator of the channel
     * @param int    $numberOfNotifications The number of notifications to return
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.10.2011
     */
    public static function getNotificationsForChannel($channelId, $numberOfNotifications = 10) {
        
    }
    
}