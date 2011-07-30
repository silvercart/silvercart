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
    protected $products = false;

    /**
     * Redefine the rules to interpret the url parameters as strings:
     * -The first parameter must be validated as a product attribute
     * -The second parameter must be interpreted as the attributes value
     * 
     * @param SS_HTTPRequest $request the HTTP request
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.7.2011
     */
    public function handleAction($request) {
        if (isset($this->urlParams['Action'])) {
            $filter = sprintf("`isActive` = 1 AND `productAttribute` = '%s'", $this->urlParams['Action']);
            $deeplinkObject = DataObject::get_one('SilvercartDeeplink', $filter);
            if ($deeplinkObject) {
                if (isset($this->urlParams['ID'])) {
                    $whereClause = sprintf("`%s` = '%s'", $this->urlParams['Action'], $this->urlParams['ID']);
                    $products = SilvercartProduct::get($whereClause);
                    if ($products && $products->Count() == 1) {
                        return Director::redirect($products->First()->Link());
                    } elseif ($products && $products->Count() > 1) {
                        $this->products = $products;
                    } else {
                        //make an SQL LIKE search
                        $likeClause = sprintf("`%s` LIKE '%%%s%%'", $this->urlParams['Action'], $this->urlParams['ID']);
                        $products = SilvercartProduct::get($likeClause);
                        if (!$products) {
                            return Director::redirect(DataObject::get_one('ErrorPage', '`ErrorCode` = 404')->Link());
                        }
                    }
                    $this->products = $products;
                    return $this->renderWith(array('SilvercartDeeplinkPage', 'Page'));
                }
            }
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
}

