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
 * @subpackage Products
 */

/**
 * Abstract for SilvercartamountUnit
 *
 * @package Silvercart
 * @subpackage Products
 * @author Ramon Kupper <rkupper@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 25.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartAmountUnit extends DataObject {

    /**
     * singular name for backend
     *
     * @var string
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.03.2011
     */
    static $singular_name = "amount unit";

    /**
     * plural name for backend
     *
     * @var string
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 29.03.2011
     */
    static $plural_name = "amount units";

    /**
     * attributes
     *
     * @var array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 29.03.2011
     */
    public static $db = array(
        'Name' => 'VarChar(50)',
        'Abbreviation' => 'VarChar(5)'
    );
    
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
        if (_t('SilvercartAmountUnit.SINGULARNAME')) {
            return _t('SilvercartAmountUnit.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
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
        if (_t('SilvercartAmountUnit.PLURALNAME')) {
            return _t('SilvercartAmountUnit.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function summaryFields() {
        return array_merge(
                parent::summaryFields(),
                array(
                    'Name' => _t('SilvercartAmountUnit.NAME'),
                    'Abbreviation' => _t('SilvercartAmountUnit.ABBREVIATION')
                )
        );
    }

    /**
     * Field labels for display in tables.
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function fieldLabels() {
        return array_merge(
                parent::fieldLabels(),
                array(
                    'Name' => _t('SilvercartAmountUnit.NAME'),
                    'Abbreviation' => _t('SilvercartAmountUnit.ABBREVIATION')
                )
        );
    }
}
