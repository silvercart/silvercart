<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @license see license file in modules root directory
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
 * @license see license file in modules root directory
 */
class SilvercartDeeplinkPage_Controller extends Page_Controller {
    
    /**
     *
     * @var DataList the products that match the attribute/value in the url params 
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
     * @param SS_HTTPRequest $request The HTTP request
     * @param string         $action  Action
     * 
     * @return string|void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2013
     */
    public function handleAction($request, $action) {
        if ($this->getDeeplink()&& isset ($this->urlParams['ID'])) {
            if ($this->getExactlyMatchingProduct()) {
                return $this->redirect($this->getExactlyMatchingProduct()->Link(), 301);
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
        
        return $this->redirect(DataObject::get_one('ErrorPage', '\"ErrorCode\" = 404')->Link());
    } 


    /**
     * Return a set of Products to be rendered in the template
     * Only filled if the result does not point to one product only
     * 
     * @return DataList
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
            $deeplinkObject = SilvercartDeeplink::get()
                    ->filter(
                            array(
                                'isActive' => 1,
                                'productAttribute' => $this->getDeeplinkAttributeName(),
                            )
                    )
                    ->first();
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
            $whereClause = sprintf("\"%s\" = '%s'", $this->getDeeplinkAttributeName(), $this->getDeeplinkValue());
            $products = SilvercartProduct::getProducts($whereClause);
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
     * @return DataList|false a set of products 
     */
    public function getPartiallyMatchingProducts() {
        if ($this->getDeeplink()) {
            $SQL_start = 0;
            
            if (isset ($_GET['start'])) {
                $SQL_start = (int)$_GET['start'];
            }
            $productsPerPage = SilvercartConfig::ProductsPerPage();
            $likeClause = sprintf("\"%s\" LIKE '%%%s%%'", $this->getDeeplinkAttributeName(), $this->getDeeplinkValue());
            $products = SilvercartProduct::getProducts($likeClause, null, null, "$SQL_start,$productsPerPage");
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
