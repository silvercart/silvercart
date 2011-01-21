<?php

/**
 * single page checkout
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 28.10.2010
 * @license BSD
 */
class CheckoutPage extends CustomHtmlFormStepPage {

    public static $singular_name = "Checkout Seite";
    public static $allowed_children = array(
        'none'
    );

    /**
     * instances to be created if no instances exist yet
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.10.10
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        $records = DataObject::get_one($this->ClassName);
        if (!$records) {
            $Page = new $this->ClassName();
            $Page->Title = "Checkout";
            $Page->URLSegment = "checkout";
            $Page->Status = "Published";
            $Page->ShowInMenus = false;
            $Page->ShowInSearch = false;
            $Page->CanViewType = 'LoggedInUsers';
            $Page->write();
            $Page->publish("Stage", "Live");
        }
    }

}

/**
 * corresponding controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 28.10.2010
 * @license BSD
 */
class CheckoutPage_Controller extends CustomHtmlFormStepPage_Controller {
    
    /**
     * Liefert die Bearbeitungsgebuehren fuer die gewaehlte Zahlungsart
     * zurueck.
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public function getHandlingCosts() {
        $checkoutData   = $this->getCombinedStepData();
        $handlingCosts  = 0;
        
        $paymentMethodObj = DataObject::get_by_id(
			'PaymentMethod',
            $checkoutData['PaymentMethod']
		);
        
        if ($paymentMethodObj) {
            $handlingCostsObj = $paymentMethodObj->getHandlingCost();
        } else {
            $handlingCostsObj = new Money;
            $handlingCostsObj->setAmount($handlingCosts);
        }
        
        return $handlingCostsObj;
    }
    
    public function CarrierAndShippingMethodTitle() {
        $checkoutData   = $this->getCombinedStepData();
        $title          = '';
        
        $selectedShippingMethod = DataObject::get_by_id(
            'ShippingMethod',
            $checkoutData['ShippingMethod']
        );
        
        if ($selectedShippingMethod) {
            $title = $selectedShippingMethod->carrier()->Title . "-" . $selectedShippingMethod->Title;
        }
        
        return $title;
    }
    
    public function HandlingCostShipment() {
        $checkoutData           = $this->getCombinedStepData();
        $handlingCostShipment   = 0;
        
        $selectedShippingMethod = DataObject::get_by_id(
            'ShippingMethod',
            $checkoutData['ShippingMethod']
        );
        
        if ($selectedShippingMethod) {
            $handlingCostShipmentObj = $selectedShippingMethod->getShippingFee()->Price;
        } else {
            $handlingCostShipmentObj = new Money();
            $handlingCostShipmentObj->setAmount($handlingCostShipment);
        }
        
        return $handlingCostShipmentObj;
    }
    
    public function PaymentMethodTitle() {
        $checkoutData   = $this->getCombinedStepData();
        $title          = '';
        
        $paymentMethodObj = DataObject::get_by_id(
			'PaymentMethod',
            $checkoutData['PaymentMethod']
		);

		if ($paymentMethodObj) {
            $title = $paymentMethodObj->Name;
		}
        
        return $title;
    }
    
    public function HandlingCostPayment() {
        $checkoutData        = $this->getCombinedStepData();
        $handlingCostPayment = 0;
        
        $paymentMethodObj = DataObject::get_by_id(
			'PaymentMethod',
            $checkoutData['PaymentMethod']
		);

		if ($paymentMethodObj) {
            $handlingCostPaymentObj = $paymentMethodObj->getHandlingCost();
		} else {
            $handlingCostPaymentObj = new Money();
            $handlingCostPaymentObj->setAmount(0);
        }
        
        return $handlingCostPaymentObj;
    }
    
    /**
     * Template metod; returns the price of the cart positions + shipping fee
     *
     * @return string a price amount
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 4.1.2011
     */
    public function getAmountGrossRaw() {
        $member         = Member::currentUser();
        $stepData       = $this->getCombinedStepData();
        $cart           = $member->shoppingCart();
        $shippingMethod = DataObject::get_by_id('ShippingMethod', $stepData['ShippingMethod']);
        $amountTotal    = 0;
        
        if ($cart && $shippingMethod) {
            $shippingFee    = $shippingMethod->getShippingFee()->Price->getAmount();
            $priceSumCart   = $cart->getPrice()->getAmount();
            
            if ($shippingFee && $priceSumCart) {
                $amountTotal = $shippingFee + $priceSumCart;
            }
        }
        
        $amountTotalObj = new Money;
        $amountTotalObj->setAmount($amountTotal);
        
        return $amountTotalObj;
    }
}