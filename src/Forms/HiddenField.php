<?php

namespace SilverCart\Forms;

use SilverStripe\Forms\HiddenField as SilverStripeHiddenField;

/**
 * Extension for SilverStripe's HiddenField.
 * Adds setter methods for the field HTML ID and the field holder HTML ID.
 * 
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.08.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class HiddenField extends SilverStripeHiddenField
{
    /**
     * HTML ID of the field.
     *
     * @var string 
     */
    protected $ID = null;
    /**
     * HTML ID of the field holder.
     *
     * @var string 
     */
    protected $holderID = null;
    
    /**
     * Sets this field's HTML ID.
     * 
     * @param string $ID HTML ID
     * 
     * @return $this
     */
    public function setID($ID)
    {
        $this->ID = $ID;
        return $this;
    }
    
    /**
     * Sets this field holder's HTML ID.
     * 
     * @param string $holderID HTML ID
     * 
     * @return $this
     */
    public function setHolderID($holderID)
    {
        $this->holderID = $holderID;
        return $this;
    }

    /**
     * Returns the HTML ID of the field.
     *
     * The ID is generated as FormName_FieldName. All Field functions should ensure that this ID is
     * included in the field.
     * 
     * A custom HTML ID can be set by self::setID().
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.08.2018
     */
    public function ID()
    {
        if (is_null($this->ID)) {
            $this->setID($this->getTemplateHelper()->generateFieldID($this));
        }
        return $this->ID;
    }

    /**
     * Returns the HTML ID for the form field holder element.
     *
     * @return string
     */
    public function HolderID()
    {
        if (is_null($this->holderID)) {
            $this->setHolderID($this->getTemplateHelper()->generateFieldHolderID($this));
        }
        return $this->holderID;
    }
}
