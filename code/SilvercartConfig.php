<?php
/*
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
 */

/**
 * The class SilvercartConfig is the Handler for central configurations of
 * SilverCart.
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 23.02.2011
 * @license LGPL
 */
class SilvercartConfig extends DataObject {

    public static $singular_name = "General configuration";
    public static $plural_name = "General configurations";

    public static $db = array(
        'DefaultCurrency' => 'VarChar(16)',
        'EmailSender' => 'VarChar(255)',
        'GlobalEmailRecipient' => 'VarChar(255)',
    );

    /**
     * Define all required configuration fields in this array. The given fields
     * will be handled in self::Check().
     *
     * @var array
     */
    public static $required_configuration_fields = array(
        'EmailSender'
    );

    public static $defaultLayoutEnabled = true;

    public static $defaultCurrency = null;

    public static $emailSender = null;

    public static $globalEmailRecipient = null;

    /**
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function  __construct($record = null, $isSingleton = false) {
        self::$singular_name = _t('SilvercartConfig.SINGULARNAME', 'General configuration');
        self::$plural_name = _t('SilvercartConfig.PLURALNAME', 'General configurations');
        parent::__construct($record, $isSingleton);
    }

    /**
     * Add notes to the CMS fields.
     *
     * @param array $params custom params
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function getCMSFields($params = null) {
        $CMSFields = parent::getCMSFields($params);

        $CMSFields->addFieldToTab('Root.Main', new LabelField('ForEmailSender',  _t('SilvercartConfig.EMAILSENDER_INFO')), 'GlobalEmailRecipient');
        $CMSFields->addFieldToTab('Root.Main', new LabelField('ForGlobalEmailRecipient', _t('SilvercartConfig.GLOBALEMAILRECIPIENT_INFO')));

        return $CMSFields;
    }

    /**
     * Sets the translations of SilvercartConfigs field labels.
     *
     * @param bool $includerelations Include relations or not
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function  fieldLabels($includerelations = true) {
        $fieldLabels = parent::fieldLabels($includerelations);
        $fieldLabels['DefaultCurrency'] = _t('SilvercartConfig.DEFAULTCURRENCY', 'Default currency');
        $fieldLabels['EmailSender'] = _t('SilvercartConfig.EMAILSENDER', 'Email sender');
        $fieldLabels['GlobalEmailRecipient'] = _t('SilvercartConfig.GLOBALEMAILRECIPIENT', 'Global email recipient');
        return $fieldLabels;
    }

    /**
     * Sets the translations of SilvercartConfigs summary fields.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function  summaryFields() {
        $summaryFields = parent::summaryFields();
        $summaryFields['DefaultCurrency'] = _t('SilvercartConfig.DEFAULTCURRENCY', 'Default currency');
        $summaryFields['EmailSender'] = _t('SilvercartConfig.EMAILSENDER', 'Email sender');
        $summaryFields['GlobalEmailRecipient'] = _t('SilvercartConfig.GLOBALEMAILRECIPIENT', 'Global email recipient');
        return $summaryFields;
    }

    /**
     * Checks whether there is an existing SilvercartConfig or not before writing.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (DataObject::get_one('SilvercartConfig')) {
            if (DataObject::get_one('SilvercartConfig')->ID !== $this->ID) {
                // is there is an existent SilvercartConfig, do not write another.
                $this->record = array();
            }
        }
    }

    /**
     * This method checks the required configuration. If there is any missing
     * configuration, an error will be displayed.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function Check() {
        if (is_null(self::$required_configuration_fields)) {
            return true;
        }
        if (empty (self::$required_configuration_fields)) {
            return true;
        }
        if (is_array(self::$required_configuration_fields)) {
            $config = DataObject::get_one('SilvercartConfig');
            foreach (self::$required_configuration_fields as $requiredField) {
                if (empty ($config->$requiredField)) {
                    $errorMessage = sprintf(_t('SilvercartConfig.ERROR_MESSAGE', 'Required configuration for "%s" is missing. Please <a href="/admin/silvercart-configuration/">log in</a> and choose "SilverCart Configuration -> general configuration" to edit the missing field.'), _t('SilvercartConfig.' . strtoupper($requiredField)));
                    $elements = array(
                        'ErrorMessage' => $errorMessage,
                    );
                    $output = Controller::curr()->customise($elements)->renderWith(
                        array(
                            'SilvercartErrorPage',
                            'Page'
                        )
                    );
                    print $output;
                    exit();
                }
            }
        }
        return true;
    }

    /**
     * Returns the configured default currency.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function DefaultCurrency() {
        if (is_null(self::$defaultCurrency)) {
            self::$defaultCurrency = DataObject::get_one('SilvercartConfig')->DefaultCurrency;
        }
        return self::$defaultCurrency;
    }

    /**
     * Returns the configured email sender.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function EmailSender() {
        if (is_null(self::$emailSender)) {
            self::$emailSender = DataObject::get_one('SilvercartConfig')->EmailSender;
        }
        return self::$emailSender;
    }

    /**
     * Returns the configured default global email recipient.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function GlobalEmailRecipient() {
        if (is_null(self::$globalEmailRecipient)) {
            self::$globalEmailRecipient = DataObject::get_one('SilvercartConfig')->GlobalEmailRecipient;
        }
        return self::$globalEmailRecipient;
    }
    
    // Put foreign configurations here
    
    /**
     * Disables the base layout of SilverCart. This is important if the layout
     * stands in conflict with your projects default layout.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function disableDefaultLayout() {
        self::$defaultLayoutEnabled = false;
    }

    /**
     * Returns whether the base layout is enabled or not.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function DefaultLayoutEnabled() {
        return self::$defaultLayoutEnabled;
    }

    /**
     * enables the creation of test data on /dev/build
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2011
     */
    public static function enableTestData() {
        SilvercartRequireDefaultRecords::enableTestData();
    }

    /**
     * disables the creation of test data on /dev/build. This is set by default,
     * so you do not have to disable creation of test data if it was not enabled
     * before.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2011
     */
    public static function disableTestData() {
        SilvercartRequireDefaultRecords::disableTestData();
    }

    /**
     * adds a new group view type for product lists to the handler.
     *
     * @param string $groupView the class name of the group view to add
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function addGroupView($groupView) {
        SilvercartGroupViewHandler::addGroupView($groupView);
    }

    /**
     * adds a new group view type for product group lists to the handler.
     *
     * @param string $groupHolderView the class name of the group view to add
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function addGroupHolderView($groupHolderView) {
        SilvercartGroupViewHandler::addGroupHolderView($groupHolderView);
    }

    /**
     * removes a group view for product lists from the handler
     *
     * @param string $groupView the class name of the group view to remove
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function removeGroupView($groupView) {
        SilvercartGroupViewHandler::removeGroupView($groupView);
    }

    /**
     * removes a group view for product group lists from the handler
     *
     * @param string $groupHolderView the class name of the group view to remove
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function removeGroupHolderView($groupHolderView) {
        SilvercartGroupViewHandler::removeGroupHolderView($groupHolderView);
    }

    /**
     * set the group view to use by default for product lists
     *
     * @param string $defaultGroupView the class name of the group view to use by default
     *
     * @return void
     */
    public static function setDefaultGroupView($defaultGroupView = null) {
        SilvercartGroupViewHandler::setDefaultGroupView($defaultGroupView);
    }

    /**
     * set the group view to use by default for product group lists
     *
     * @param string $defaultGroupHolderView the class name of the group view to use by default
     *
     * @return void
     */
    public static function setDefaultGroupHolderView($defaultGroupHolderView = null) {
        SilvercartGroupViewHandler::setDefaultGroupHolderView($defaultGroupHolderView);
    }
}
