<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage FormFields
 */

/**
 * Adds some additional functionallity to default text field
 *
 * @package Silvercart
 * @subpackage FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 23.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTextField extends TextField {
    
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
     * Returns the HTML for the field
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2012
     */
    public function Field() {
        $attributes = array(
                'type'      => 'text',
                'class'     => 'text' . ($this->extraClass() ? $this->extraClass() : ''),
                'id'        => $this->id(),
                'name'      => $this->Name(),
                'value'     => $this->Value(),
                'tabindex'  => $this->getTabIndex(),
                'maxlength' => ($this->maxLength) ? $this->maxLength : null,
                'size'      => ($this->maxLength) ? min( $this->maxLength, 30 ) : null 
        );
        
        $placeholder = $this->getPlaceholder();
        if (!empty($placeholder)) {
            $attributes['placeholder'] = $placeholder;
        }

        if ($this->disabled) {
            $attributes['disabled'] = 'disabled';
        }

        return $this->createTag('input', $attributes);
    }
}