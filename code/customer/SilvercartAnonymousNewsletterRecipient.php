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
 * @subpackage Customer
 */

/**
 * This is a datastore for recipients of the newsletter that are not
 * registered or logged in as Silvercart customers.
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 22.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartAnonymousNewsletterRecipient extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.03.2011
     */
    public static $db = array(
        'Salutation' => 'Varchar(20)',
        'FirstName'  => 'VarChar(50)',
        'Surname'    => 'VarChar(50)',
        'Email'      => 'VarChar(100)'
    );

    /**
     * Add a recipient to the list.
     *
     * @param string $Salutation The salutation to use
     * @param string $FirstName  The recipients' firstname
     * @param string $Surname    The recipients' surname
     * @param string $Email      The recipients' email address
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.03.2011
     */
    public static function add($Salutation, $FirstName, $Surname, $Email) {
        if (!empty($Salutation) &&
            !empty($FirstName) &&
            !empty($Surname) &&
            !empty($Email)) {

            $anonymousRecipient = new SilvercartAnonymousNewsletterRecipient();
            $anonymousRecipient->setField('Salutation', $Salutation);
            $anonymousRecipient->setField('FirstName',  $FirstName);
            $anonymousRecipient->setField('Surname',    $Surname);
            $anonymousRecipient->setField('Email',      $Email);
            $anonymousRecipient->write();
        }
    }

    /**
     * Remove a recipient from the list by his/her email address.
     *
     * @param string $emailAddress The email address to remove
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.03.2011
     */
    public static function removeByEmailAddress($emailAddress) {
        if (!empty($emailAddress)) {
            $anonymousRecipient = DataObject::get_one(
                'SilvercartAnonymousNewsletterRecipient',
                sprintf(
                    "Email = '%s'",
                    $emailAddress
                )
            );
            if ($anonymousRecipient) {
                $anonymousRecipient->delete();
            }
        }
    }
}