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
 * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.02.2013
 * @license see license file in modules root directory
 */
class SilvercartAddressOptionsetField extends OptionsetField {
    
    /**
     * Markup of the field
     *
     * @var string
     */
    protected $field = null;

    /**
     * Create a UL tag containing sets of radio buttons and labels.  The IDs are set to
     * FieldID_ItemKey, where ItemKey is the key with all non-alphanumerics removed.
     * 
     * @param array $properties not in unse, just declared to be compatible with parent
     *
     * @return string
     *
     * @author Sascha Köhler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function Field($properties = array()) {
        if (is_null($this->field)) {
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
                $address = DataObject::get_by_id('SilvercartAddress', $key);

                if ($address) {
                    $odd                = ($odd + 1) % 2;
                    $extraClass         = $odd ? "odd" : "even";
                    $checked            = false;
                    $isCompanyAddress   = $address->isCompanyAddress();

                    // check if field should be checked
                    if ($this->value == $key) {
                        $checked = true;
                    }

                    $items['item_'.$itemIdx] = new ArrayData(
                        array_merge(
                            array(
                                'ID'                => $this->id() . "_" . preg_replace('@[^a-zA-Z0-9]+@','',$key),
                                'checked'           => $checked,
                                'odd'               => $odd,
                                'even'              => !$odd,
                                'disabled'          => ($this->disabled || in_array($key, $this->disabledItems)),
                                'value'             => $key,
                                'label'             => $value,
                                'name'              => $this->name,
                                'htmlId'            => $this->id() . "_" . preg_replace('@[^a-zA-Z0-9]+@','',$key),
                                'isInvoiceAddress'  => $address->isInvoiceAddress(),
                                'isShippingAddress' => $address->isShippingAddress(),
                                'isCompanyAddress'  => $isCompanyAddress,
                                'isLastAddress'     => $address->isLastAddress(),
                                'address'           => $address,
                                'SilvercartCountry' => $address->SilvercartCountry(),
                            ),
                            $address->toRawMap()
                        )
                    );
                }

                $itemIdx++;
            }
            $templateVars['items'] = new ArrayList($items);
            $output                = $this->customise($templateVars)->renderWith('SilvercartAddressOptionsetField');

            $this->field = $output;
        }
        return $this->field;
    }
    
    /**
     * Basicly checks whether an address can be deleted by delete button or not
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.07.2012
     */
    public function canDelete() {
        $canDelete = false;
        if (count($this->getSource()) > 1) {
            $canDelete = true;
        }
        return $canDelete;
    }
}
