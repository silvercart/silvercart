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
 * Abstract for a range of numbers (ordernumbers, customernumbers, invoicenumbers, etc.)
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 05.04.2011
 */
class SilvercartNumberRange extends DataObject {

    /**
     * attributes od SilvercartNumberRange
     *
     * @var array
     */
    public static $db = array(
        'Title' => 'VarChar(32)',
        'Identifier' => 'VarChar(32)',
        'Prefix' => 'VarChar(32)',
        'Suffix' => 'VarChar(32)',
        'StartCount' => 'Int(16)',
        'EndCount' => 'Int(16)',
        'ActualCount' => 'Int(16)',
    );

    /**
     * default values for some attributes
     *
     * @var array
     */
    public static $defaults = array(
        'StartCount'    => '100000',
        'EndCount'      => '999999',
        'ActualCount'   => '100000',
    );

    /**
     * some virtual attributes (not in db)
     *
     * @var array
     */
    public static $casting = array(
        'ActualNumber' => 'VarChar(80)',
        'EndNumber' => 'VarChar(80)',
        'StartNumber' => 'VarChar(80)',
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
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
     * @since 13.07.2012
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
     * @since 05.04.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Title'         => _t('SilvercartNumberRange.TITLE', 'Title'),
                    'Identifier'    => _t('SilvercartNumberRange.IDENTIFIER', 'Identifier'),
                    'Prefix'        => _t('SilvercartNumberRange.PREFIX', 'Prefix'),
                    'Suffix'        => _t('SilvercartNumberRange.SUFFIX', 'Suffix'),
                    'StartCount'    => _t('SilvercartNumberRange.STARTCOUNT', 'Start'),
                    'EndCount'      => _t('SilvercartNumberRange.ENDCOUNT', 'End'),
                    'ActualCount'   => _t('SilvercartNumberRange.ACTUALCOUNT', 'Actual Count'),
                )
        );
    }
    
    /**
     * i18n for summary fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function summaryFields() {
        return array_merge(
                parent::summaryFields(),
                array(
                    'Title'         => _t('SilvercartNumberRange.TITLE', 'Title'),
                    'StartNumber'   => _t('SilvercartNumberRange.START', 'Start'),
                    'EndNumber'     => _t('SilvercartNumberRange.END', 'End'),
                    'ActualNumber'  => _t('SilvercartNumberRange.ACTUAL', 'Actual'),
                )
        );
    }

    /**
     * customizes the backends fields
     *
     * @param array $params parameters
     *
     * @return FieldList
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function getCMSFields($params = null) {
        $fields = parent::getCMSFields($params);
        if (!empty($this->Identifier)) {
            $fields->removeByName('Identifier');
            $fields->makeFieldReadonly('ActualCount');
        }
        return $fields;
    }

    /**
     * Remove permission to delete for all members.
     *
     * @param Member $member Member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function canDelete($member = null) {
        return false;
    }

    /**
     * Builds the actual number with prefix, count and suffix
     *
     * @return string
     */
    public function getActualNumber() {
        return $this->Prefix . $this->ActualCount . $this->Suffix;
    }

    /**
     * Builds the end number with prefix, count and suffix
     *
     * @return string
     */
    public function getEndNumber() {
        return $this->Prefix . $this->EndCount . $this->Suffix;
    }

    /**
     * Builds the start number with prefix, count and suffix
     *
     * @return string
     */
    public function getStartNumber() {
        return $this->Prefix . $this->StartCount . $this->Suffix;
    }

    /**
     * increments and returns the actual number.
     *
     * @return string
     */
    protected function getNewNumber() {
        $this->ActualCount++;
        $this->write();
        return $this->getActualNumber();
    }

    /**
     * reserves a new number or returns an already reserved one.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function reserveNewNumber() {
        if (!Session::get('Reserved' . $this->Identifier)) {
            Session::set('Reserved' . $this->Identifier, $this->getNewNumber());
            Session::save();
        }
        return Session::get('Reserved' . $this->Identifier);
    }

    /**
     * returns a reserverd number and deletes the number from session.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function useReservedNumber() {
        $reservedNumber = $this->reserveNewNumber();
        Session::clear('Reserved' . $this->Identifier);
        Session::save();
        return $reservedNumber;
    }

    /**
     * returns a number range by identifier.
     *
     * @param string $identifier Identifier of the number range
     *
     * @return SilvercartNumberRange
     */
    public static function getByIdentifier($identifier) {
        return SilvercartNumberRange::get()->filter('Identifier', $identifier)->first();
    }

    /**
     * reserves a new number or returns an already reserved one by identifier.
     *
     * @param string $identifier Identifier of the number range
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public static function reserveNewNumberByIdentifier($identifier) {
        $numberRange = self::getByIdentifier($identifier);
        if ($numberRange) {
            return $numberRange->reserveNewNumber();
        }
        return false;
    }

    /**
     * returns a reserverd number and deletes the number from session by identifier.
     *
     * @param string $identifier Identifier of the number range
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public static function useReservedNumberByIdentifier($identifier) {
        $numberRange = self::getByIdentifier($identifier);
        if ($numberRange) {
            return $numberRange->useReservedNumber();
        }
        return false;
    }

}