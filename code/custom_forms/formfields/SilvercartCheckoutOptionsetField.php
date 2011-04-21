<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * A formfield for the payment checkout step that can render additional
 * payment informations.
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 21.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutOptionsetField extends OptionsetField {

	/**
	 * Create a UL tag containing sets of radio buttons and labels.  The IDs are set to
	 * FieldID_ItemKey, where ItemKey is the key with all non-alphanumerics removed.
	 *
	 * @todo Should use CheckboxField FieldHolder rather than constructing own markup.
	 */
	function Field() {
		$odd            = 0;
        $itemIdx        = 0;
		$source         = $this->getSource();
        $items          = array();
        $templateVars   = array(
            'ID'            => $this->id(),
            'extraClass'    => $this->extraClass(),
            'items'         => array()
        );

		foreach($source as $key => $value) {

            $odd        = ($odd + 1) % 2;
            $extraClass = $odd ? "odd" : "even";

            $items['item_'.$itemIdx] = array(
                'ID'        => $this->id() . "_" . ereg_replace('[^a-zA-Z0-9]+','',$key),
                'checked'   => ($key == $this->value),
                'odd'       => $odd,
                'even'      => !$odd,
                'disabled'  => ($this->disabled || in_array($key, $this->disabledItems)),
                'value'     => $key,
                'label'     => $value,
                'name'      => $this->name
            );
            
            $itemIdx++;
		}

        $templateVars['items'] = $items;

		$output = $this->customise($templateVars)->renderWith('SilvercartCheckoutOptionsetField');
        
		return $output;
	}
}
