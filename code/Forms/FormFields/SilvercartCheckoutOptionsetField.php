<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 */

/**
 * A formfield for the payment checkout step that can render additional
 * payment informations.
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 21.04.2011
 * @license see license file in modules root directory
 */
class SilvercartCheckoutOptionsetField extends OptionsetField {

    /**
     * Create a UL tag containing sets of radio buttons and labels.  The IDs are set to
     * FieldID_ItemKey, where ItemKey is the key with all non-alphanumerics removed.
     * 
     * @param array $properties not in unse, just declared to be compatible with parent
     *
     * @return string
     *
     * @author Sascha Köhler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.04.2013
     */
    public function Field($properties = array()) {
        $odd            = 0;
        $itemIdx        = 0;
        $source         = $this->getSource();
        $items          = array();
        $templateVars   = array(
            'ID'            => $this->id(),
            'extraClass'    => $this->extraClass(),
            'items'         => array()
        );

        foreach ($source as $key => $value) {

            // get payment method
            $paymentMethod = DataObject::get_by_id('SilvercartPaymentMethod', $key);

            if ($paymentMethod) {
                $odd        = ($odd + 1) % 2;
                $extraClass = $odd ? "odd" : "even";
                $checked    = false;
                
                // check if field should be checked
                if ($this->value == $key) {
                    $checked = true;
                }

                $items['item_'.$itemIdx] = new ArrayData(array(
                    'ID'                => $this->id() . "_" . preg_replace('@[^a-zA-Z0-9]+@','',$key),
                    'checked'           => $checked,
                    'odd'               => $odd,
                    'even'              => !$odd,
                    'disabled'          => ($this->disabled || in_array($key, $this->disabledItems)),
                    'value'             => $key,
                    'label'             => $value,
                    'name'              => $this->name,
                    'htmlId'            => $this->id() . "_" . preg_replace('@[^a-zA-Z0-9]+@','',$key),
                    'description'       => Convert::raw2xml($paymentMethod->getPaymentDescription()),
                    'showPaymentLogos'  => $paymentMethod->showPaymentLogos,
                    'PaymentLogos'      => $paymentMethod->PaymentLogos()
                ));
            }

            $itemIdx++;
        }

        $templateVars['items'] = new ArrayList($items);

        $output = $this->customise($templateVars)->renderWith('SilvercartCheckoutOptionsetField');

        return $output;
    }
}
