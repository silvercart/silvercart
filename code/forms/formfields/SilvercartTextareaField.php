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
 * Fixes the tabIndex for TextareaField.
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 2013-01-03
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartTextareaField extends TextareaField {
    
    /**
     * Placeholder to set
     *
     * @var string
     */
    protected $placeholder = '';
    
    /**
     * Returns the placeholder
     *
     * @return string
     */
    public function getPlaceholder() {
        return $this->placeholder;
    }

    /**
     * Sets the placeholder
     *
     * @param string $placeholder Placeholder to set
     * 
     * @return void
     */
    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;
    }

    /**
     * Returns the attributes
     * 
     * @return array
     */
    public function getAttributes() {
        return array_merge(
            parent::getAttributes(),
            array(
                'placeholder' => $this->getPlaceholder(),
            )
        );
    }

}