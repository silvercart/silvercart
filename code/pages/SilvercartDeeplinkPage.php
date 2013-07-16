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
 * @subpackage Pages
 */

/**
 * Redirects to a product which is identified by url parameters product attribute
 * and attribute value;
 * If the result is ambiguous the set of products is shown.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 29.07.2011
 * @copyright 2013 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDeeplinkPage extends Page {
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/metanavigation_page";
    
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
}

/**
 * corresponding controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.05.2013
 * @copyright 2013 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDeeplinkPage_Controller extends Page_Controller {
    
    /**
     *
     * @var DataObjectSet the products that match the attribute/value in the url params 
     */
    protected $products = null;
    
    /**
     * SQL limit start value
     *
     * @var int
     */
    protected $SQL_start = 0;

    /**
     * controller method called before anything else happens
     * 
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.11.2012
     */
    public function init() {
        if (isset($_GET['start'])) {
            $this->SQL_start = (int)$_GET['start'];
        }
        $formActionLink  = $this->getRelativeDeepLinkForPartiallyMatchingProducts();
        $formActionLink .= 'customHtmlFormSubmit';
        
        //fill $products if there is more than one result
        if (!$this->getExactlyMatchingProduct() && $this->getPartiallyMatchingProducts()) {
            $this->products         = $this->getPartiallyMatchingProducts();
            $backLink               = $this->Link() . $this->getRelativeDeepLinkForPartiallyMatchingProducts() . "?start=" . $this->SQL_start;
            $productAddCartFormName = $this->getCartFormName();
            foreach ($this->products as $product) {
                $addCartForm = new $productAddCartFormName(
                        $this,
                        array(
                            'productID' => $product->ID,
                            'backLink'  => $backLink,
                        ),
                        array(
                            'submitAction' => $formActionLink
                        )
                );
                $this->registerCustomHtmlForm('ProductAddCartForm' . $product->ID, $addCartForm);
            }
        }
        parent::init();
    }
    
    /**
     * Checks whether the given group view is allowed to render for this group
     *
     * @param string $groupView GroupView code
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.05.2013
     */
    public function isGroupViewAllowed($groupView) {
        return true;
    }

    /**
     * Redefine the rules to interpret the url parameters as strings:
     * -The first parameter must be interpreted as a product attribute
     * -The second parameter must be interpreted as the attributes value
     * 
     * @param SS_HTTPRequest $request the HTTP request
     * 
     * @return string|void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.7.2011
     */
    public function handleAction(SS_HTTPRequest $request) {
        if ($this->getDeeplink()&& isset ($this->urlParams['ID'])) {
            if ($this->getExactlyMatchingProduct()) {
                return Director::redirect($this->getExactlyMatchingProduct()->Link(), 301);
            } elseif ($this->getPartiallyMatchingProducts()) {
                
                if ($this->urlParams['OtherID'] == 'customHtmlFormSubmit') {
                    $this->customHtmlFormSubmit($request);
                }
                
                return $this->renderWith(array('SilvercartDeeplinkPage', 'Page'));
            } 
        } elseif ($this->urlParams['Action'] == 'customHtmlFormSubmit') {
            $this->customHtmlFormSubmit($request);
            return $this->renderWith(array('SilvercartSearchResultsPage', 'Page'));
        }
        
        return Director::redirect(DataObject::get_one('ErrorPage', '`ErrorCode` = 404')->Link());
    } 


    /**
     * Return a set of Products to be rendered in the template
     * Only filled if the result does not point to one product only
     * 
     * @return DataObjectSet
     */
    public function getProducts() {
        return $this->products;
    }
    
    /**
     * Getter for a Deeplink object determined by url param Action
     * A param ID must be there too.
     * 
     * @return SilvercartDeeplink
     */
    public function getDeeplink() {
        if (isset ($this->urlParams['Action'])) {
            $filter = sprintf(
                    "`isActive` = 1 AND `productAttribute` = '%s'",
                    $this->getDeeplinkAttributeName()
            );
            $deeplinkObject = DataObject::get_one('SilvercartDeeplink', $filter);
            return $deeplinkObject;
        }
        return false;
    }
    
    /**
     * Returns the result of an exact match search with the url parameters Action
     * and ID
     * 
     * @return SilvercartProduct
     */
    public function getExactlyMatchingProduct() {
        if ($this->getDeeplink()) {
            $whereClause = sprintf(
                    "`%s` = '%s'",
                    $this->getDeeplinkAttributeName(),
                    $this->getDeeplinkValue()
            );
            $products = SilvercartProduct::get($whereClause);
            if ($products) {
                return $products->First();
            }
        }
        return false;
    }
    
    /**
     * Returns the result of a partial match search with the url parameters Action
     * and ID
     * 
     * @return DataObjectSet
     */
    public function getPartiallyMatchingProducts() {
        if ($this->getDeeplink()) {
            $SQL_start = 0;
            
            if (isset ($_GET['start'])) {
                $SQL_start = (int)$_GET['start'];
            }
            $productsPerPage = SilvercartConfig::ProductsPerPage();
            $likeClause = sprintf(
                    "`%s` LIKE '%%%s%%'",
                    $this->getDeeplinkAttributeName(),
                    $this->getDeeplinkValue()
            );
            $products = SilvercartProduct::get($likeClause, null, null, "$SQL_start,$productsPerPage");
            return $products;
        }
        return false;
    }
    
    /**
     * Returns the relative path for the current view with identifier sections.
     * 
     * @return string
     */
    protected function getRelativeDeepLinkForPartiallyMatchingProducts() {
        $link = '';
        
        if (isset($this->urlParams['Action'])) {
            $link .= $this->urlParams['Action'].'/';
        }
        if (isset($this->urlParams['ID'])) {
            $link .= $this->urlParams['ID'].'/';
        }
        
        return $link;
    }
    
    /**
     * Returns the deeplink value
     * 
     * @return string
     */
    protected function getDeeplinkValue() {
        $deeplinkValue  = '';
        $deeplink       = $this->getDeeplink();
        if ($deeplink instanceof SilvercartDeeplink) {
            $deeplinkValue = $this->urlParams['ID'];
            
            if (strlen($deeplink->Prefix) > 0) {
                while (strpos($deeplinkValue, $deeplink->Prefix) === 0) {
                    $deeplinkValue = substr($deeplinkValue, strlen($deeplink->Prefix));
                }
            }
            
            $revertedDeeplinkValue = strrev($deeplinkValue);
            if (strlen($deeplink->Suffix) > 0) {
                $revertedSuffix = strrev($deeplink->Suffix);
                while (strpos($revertedDeeplinkValue, $revertedSuffix) === 0) {
                    $deeplinkValue = strrev(substr($revertedDeeplinkValue, strlen($revertedSuffix)));
                }
            }
        }
        return $deeplinkValue;
    }
    
    /**
     * Returns the deeplink name
     * 
     * @return string
     */
    protected function getDeeplinkAttributeName() {
        return $this->urlParams['Action'];
    }
    
    /**
     * Returns the inherited UseOnlyDefaultGroupView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getUseOnlyDefaultGroupViewInherited($context = null) {
        return true;
    }

    /**
     * Returns the inherited DefaultGroupView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getDefaultGroupViewInherited($context = null) {
        return SilvercartGroupViewHandler::getDefaultGroupView();
    }
}
