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
 * @subpackage Customer
 */

/**
 * Decorates the Group class for additional functionality
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 01.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartGroupDecorator extends DataObjectDecorator {
   
    /**
     * Defines relations, attributes and settings for the decorated class.
     *
     * @return array for defining and configuring the decorated class.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.07.2011
     */
    public function extraStatics() {
        return array(
            'belongs_many_many' => array(
                'SilvercartPaymentMethods' => 'SilvercartPaymentMethod'
            )
        );
    }
    
    /**
     * Adds or removes GUI elements for the backend editing mask.
     *
     * @param FieldSet &$fields The original FieldSet
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.01.2012
     */
    public function updateCMSFields(FieldSet &$fields) {
        $fields->addFieldToTab('Root.Members', new TextField('Code', _t('Group.CODE')));
    }
}
