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
 * @subpackage Base
 */

/**
 * abstract for a single position of an order
 * they are not changeable after creation and serve as a history
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 08.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartContactMessage extends DataObject {

    public static $db = array(
        'Salutation' => 'VarChar(16)',
        'FirstName' => 'VarChar(255)',
        'Surname' => 'VarChar(128)',
        'Email' => 'VarChar(255)',
        'Message' => 'Text',
    );

    public static $casting = array(
        'CreatedNice' => 'VarChar',
    );

    public static $default_sort = 'Created DESC';

    /**
     * Constructor. We localize the static variables here.
     *
     * @param array $record      array of field values
     * @param bool  $isSingleton true if this is a singleton() object
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function  __construct($record = null, $isSingleton = false) {
        self::$singular_name = _t('SilvercartContactMessage.SINGULARNAME');
        self::$plural_name = _t('SilvercartContactMessage.PLURALNAME');
        parent::__construct($record, $isSingleton);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'CreatedNice'   => _t('Silvercart.DATE'),
                    'Salutation'    => _t('SilvercartAddress.SALUTATION'),
                    'FirstName'     => _t('Member.FIRSTNAME'),
                    'Surname'       => _t('Member.SURNAME'),
                    'Email'         => _t('Member.EMAIL'),
                )
        );
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function summaryFields() {
        return array(
            'CreatedNice'   => _t('Silvercart.DATE'),
            'Salutation'    => _t('SilvercartAddress.SALUTATION'),
            'FirstName'     => _t('Member.FIRSTNAME'),
            'Surname'       => _t('Member.SURNAME'),
            'Email'         => _t('Member.EMAIL'),
        );
    }

    /**
     * returns the orders creation date formated: dd.mm.yyyy hh:mm
     *
     * @return string
     */
    public function getCreatedNice() {
        return date('d.m.Y - H:i', strtotime($this->Created));
    }

    /**
     * Disable editing for all Member types.
     *
     * @param Member $member Member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function canEdit($member = null) {
        return false;
    }

    /**
     * Send the contact message via email.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function send() {
        SilvercartShopEmail::send(
                'ContactMessage',
                Email::getAdminEmail(),
                array(
                    'FirstName' => $this->FirstName,
                    'Surname'   => $this->Surname,
                    'Email'     => $this->Email,
                    'Message'   => str_replace('\r\n', '<br/>', nl2br($this->Message)),
                )
        );
    }

}