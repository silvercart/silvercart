<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverCart\Checkout\Checkout;
use SilverCart\Dev\Tools;
use SilverCart\Forms\DecrementPositionQuantityForm;
use SilverCart\Forms\IncrementPositionQuantityForm;
Use SilverCart\Forms\RemovePositionForm;
use SilverCart\Forms\Checkout\CheckoutFormStep2;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Pages\Page;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\Member;
use SilverStripe\Control\Director;

/**
 * CartPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CartPageController extends \PageController
{
    const SESSION_KEY_CONTINUE_SHOPPING_LINK = 'SilverCart.CartPage.ContinueShoppingLink';
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'IncrementPositionQuantityForm',
        'DecrementPositionQuantityForm',
        'RemovePositionForm',
    ];
    /**
     * Determines whether to show a positions description text in print preview.
     * 
     * @var bool
     */
    private static $show_description_in_print_preview = false;
    /**
     * Checkout.
     *
     * @var Checkout
     */
    protected $checkout = null;
    
    /**
     * Sets the Continue Shopping Link.
     * 
     * @param string $returnRefererLink Return referer link
     * 
     * @return void
     */
    public static function setContinueShoppingLink(string $returnRefererLink) : void
    {
        Tools::Session()->set(self::SESSION_KEY_CONTINUE_SHOPPING_LINK, $returnRefererLink);
        Tools::saveSession();
    }
    
    /**
     * Returns the Continue Shopping Link.
     * 
     * @return string
     */
    public static function getContinueShoppingLink() : string
    {
        $link = Tools::Session()->get(self::SESSION_KEY_CONTINUE_SHOPPING_LINK);
        if ($link === null) {
            $defaultHomepage = self::getDefaultHomepage();
            if ($defaultHomepage !== null) {
                $link = $defaultHomepage->Link();
            }
        }
        return (string) $link;
    }

    /**
     * Initialise the shopping cart.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    protected function init() : void
    {
        $customer = Customer::currentUser();
        if ($customer instanceof Member
        && $customer->ShoppingCartID > 0
        ) {
            $customer->getCart();
        }
        parent::init();
        if ($customer instanceof Member
         && $customer->getCart()->exists()
         && $customer->getCart()->ShoppingCartPositions()->exists()
         && Config::RedirectToCheckoutWhenInCart()
        ) {
            $this->redirect(Tools::PageByIdentifierCode(Page::IDENTIFIER_CHECKOUT_PAGE)->Link());
        } elseif ($customer instanceof Member
               && $customer->getCart()->exists()
               && $customer->getCart()->ShoppingCartPositions()->exists()
        ) {
            $customer->getCart()->adjustPositionQuantitiesToStockQuantities();
        }
        $referer = (string) $this->getReturnReferer();
        $page    = SiteTree::get_by_link($referer);
        if (!($page instanceof CheckoutStep)
         && !($page instanceof CartPage)
        ) {
            if (strpos(Director::makeRelative($referer), Director::makeRelative($this->Link())) !== 0) {
                $checkout = CheckoutStep::get()->first();
                if (strpos(Director::makeRelative($referer), Director::makeRelative($checkout->Link())) !== 0) {
                    self::setContinueShoppingLink($referer);
                }
            }
        }
    }

    /** Indicates wether ui elements for removing items and altering their
     * quantity should be shown in the shopping cart templates.
     *
     * @return boolean true
     */
    public function getEditableShoppingCart() {
        return true;
    }

    /**
     * Returns the checkout.
     * 
     * @return Checkout
     */
    public function getCheckout() {
        if (is_null($this->checkout)) {
            $this->checkout = Checkout::create_from_session($this);
        }
        return $this->checkout;
    }
    
    /**
     * Returns an instance of CheckoutFormStep2 to represent a valid 
     * checkout context.
     * 
     * @return CheckoutFormStep2
     */
    public function getCheckoutContext() {
        $checkoutStepPage = Tools::PageByIdentifierCode(Page::IDENTIFIER_CHECKOUT_PAGE);
        $checkoutStepPageController = ModelAsController::controller_for($checkoutStepPage);
        $checkoutStepPageController->handleRequest($this->getRequest());
        return new CheckoutFormStep2($checkoutStepPageController);
    }
    
    /**
     * Returns the shopping cart position by the given ID
     * 
     * @param int $positionID Shopping cart position ID
     * 
     * @return ShoppingCartPosition
     */
    protected function getPositionByID($positionID) {
        if (is_null($positionID)) {
            $position = ShoppingCartPosition::singleton();
        } else {
            $position = ShoppingCartPosition::get()->byID($positionID);
            if (!($position instanceof ShoppingCartPosition)) {
                $position = ShoppingCartPosition::singleton();
            }
        }
        return $position;
    }

    /**
     * Returns the form for incrementing the amount of this position.
     * 
     * @var HTTPRequest $request    HTTP request
     * @var int         $positionID ID of the context shopping cart position.
     *
     * @return IncrementPositionQuantityForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function IncrementPositionQuantityForm(HTTPRequest $request, $positionID = null) {
        $form = new IncrementPositionQuantityForm($this->getPositionByID($positionID), $this);
        return $form;
    }

    /**
     * Returns the form for decrementing the amount of this position.
     * 
     * @var HTTPRequest $request    HTTP request
     * @var int         $positionID ID of the context shopping cart position.
     *
     * @return DecrementPositionQuantityForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function DecrementPositionQuantityForm(HTTPRequest $request, $positionID = null) {
        $form = new DecrementPositionQuantityForm($this->getPositionByID($positionID), $this);
        return $form;
    }

    /**
     * Returns the form for removing this position.
     * 
     * @var HTTPRequest $request    HTTP request
     * @var int         $positionID ID of the context shopping cart position.
     *
     * @return RemovePositionForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function RemovePositionForm(HTTPRequest $request, $positionID = null) {
        $form = new RemovePositionForm($this->getPositionByID($positionID), $this);
        return $form;
    }

    /**
     * Returns whether to show a positions description text in print preview.
     * 
     * @return bool
     */
    public function ShowDescriptionInPrintPreview() : bool
    {
        return (bool) $this->config()->show_description_in_print_preview;
    }
}