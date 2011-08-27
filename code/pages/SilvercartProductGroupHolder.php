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
 * @subpackage Pages
 */

/**
 * to display a group of products
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 23.10.2010
 */
class SilvercartProductGroupHolder extends Page {

    public static $singular_name = "";
    public static $plural_name = "";
    public static $allowed_children = array(
        'SilvercartProductGroupPage',
        'RedirectorPage'
    );
    
    public static $icon = "silvercart/images/page_icons/product_group_holder";
    
    /**
     * Attributes.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public static $db = array(
        'productGroupsPerPage'  => 'Int'
    );
    
    /**
     * Return all fields of the backend.
     *
     * @return FieldSet Fields of the CMS
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $productGroupsPerPageField = new TextField('productGroupsPerPage', _t('SilvercartProductGroupPage.PRODUCTGROUPSPERPAGE'));
        $fields->addFieldToTab('Root.Content.Main', $productGroupsPerPageField, 'IdentifierCode');

        $this->extend('extendCMSFields', $fields);
        return $fields;
    }

    /**
     * Checks if SilvercartProductGroup has children or products.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.02.2011
     */
    public function hasProductsOrChildren() {
        if (count($this->Children()) > 0) {
            return true;
        }
        return false;
    }
}

/**
 * correlating controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductGroupHolder_Controller extends Page_Controller {

    protected $groupProducts;

    /**
     * statements to be called on oject instantiation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function init() {


        // Get Products for this group
        if (!isset($_GET['start']) ||
                !is_numeric($_GET['start']) ||
                (int) $_GET['start'] < 1) {
            $_GET['start'] = 0;
        }

        $SQL_start = (int) $_GET['start'];
        
        parent::init();
    }

    /**
     * to be called on a template
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @return DataObjectSet set of randomly choosen product objects
     * @since 23.10.2010
     */
    public function randomProducts() {
        //return $this->groupProducts;
    }

    /**
     * Builds an associative array of ProductGroups to use in GroupedDropDownFields.
     *
     * @param SiteTree $parent      Expects a SilvercartProductGroupHolder or a SilvercartProductGroupPage
     * @param boolean  $allChildren ???
     *
     * @return array
     */
    public static function getRecursiveProductGroupsForGroupedDropdownAsArray($parent = null, $allChildren = false, $withParent = false) {
        $productGroups = array();
        
        if (is_null($parent)) {
            $productGroups['']  = '';
            $parent             = self::PageByIdentifierCode('SilvercartProductGroupHolder');
        }
        
        if ($parent) {
            if ($withParent) {
                $productGroups[$parent->ID] = $parent->Title;
            }
            if ($allChildren) {
                $children = $parent->AllChildren();
            } else {
                $children = $parent->Children();
            }
            foreach ($children as $child) {
                $productGroups[$child->ID] = $child->Title;
                $subs                      = self::getRecursiveProductGroupsForGroupedDropdownAsArray($child);
                
                if (!empty ($subs)) {
                    $productGroups[_t('SilvercartProductGroupHolder.SUBGROUPS_OF','Subgroups of ') . $child->Title] = $subs;
                }
            }
        }
        
        return $productGroups;
    }

    /**
     * All viewable product groups of this group.
     *
     * @param int $numberOfProductGroups Number of product groups to display
     * 
     * @return DataObjectSet
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2011
     */
    public function getViewableChildren($numberOfProductGroups = false) {
        $viewableChildren = array();
        foreach ($this->Children() as $child) {
            if ($child->hasProductsOrChildren()) {
                $viewableChildren[] = $child;
            }
        }
        
        if ($numberOfProductGroups == false) {
            if ($this->productGroupsPerPage) {
                $pageLength = $this->productGroupsPerPage;
            } else {
                $pageLength = SilvercartConfig::ProductGroupsPerPage();
            }
        } else {
            $pageLength = $numberOfProductGroups;
        }
        
        $pageStart = $this->getSqlOffsetForProductGroups($numberOfProductGroups);
        
        $viewableChildrenSet = new DataObjectSet($viewableChildren);
        $viewableChildrenPage = $viewableChildrenSet->getRange($pageStart, $pageLength);
        $viewableChildrenPage->setPaginationGetVar('groupStart');
        $viewableChildrenPage->setPageLimits($pageStart, $pageLength, $viewableChildrenSet->Count());
        
        return $viewableChildrenPage;
    }
    
    /**
     * Return the start value for the limit part of the sql query that
     * retrieves the product group list for the current product group page.
     * 
     * @param int|bool $numberOfProductGroups The number of product groups to return
     *
     * @return int
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2011
     */
    public function getSqlOffsetForProductGroups($numberOfProductGroups = false) {
        if ($this->productGroupsPerPage) {
            $productGroupsPerPage = $this->productGroupsPerPage;
        } else {
            $productGroupsPerPage = SilvercartConfig::ProductsPerPage();
        }

        if ($numberOfProductGroups !== false) {
            $productGroupsPerPage = (int) $numberOfProductGroups;
        }
            
        if (!isset($_GET['groupStart']) ||
            !is_numeric($_GET['groupStart']) ||
            (int)$_GET['groupStart'] < 1) {

            if (isset($_GET['groupOffset'])) {
                // --------------------------------------------------------
                // Use offset for getting the current item rage
                // --------------------------------------------------------
                $offset = (int) $_GET['groupOffset'];

                if ($offset > 0) {
                    $offset -= 1;
                }

                // Prevent too high values
                if ($offset > 999999) {
                    $offset = 0;
                }

                $SQL_start = $offset * $productGroupsPerPage;
            } else {
                // --------------------------------------------------------
                // Use item number for getting the current item range
                // --------------------------------------------------------
                $SQL_start = 0;
            }
        } else {
            $SQL_start = (int) $_GET['groupStart'];
        }
        
        return $SQL_start;
    }
}
