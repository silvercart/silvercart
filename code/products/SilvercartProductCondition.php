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
 * @subpackage Products
 */

/**
 * Definition for the condition of a product.
 *
 * @package Silvercart
 * @subpacke Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 09.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductCondition extends DataObject {
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public function singular_name() {
        if (_t('SilvercartProductCondition.SINGULARNAME')) {
            return _t('SilvercartProductCondition.SINGULARNAME');
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011 
     */
    public function plural_name() {
        if (_t('SilvercartProductCondition.PLURALNAME')) {
            return _t('SilvercartProductCondition.PLURALNAME');
        } else {
            return parent::plural_name();
        }   
    }
    
    /**
     * Attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public static $db = array(
        'Title' => 'VarChar(255)'
    );
    
    /**
     * n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public static $has_many = array(
        'SilvercartProducts' => 'SilvercartProduct'
    );
    
    /**
     * Returns a string with HTML Code for a selector box that lets the user
     * choose a product condition.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 10.08.2011
     */
    public static function getDropdownFieldOptionSet() {
        $productConditionMap    = array();
        $productConditions      = DataObject::get(
            'SilvercartProductCondition'
        );
        
        if ($productConditions) {
            $productConditionMap = $productConditions->map('ID', 'Title');
        }
        
        return $productConditionMap;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 09.08.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title' => _t('SilvercartProductCondition.TITLE'),
                'SilvercartProducts' => _t('SilvercartProduct.PLURALNAME')
            )
        );
        
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 10.03.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title' => _t('SilvercartProductCondition.TITLE')
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}