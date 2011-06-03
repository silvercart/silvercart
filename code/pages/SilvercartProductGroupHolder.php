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

        $this->groupProducts = SilvercartProduct::getRandomProducts(5);

        // Initialise formobjects
        $templateProductList = new DataObjectSet();
        $productIdx = 0;
        if ($this->groupProducts) {
            $productAddCartForm = $this->getCartFormName();
            foreach ($this->groupProducts as $product) {
                $this->registerCustomHtmlForm('ProductAddCartForm' . $productIdx, new $productAddCartForm($this, array('productID' => $product->ID)));
                $product->setField('Link', $product->Link());
                $product->productAddCartForm = $this->InsertCustomHtmlForm(
                                'ProductAddCartForm' . $productIdx,
                                array(
                                    $product
                                )
                );
                $productIdx++;
            }
        }

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
        return $this->groupProducts;
    }

    /**
     * Builds an associative array of ProductGroups to use in GroupedDropDownFields.
     *
     * @param SiteTree $parent Expects a SilvercartProductGroupHolder or a SilvercartProductGroupPage
     *
     * @return array
     */
    public static function getRecursiveProductGroupsForGroupedDropdownAsArray($parent = null) {
        $productGroups = array();
        
        if (is_null($parent)) {
            $productGroups['']  = '';
            $parent             = self::PageByIdentifierCode('SilvercartProductGroupHolder');
        }
        
        if ($parent) {
            foreach ($parent->Children() as $child) {
                $productGroups[$child->ID] = $child->Title;
                $subs                      = self::getRecursiveProductGroupsForGroupedDropdownAsArray($child);
                
                if (!empty ($subs)) {
                    $productGroups[_t('SilvercartProductGroupHolder.SUBGROUPS_OF','Subgroups of ') . $child->Title] = $subs;
                }
            }
        }
        
        return $productGroups;
    }

}
