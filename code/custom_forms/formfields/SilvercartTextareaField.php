<?php
/**
 * Copyright 2013 pixeltricks GmbH
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
 * @subpackage Forms
 */

/**
 * Fixes the tabIndex for TextareaField.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 2013-01-03
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
     * Create the <textarea> or <span> HTML tag with the
     * attributes for this instance of TextareaField. This
     * makes use of {@link FormField->createTag()} functionality.
     *
     * @return HTML code for the textarea OR span element
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2014
     */
    public function Field() {
        if ($this->readonly) {
            $attributes = array(
                'id' => $this->id(),
                'class' => 'readonly' . ($this->extraClass() ? $this->extraClass() : ''),
                'name' => $this->name,
                'rows' => $this->rows,
                'cols' => $this->cols,
                'tabindex' => $this->getTabIndex(),
                'readonly' => 'readonly'
            );
        
            $placeholder = $this->getPlaceholder();
            if (!empty($placeholder)) {
                $attributes['placeholder'] = $placeholder;
            }

            return $this->createTag(
                'span',
                $attributes,
                (($this->value) ? nl2br(htmlentities($this->value, ENT_COMPAT, 'UTF-8')) : '<i>(' . _t('FormField.NONE', 'none') . ')</i>')
            );
        } else {
            $attributes = array(
                'id' => $this->id(),
                'class' => ($this->extraClass() ? $this->extraClass() : ''),
                'name' => $this->name,
                'rows' => $this->rows,
                'cols' => $this->cols,
                'tabindex' => $this->getTabIndex()
            );
        
            $placeholder = $this->getPlaceholder();
            if (!empty($placeholder)) {
                $attributes['placeholder'] = $placeholder;
            }

            if ($this->disabled) {
                $attributes['disabled'] = 'disabled';
            }

            return $this->createTag('textarea', $attributes, htmlentities($this->value, ENT_COMPAT, 'UTF-8'));
        }
    }

}