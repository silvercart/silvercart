<?php

/**
 * represents a shopping cart. Every customer has one initially.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 23.10.2010
 */
class CartPage extends Page {

    public static $singular_name = "cart page";

    /**
     * default instances related to $this
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return void
     * @since 23.10.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $cartPage = new CartPage();
            $cartPage->Title = _t('Page.CART');
            $cartPage->URLSegment = _t('CartPage.URL_SEGMENT', 'cart');
            $cartPage->Status = "Published";
            $cartPage->ShowInMenus = true;
            $cartPage->ShowInSearch = false;
            $cartPage->write();
            $cartPage->publish("Stage", "Live");
        }
    }

}

/**
 * related controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class CartPage_Controller extends Page_Controller {

    /**
     * Calls the registered shoppingcart modules method "ShoppingCartInit"
     * if available.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.01.2011
     */
    public function init() {
        $registeredModules = ShoppingCart::$registeredModules;

        foreach ($registeredModules as $registeredModule) {
            $registeredModuleObj = new $registeredModule();

            if ($registeredModuleObj->hasMethod('ShoppingCartInit')) {
                $registeredModuleObj->ShoppingCartInit();
            }
        }

        //create and register forms for shopping cart manipulation
        $member = Member::currentUser();
        if ($member) {
            $cart = $member->shoppingCart();
            if ($cart) {
                $positions = $cart->positions();
                if ($positions) {
                    $this->positions = $positions;
                    $positionIndex = 0;
                    foreach ($this->positions as $position) {
                        $this->registerCustomHtmlForm('IncrementPositionQuantityForm' . $positionIndex, new IncrementPositionQuantityForm($this, array('positionID' => $position->ID)));
                        $this->registerCustomHtmlForm('DecrementPositionQuantityForm' . $positionIndex, new DecrementPositionQuantityForm($this, array('positionID' => $position->ID)));
                        $this->registerCustomHtmlForm('RemovePositionForm' . $positionIndex, new RemovePositionForm($this, array('positionID' => $position->ID)));

                        $position->IncrementPositionQuantityForm = $this->InsertCustomHtmlForm(
                                        'IncrementPositionQuantityForm' . $positionIndex,
                                        array(
                                            $position
                                        )
                        );
                        $position->DecrementPositionQuantityForm = $this->InsertCustomHtmlForm(
                                        'DecrementPositionQuantityForm' . $positionIndex,
                                        array(
                                            $position
                                        )
                        );
                        $position->RemovePositionForm = $this->InsertCustomHtmlForm(
                                        'RemovePositionForm' . $positionIndex,
                                        array(
                                            $position
                                        )
                        );
                        $positionIndex++;
                    }
                }
            }
        }
        parent::init();
    }

    /**
     * Return the current members cart positions for frontend
     *
     * @return DataObjectSet | false Returns the current members cart positions.
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 9.2.2011
     */
    public function getPositions() {
        if ($this->positions) {
            return $this->positions;
        } else {
            return false;
        }
    }

}