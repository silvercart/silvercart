<?php

namespace SilverCart\Forms;

use SilverCart\Admin\Model\Config;
use SilverCart\Forms\CustomForm;
use SilverCart\Model\Product\Product;
use SilverStripe\Control\Director;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\Validator;

/**
 * Form to add a product to the shopping cart.
 * 
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 08.11.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AddToCartForm extends CustomForm
{
    /**
     * Custom form action path, if not linking to itself.
     * E.g. could be used to post to an external link
     *
     * @var string
     */
    protected $formActionPath = 'sc-action/addToCart';
    /**
     * Product.
     *
     * @var Product|null
     */
    protected $product = null;
    /**
     * The forms view context.
     * For example 'List', 'Detail', 'Title'.
     *
     * @var string|null
     */
    protected $viewContext = null;
    
    /**
     * Create a new form, with the given fields an action buttons.
     *
     * @param Product        $product    Product to add to cart.
     * @param RequestHandler $controller Optional parent request handler
     * @param string         $name       The method on the controller that will return this form object.
     * @param FieldList      $fields     All of the fields in the form - a {@link FieldList} of {@link FormField} objects.
     * @param FieldList      $actions    All of the action buttons in the form - a {@link FieldLis} of {@link FormAction} objects
     * @param Validator      $validator  Override the default validator instance (Default: {@link RequiredFields})
     * 
     * @return void
     */
    public function __construct(Product $product, RequestHandler $controller = null, $name = self::DEFAULT_NAME, FieldList $fields = null, FieldList $actions = null, Validator $validator = null)
    {
        $this->setProduct($product);
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() : array
    {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $product  = $this->getProduct();
            $quantity = $product->getQuantityInCart();
            if ($quantity == 0) {
                $quantity = $product->getMinQuantityForCart();
            }
            $fields += [
                HiddenField::create('backLink',  'backLink',  $this->getBackLink()),
                HiddenField::create('productID', 'productID', $product->ID),
                NumericField::create('productQuantity', $product->fieldLabel('Quantity'), $quantity, $this->getQuantityMaxLength())
            ];
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() : array
    {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('addtocart', $this->getSubmitButtonTitle())
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }

    /**
     * Returns the product
     * 
     * @return Product|null
     */
    public function getProduct() : ?Product
    {
        return $this->product;
    }

    /**
     * Sets the product.
     * 
     * @param Product $product Product
     * 
     * @return AddToCartForm
     */
    public function setProduct(Product $product) : AddToCartForm
    {
        $this->product = $product;
        return $this;
    }
    
    /**
     * Returns the view context.
     * 
     * @return string
     */
    public function getViewContext() : ?string
    {
        return $this->viewContext;
    }

    /**
     * Sets the view context.
     * 
     * @param string $viewContext View context
     * 
     * @return AddToCartForm
     */
    public function setViewContext(string $viewContext) : AddToCartForm
    {
        $this->setTemplateBySuffix('_' . $viewContext);
        $this->viewContext = $viewContext;
        return $this;
    }
    
    /**
     * Returns the submit button title for the add-to-cart-form.
     * 
     * @return string
     */
    protected function getSubmitButtonTitle() : string
    {
        $product = $this->getProduct();
        if ($product->HasReleaseDate()) {
            $submitButtonTitle = $product->fieldLabel('PreorderNow');
        } elseif ($product->isInCart()) {
            $submitButtonTitle = $product->fieldLabel('ChangeQuantity');
        } else {
            $submitButtonTitle = $product->fieldLabel('AddToCart');
        }
        return $submitButtonTitle;
    }
    
    /**
     * Returns the back link for the add-to-cart-form.
     * 
     * @return string
     */
    protected function getBackLink() : string
    {
        $backLink = $this->getController()->getRequest()->getURL();
        if (Director::is_relative_url($backLink)) {
            $backLink = Director::absoluteURL($backLink, true);
        }
        return $backLink;
    }
    
    /**
     * Returns the max length for the add-to-cart-form quantity field.
     * 
     * @return int
     */
    protected function getQuantityMaxLength() : int
    {
        $numberOfDecimalPlaces = $this->getProduct()->QuantityUnit()->numberOfDecimalPlaces;
        $maxLength             = strlen((string) Config::addToCartMaxQuantity());
        if ($maxLength === 0) {
            $maxLength++;
        }
        if ($numberOfDecimalPlaces !== false
         && $numberOfDecimalPlaces > 0
        ) {
            $maxLength += 1 + $numberOfDecimalPlaces;
        }
        return $maxLength;
    }
}