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
 * @subpackage Products
 */

/**
 * This is an example implementation of a callback class for Silvercart product
 * exporters.
 * 
 * The naming scheme for the class is as follows:
 * 
 *      SilvercartProductExporter_{NameOfTheExporter}
 * 
 * where {NameOfTheExporter} is the name you gave the exporter in the
 * storeadmin.
 * 
 * You can write a method for every field you defined in the exporter; just
 * name it exactly like the field.
 *
 * @package Silvercart
 * @subpackage Products
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 07.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductExporter_Kelkoo {

    /**
     * Determines wether the given product should be included as CSV row.
     * 
     * @param SilvercartProduct $product The SilvercartProduct object. You can access every attribute and relation.
     * 
     * @return Boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public function includeRow($product) {
        return true;
    }
    
    /**
     * Treats the field "Title".
     * 
     * @param SilvercartProduct $product    The SilvercartProduct object. You can access every attribute and relation.
     * @param mixed             $fieldValue The original value of the field.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public static function Title($product, $fieldValue) {
        $fieldValue = '* '.$fieldValue.' *';
        
        return $fieldValue;
    }
    
    /**
     * Treats the callback field "CallbackField2".
     * 
     * @param SilvercartProduct $product    The SilvercartProduct object. You can access every attribute and relation.
     * @param mixed             $fieldValue The original value of the field.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public static function CallbackField2($product, $fieldValue) {
        return "Virtual callback field 'callbackField2'";
    }
    
    /**
     * Treats the callback field "MyTestField".
     * 
     * @param SilvercartProduct $product    The SilvercartProduct object. You can access every attribute and relation.
     * @param mixed             $fieldValue The original value of the field.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public static function MyTestField($product, $fieldValue) {
        return "Virtual callback field 'MyTestField' for productID ".$product->ID;
    }
}
