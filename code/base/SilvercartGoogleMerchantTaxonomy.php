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
 * Definition for Google merchant product groups (Froogle)
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 08.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartGoogleMerchantTaxonomy extends DataObject {
    
    /**
     * The cache key for storing the complete result set of all breadcrumbs
     * for display in HTML dropdown fields.
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public static $cacheKey = 'SilvercartGoogleMerchantTaxonomyBreadcrumbs';
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function singular_name() {
        if (_t('SilvercartGoogleMerchantTaxonomy.SINGULARNAME')) {
            return _t('SilvercartGoogleMerchantTaxonomy.SINGULARNAME');
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
     * @since 08.08.2011 
     */
    public function plural_name() {
        if (_t('SilvercartGoogleMerchantTaxonomy.PLURALNAME')) {
            return _t('SilvercartGoogleMerchantTaxonomy.PLURALNAME');
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
     * @since 08.08.2011
     */
    public static $db = array(
        'CategoryLevel1' => 'VarChar(100)',
        'CategoryLevel2' => 'VarChar(100)',
        'CategoryLevel3' => 'VarChar(100)',
        'CategoryLevel4' => 'VarChar(100)',
        'CategoryLevel5' => 'VarChar(100)',
        'CategoryLevel6' => 'VarChar(100)'
    );
    
    /**
     * Has-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public static $has_many = array(
        'SilvercartProductGroupPages' => 'SilvercartProductGroupPage'
    );
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'CategoryLevel1' => _t('SilvercartGoogleMerchantTaxonomy.LEVEL1'),
            'CategoryLevel2' => _t('SilvercartGoogleMerchantTaxonomy.LEVEL2'),
            'CategoryLevel3' => _t('SilvercartGoogleMerchantTaxonomy.LEVEL3'),
            'CategoryLevel4' => _t('SilvercartGoogleMerchantTaxonomy.LEVEL4'),
            'CategoryLevel5' => _t('SilvercartGoogleMerchantTaxonomy.LEVEL5'),
            'CategoryLevel6' => _t('SilvercartGoogleMerchantTaxonomy.LEVEL6')
        );
        
        return $summaryFields;
    }
    
    /**
     * Returns a breadcrumb string for the full path of this object.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public function BreadCrumb() {
        $breadcrumbs            = '';
        $breadcrumbSeparator    = ' > ';

        foreach ($this->db() as $dbFieldName => $dbFieldDefinition) {
            if (substr($dbFieldName, 0, 13) == 'CategoryLevel' &&
                !empty($this->$dbFieldName)) {

                if (!empty($breadcrumbs)) {
                    $breadcrumbs .= $breadcrumbSeparator;
                }
                $breadcrumbs .= $this->$dbFieldName;
            }
        }
        
        return $breadcrumbs;
    }
}