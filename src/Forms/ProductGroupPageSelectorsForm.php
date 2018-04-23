<?php

namespace SilverCart\Forms;

use SilverCart\Admin\Model\Config;
use SilverCart\Forms\CustomForm;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Pages\ProductGroupPageController;
use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Product\Product;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Security\Member;

/**
 * A form that displays fields for manipulating the display of a
 * SilverCart\Model\pages\ProductGroupPage group view results.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupPageSelectorsForm extends CustomForm {
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form',
        'pull-left',
        'no-margin',
    ];
    
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $productsPerPage = $this->getController()->getProductsPerPageSetting();
            if ($productsPerPage == Config::getProductsPerPageUnlimitedNumber()) {
                $productsPerPage = 0;
            }
            $product                     = Product::singleton();
            $sortableFrontendFields      = $product->sortableFrontendFields();
            $sortableFrontendFieldValues = array_flip(array_keys($sortableFrontendFields));
            if (!array_key_exists($product->getDefaultSort(), $sortableFrontendFieldValues)) {
                $sortableFrontendFieldValues[$product->getDefaultSort()] = 0;
            }
            $sortOrder                         = $sortableFrontendFieldValues[$product->getDefaultSort()];
            $sortableFrontendFieldsForDropdown = array_values($sortableFrontendFields);
            asort($sortableFrontendFieldsForDropdown);
            
            $fields += [
                DropdownField::create('SortOrder', _t(ProductGroupPage::class . '.SORT_ORDER', 'Sort order'), $sortableFrontendFieldsForDropdown, $sortOrder),
            ];
            $productsPerPageOptions = Config::getProductsPerPageOptions();
            if (!empty($productsPerPageOptions)) {
                $fields += [
                    DropdownField::create('productsPerPage', _t(ProductGroupPage::class . '.PRODUCTS_PER_PAGE', 'Products per page'), $productsPerPageOptions, $productsPerPage),
                ];
            } else {
                $fields += [
                    HiddenField::create('productsPerPage', '', Config::getProductsPerPageOptions()),
                ];
            }
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('dosubmit', _t(ProductGroupPage::class . '.OK', 'Ok'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }

    /**
     * Returns the total number of products for the current controller.
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.12.2012
     */
    public function getTotalNumberOfProducts() {
        $controller       = Controller::curr();
        $numberOfProducts = 0;

        if ($controller &&
            $controller instanceof ProductGroupPageController) {

            $numberOfProducts = $controller->getTotalNumberOfProducts();
        }

        return $numberOfProducts;
    }
    
    /**
     * Returns whether there are products per page options.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function hasProductsPerPageOptions() {
        $productsPerPageOptions = Config::getProductsPerPageOptions();
        return !empty($productsPerPageOptions);
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function doSubmit($data, CustomForm $form) {
        $backLink = $this->getController()->Link();
        $member   = Customer::currentUser();
        
        $product                     = Product::singleton();
        $sortableFrontendFields      = $product->sortableFrontendFields();
        $sortableFrontendFieldValues = array_keys($sortableFrontendFields);
        $sortOrder                   = $sortableFrontendFieldValues[$data['SortOrder']];
        Product::setDefaultSort($sortOrder);
        
        if (array_key_exists('productsPerPage', $data) &&
            !empty($data['productsPerPage'])) {
            
            if (!($member instanceof Member) ||
                !$member->exists()) {
                $member = Customer::createAnonymousCustomer();
            }

            if ($member instanceof Member &&
                $member->exists()) {
                $member->getCustomerConfig()->productsPerPage = $data['productsPerPage'];
                $member->getCustomerConfig()->write();
            }
        }
        
        if (array_key_exists('backLink', $data) &&
            !empty($data['backLink'])) {
            $backLink = $data['backLink'];
        }
        
        $this->getController()->redirect($backLink);
    }
    
}
