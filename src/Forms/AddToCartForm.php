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

class AddToCartForm extends CustomForm {
    
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
     * @var Product 
     */
    protected $product = null;
    
    /**
     * The forms view context.
     * For example 'List', 'Detail', 'Title'.
     *
     * @var string
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
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.11.2017
     */
    public function __construct(Product $product, RequestHandler $controller = null, $name = self::DEFAULT_NAME, FieldList $fields = null, FieldList $actions = null, Validator $validator = null) {
        $this->setProduct($product);
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $product = $this->getProduct();
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
    public function getCustomActions() {
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
     * @return Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * Sets the product.
     * 
     * @param Product $product Product
     * 
     * @return void
     */
    public function setProduct(Product $product) {
        $this->product = $product;
    }
    
    /**
     * Returns the view context.
     * 
     * @return string
     */
    public function getViewContext() {
        return $this->viewContext;
    }

    /**
     * Sets the view context.
     * 
     * @param string $viewContext View context
     * 
     * @return void
     */
    public function setViewContext($viewContext) {
        $this->setTemplateBySuffix('_' . $viewContext);
        $this->viewContext = $viewContext;
    }
    
    /**
     * Returns the submit button title for the add-to-cart-form.
     * 
     * @return string
     */
    protected function getSubmitButtonTitle() {
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
    protected function getBackLink() {
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
    protected function getQuantityMaxLength() {
        $numberOfDecimalPlaces = $this->getProduct()->QuantityUnit()->numberOfDecimalPlaces;
        $quantityFieldMaxLength = strlen((string) Config::addToCartMaxQuantity());
        if ($quantityFieldMaxLength == 0) {
            $quantityFieldMaxLength = 1;
        }
        if ($numberOfDecimalPlaces !== false &&
            $numberOfDecimalPlaces > 0) {
            $maxLength = $quantityFieldMaxLength + 1 + $numberOfDecimalPlaces;
        } else {
            $maxLength = $quantityFieldMaxLength;
        }
        return $maxLength;
    }
    
}