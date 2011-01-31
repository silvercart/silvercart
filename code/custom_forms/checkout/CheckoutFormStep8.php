<?php

/**
 * CheckoutProcessPaymentAfterOrder
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2011
 * @license BSD
 */
class CheckoutFormStep8 extends CustomHtmlForm {

    /**
     * processor method
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.11.2010
     */
    public function process() {
        $member = Member::currentUser();
        $checkoutData = $this->controller->getCombinedStepData();
        $paymentSuccessful = false;

        if ($member) {
            // postprocessing of payment method
            if (!$this->controller->paymentMethodObj) {
                $this->controller->paymentMethodObj = DataObject::get_by_id(
                                'PaymentMethod',
                                $checkoutData['PaymentMethod']
                );
            }

            $orderObj = DataObject::get_by_id(
                            'Order',
                            $checkoutData['orderId']
            );

            if ($this->controller->paymentMethodObj &&
                    $orderObj) {

                $this->controller->paymentMethodObj->setController($this->controller);
                $paymentSuccessful = $this->controller->paymentMethodObj->processPaymentAfterOrder($orderObj);
            } else {
                Director::redirect($this->controller->Link() . '/Cancel');
                exit();
            }

            if ($paymentSuccessful) {
                $this->controller->addCompletedStep();
                $this->controller->NextStep();
            }
        }
    }

}