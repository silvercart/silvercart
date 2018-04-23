<?php

namespace SilverCart\Forms\FormFields;

use SilverCart\Model\Shipment\ShippingMethod;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/** 
 * A formfield for the shipment checkout step that can render additional
 * shipping informations.
 *
 * @package SilverCart
 * @subpackage Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShippingOptionsetField extends OptionsetField {

    /**
     * Create a UL tag containing sets of radio buttons and labels.  The IDs are set to
     * FieldID_ItemKey, where ItemKey is the key with all non-alphanumerics removed.
     * 
     * @param array $properties not in use, just declared to be compatible with parent
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2014
     */
    public function Field($properties = []) {
        $odd            = 0;
        $itemIdx        = 0;
        $source         = $this->getSource();
        $items          = [];
        $templateVars   = [
            'ID'            => $this->id(),
            'extraClass'    => $this->extraClass(),
            'items'         => [],
        ];

        if (is_array($source)) {
            foreach ($source as $key => $value) {
                $shippingMethod = ShippingMethod::get()->byID($key);

                if ($shippingMethod) {
                    $odd        = ($odd + 1) % 2;
                    $checked    = false;

                    // check if field should be checked
                    if ($this->value == $key) {
                        $checked = true;
                    }

                    $items['item_'.$itemIdx] = new ArrayData([
                        'ID'                => $this->id() . "_" . preg_replace('@[^a-zA-Z0-9]+@','',$key),
                        'checked'           => $checked,
                        'odd'               => $odd,
                        'even'              => !$odd,
                        'disabled'          => ($this->disabled || in_array($key, $this->disabledItems)),
                        'value'             => $key,
                        'label'             => $value,
                        'name'              => $this->name,
                        'htmlId'            => $this->id() . "_" . preg_replace('@[^a-zA-Z0-9]+@','',$key),
                        'description'       => Convert::raw2xml($shippingMethod->Description),
                        'ShippingMethod'    => $shippingMethod,
                    ]);
                }

                $itemIdx++;
            }
        }

        $templateVars['items'] = new ArrayList($items);

        $output = $this->customise($templateVars)->renderWith($this->getTemplates());

        return $output;
    }
}
