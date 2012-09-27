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
 * A contact message object. There's a storeadmin view for this object, too.
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 08.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartContactMessage extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Salutation'    => 'VarChar(16)',
        'FirstName'     => 'VarChar(255)',
        'Surname'       => 'VarChar(128)',
        'Email'         => 'VarChar(255)',
        'Message'       => 'Text',
    );

    /**
     * Casting.
     *
     * @var array
     */
    public static $casting = array(
        'CreatedNice' => 'VarChar',
    );

    /**
     * Default SQL sort statement.
     *
     * @var string
     */
    public static $default_sort = 'Created DESC';
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
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
        $fields = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'CreatedNice'   => _t('Silvercart.DATE'),
                'Salutation'    => _t('SilvercartAddress.SALUTATION'),
                'FirstName'     => _t('Member.FIRSTNAME'),
                'Surname'       => _t('Member.SURNAME'),
                'Email'         => _t('Member.EMAIL'),
                'Message'       => _t('SilvercartContactMessage.MESSAGE')
            )
        );
        
        $this->extend('updateFieldLabels', $fields);
        SilvercartPlugin::call($this, 'fieldLabels', array($fields), true);
        
        return $fields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function summaryFields() {
        $fields = array(
            'CreatedNice'   => $this->fieldLabel('CreatedNice'),
            'Salutation'    => $this->fieldLabel('Salutation'),
            'FirstName'     => $this->fieldLabel('FirstName'),
            'Surname'       => $this->fieldLabel('Surname'),
            'Email'         => $this->fieldLabel('Email'),
        );
        
        $this->extend('updateSummaryFields', $fields);
        SilvercartPlugin::call($this, 'summaryFields', array($fields), true);
            
        return $fields;
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
        $silvercartPluginCall = SilvercartPlugin::call($this, 'send');

        if (!$silvercartPluginCall) {
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
    
    /**
     * returns field value for given fieldname with stripped slashes
     *
     * @param string $field fieldname
     * 
     * @return string 
     */
    public function getField($field) {
        $parentField = parent::getField($field);
        if (!is_null($parentField)) {
            $parentField = stripcslashes($parentField);
        }
        return $parentField;
    }
   
    
}
