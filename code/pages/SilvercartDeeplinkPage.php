<?php

/**
 * Redirects to a product which is identified by url parameters product attribute
 * and attribute value;
 * If the result is ambiguous the set of products is shown.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 29.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDeeplinkPage extends Page {
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.10.2011
     */
    public static $icon = "silvercart/images/page_icons/metanavigation_page";
}

/**
 * corresponding controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 29.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDeeplinkPage_Controller extends Page_Controller {
    
    /**
     *
     * @var DataObjectSet the products that match the attribute/value in the url params 
     */
    protected $products = null;
    
    protected $SQL_start = 0;

    /**
     * controller method called before anything else happens
     * 
     * return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 1.8.2011
     */
    public function init() {
        if (isset($_GET['start'])) {
            $this->SQL_start = (int)$_GET['start'];
        }
        $formActionLink  = $this->getRelativeDeepLinkForPartiallyMatchingProducts();
        $formActionLink .= 'customHtmlFormSubmit';
        
        //fill $products if there is more than one result
        if (!$this->getExactlyMatchingProduct() && $this->getPartiallyMatchingProducts()) {
            $this->products = $this->getPartiallyMatchingProducts();
            $productIdx = 0;
            $productAddCartForm = $this->getCartFormName();
            foreach ($this->products as $product) {
                $backLink = $this->Link().$this->getRelativeDeepLinkForPartiallyMatchingProducts()."?start=".$this->SQL_start;
                $addCartForm = new $productAddCartForm($this, array('productID' => $product->ID, 'backLink' => $backLink), array('submitAction' => $formActionLink));
                $this->registerCustomHtmlForm('ProductAddCartForm'.$productIdx, $addCartForm);
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
     * Redefine the rules to interpret the url parameters as strings:
     * -The first parameter must be interpreted as a product attribute
     * -The second parameter must be interpreted as the attributes value
     * 
     * @param SS_HTTPRequest $request the HTTP request
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.7.2011
     */
    public function handleAction($request) {
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
     */
    public function getProducts() {
        return $this->products;
    }
    
    /**
     * Getter for a Deeplink object determined by url param Action
     * A param ID must be there too.
     * 
     * @return SilvercartDeeplink|false 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.7.2011
     */
    public function getDeeplink() {
        if (isset ($this->urlParams['Action'])) {
            $filter = sprintf("`isActive` = 1 AND `productAttribute` = '%s'", $this->urlParams['Action']);
            $deeplinkObject = DataObject::get_one('SilvercartDeeplink', $filter);
            return $deeplinkObject;
        }
        return false;
    }
    
    /**
     * Returns the result of an exact match search with the url parameters Action
     * and ID
     * 
     * @return SilvercartProduct|false
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.7.2011 
     */
    public function getExactlyMatchingProduct() {
        if ($this->getDeeplink()) {
            $whereClause = sprintf("`%s` = '%s'", $this->urlParams['Action'], $this->urlParams['ID']);
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
     * @return DataObjectSet|false a set of products 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.7.2011
     */
    public function getPartiallyMatchingProducts() {
        if ($this->getDeeplink()) {
            $SQL_start = 0;
            
            if (isset ($_GET['start'])) {
                $SQL_start = (int)$_GET['start'];
            }
            $productsPerPage = SilvercartConfig::ProductsPerPage();
            $likeClause = sprintf("`%s` LIKE '%%%s%%'", $this->urlParams['Action'], $this->urlParams['ID']);
            $products = SilvercartProduct::get($likeClause, null, null, "$SQL_start,$productsPerPage");
            return $products;
        }
        return false;
    }
    
    /**
     * Returns the relative path for the current view with identifier sections.
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 1.8.2011
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
}
