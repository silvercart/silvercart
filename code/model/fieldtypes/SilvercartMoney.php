<?php

/**
 * Copyright 2013 pixeltricks GmbH
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
 * @subpackage Model_Fieldtypes
 */

/**
 * This is an extended Money Field to modify scaffolding and add some functions.
 *
 * @package Silvercart
 * @subpackage Model_Fieldtypes
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 13.02.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartMoney extends Money implements CompositeDBField {

    /**
     * Similiar to {@link DataObject::$db},
     * holds an array of composite field names.
     * Don't include the fields "main name",
     * it will be prefixed in {@link requireField()}.
     * 
     * @param array
     */
    public static $composite_db = array(
        "Currency" => "Varchar(3)",
        "Amount" => 'Decimal(19,4)'
    );

    /**
     * Returns the amount formatted.
     *
     * @param array $options The options
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function NiceAmount($options = array()) {
        $options['display'] = Zend_Currency::NO_SYMBOL;
        return $this->Nice($options);
    }

    /**
     * Returns a CompositeField instance used as a default
     * for form scaffolding.
     *
     * Used by {@link SearchContext}, {@link ModelAdmin}, {@link DataObject::scaffoldFormFields()}
     * 
     * @param string $title Optional. Localized title of the generated instance
     * 
     * @return FormField
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function scaffoldFormField($title = null) {
        $field = new SilvercartMoneyField($this->name);
        $field->setAllowedCurrencies($this->getAllowedCurrencies());
        $field->setLocale($this->getLocale());
        return $field;
    }

    /**
     * Set the value of this field in various formats.
     * Used by {@link DataObject->getField()}, {@link DataObject->setCastedField()}
     * {@link DataObject->dbObject()} and {@link DataObject->write()}.
     * 
     * As this method is used both for initializing the field after construction,
     * and actually changing its values, it needs a {@link $markChanged}
     * parameter. 
     * 
     * @param DBField|array $value       Value to set
     * @param array         $record      Map of values loaded from the database
     * @param boolean       $markChanged Indicate wether this field should be marked changed. 
     *                                   Set to FALSE if you are initializing this field after construction, rather
     *                                   than setting a new value.
     * 
     * @return void
     */
    public function setValue($value, $record = null, $markChanged = true) {
        return parent::setValue($value, $record, $markChanged);
    }

    /**
     * Add the custom internal values to an INSERT or UPDATE
     * request passed through the ORM with {@link DataObject->write()}.
     * Fields are added in $manipulation['fields']. Please ensure
     * these fields are escaped for database insertion, as no
     * further processing happens before running the query.
     * Use {@link DBField->prepValueForDB()}.
     * Ensure to write NULL or empty values as well to allow 
     * unsetting a previously set field. Use {@link DBField->nullValue()}
     * for the appropriate type.
     * 
     * @param array &$manipulation Manipulation to write to
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function writeToManipulation(&$manipulation) {
        return parent::writeToManipulation($manipulation);
    }

    /**
     * Add all columns which are defined through {@link requireField()}
     * and {@link $composite_db}, or any additional SQL that is required
     * to get to these columns. Will mostly just write to the {@link SQLQuery->select}
     * array.
     * 
     * @param SQLQuery &$query Query to add
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function addToQuery(&$query) {
        return parent::addToQuery($query);
    }

    /**
     * Return array in the format of {@link $composite_db}.
     * Used by {@link DataObject->hasOwnDatabaseField()}.
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function compositeDatabaseFields() {
        return parent::compositeDatabaseFields();
    }

    /**
     * Determines if the field has been changed since its initialization.
     * Most likely relies on an internal flag thats changed when calling
     * {@link setValue()} or any other custom setters on the object.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function isChanged() {
        return parent::isChanged();
    }

    /**
     * Determines if any of the properties in this field have a value,
     * meaning at least one of them is not NULL.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function exists() {
        return parent::exists();
    }

}

/**
 * Extension for Money
 *
 * @package Silvercart
 * @subpackage Model_Fieldtypes_Extensions
 * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 13.02.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartMoneyExtension extends DataExtension {

    /**
     * Returns the amount formatted.
     *
     * @param array $options The options
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function NiceAmount($options = array()) {
        $options['display'] = Zend_Currency::NO_SYMBOL;
        return $this->owner->Nice($options);
    }

}