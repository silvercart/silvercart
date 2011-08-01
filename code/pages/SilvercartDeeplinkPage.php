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
class SilvercartDeeplinkPage extends SilvercartPage {
    
}

/**
 * corresponding controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 29.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDeeplinkPage_Controller extends SilvercartPage_Controller {
    
    /**
     *
     * @var DataObjectSet the products that match the attribute/value in the url params 
     */
    protected $products = null;

    public function init() {
        parent::init();
        //fill $products if there is more than one result
        if (!$this->getExactlyMatchingProduct() && $this->getPartiallyMatchingProducts()) {
            $products = $this->getProducts();
            $productIdx = 0;
            $productAddCartForm = $this->getCartFormName();
            foreach ($products as $product) {
                $this->registerCustomHtmlForm('ProductAddCartForm'.$productIdx, new $productAddCartForm($this, array('productID' => $product->ID)));
                $product->productAddCartForm = $this->InsertCustomHtmlForm(
                    'ProductAddCartForm' . $productIdx,
                    array(
                        $product
                    )
                );
                $productIdx++;
            }
        }
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
        if ($this->getDeeplink()) {
            if ($this->getExactlyMatchingProduct()) {
                return Director::redirect($this->getExactlyMatchingProduct()->Link());
            } elseif ($this->getPartiallyMatchingProducts()) {
                return $this->renderWith(array('SilvercartDeeplinkPage', 'Page'));
            }
        }
        return Director::redirect(DataObject::get_one('ErrorPage', '`ErrorCode` = 404')->Link());
    }


    /**
     * Return a set of Products to be rendered in the template
     * Only filled if the result does not point to one product only
     */
    public function getProducts() {
        $this->products = $this->getPartiallyMatchingProducts();
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
        if (isset ($this->urlParams['Action']) && isset ($this->urlParams['ID'])) {
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
            $likeClause = sprintf("`%s` LIKE '%%%s%%'", $this->urlParams['Action'], $this->urlParams['ID']);
            $products = SilvercartProduct::get($likeClause);
            return $products;
        }
        return false;
    }
}
