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
class SilvercartProductExporter_Froogle {

    /**
     * Determines wether the given product should be included as CSV row.
     * 
     * @param array $record An array with raw product data.
     * 
     * @return Boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public static function includeRow($record) {
        return true;
    }
    
    /**
     * Returns the value for the link.
     *
     * @return string
     *
     * @param array $record An array with raw product data.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function Link($record) {
        return Director::absoluteURL($this->getDataObj($record)->Link());
    }
    
    /**
     * Returns the value for the condition.
     *
     * @return string
     *
     * @param array $record An array with raw product data.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function GoogleProductCategory($record) {
        return $this->getDataObj($record)->getGoogleTaxonomyCategory();
    }
    
    /**
     * Returns the value for the condition.
     *
     * @return string
     *
     * @param array $record An array with raw product data.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function ShopProductCategory($record) {
        Page::$breadcrumbs_delimiter = ' > ';
        return $this->getDataObj($record)->SilvercartProductGroup()->Breadcrumbs(20, true);
    }
    
    /**
     * Returns the value for the condition.
     *
     * @return string
     *
     * @param array $record An array with raw product data.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function ImageLink($record) {
        if ($this->getDataObj($record)->SilvercartImages()->Count() > 0) {
            return Director::absoluteURL($this->getDataObj($record)->SilvercartImages()->First()->Image()->Link());
        }
    }
    
    /**
     * Returns the value for the condition.
     *
     * @return string
     *
     * @param array $record An array with raw product data.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function Condition($record) {
        $condition = '';
        
        if ($this->getDataObj($record)->SilvercartProductConditionID > 0) {
            $condition = $this->getDataObj($record)->getCondition();
        }
        
        return $condition;
    }
    
    /**
     * Returns the value for the condition.
     *
     * @return string
     *
     * @param array $record An array with raw product data.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function Availability($record) {
        $availabiliy = 'nicht auf lager';
        
        switch ($this->getDataObj($record)->SilvercartAvailabilityStatus()->Code) {
            case 'available':
                $availabiliy = 'auf lager';
                break;
        }
        
        return $availabiliy;
    }
    
    /**
     * Returns the value for the condition.
     *
     * @return string
     *
     * @param array $record An array with raw product data.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function Price($record) {
        return $this->getDataObj($record)->getPrice()->NiceWithShortname();
    }
    
    /**
     * Returns the value for the condition.
     *
     * @return string
     *
     * @param array $record An array with raw product data.
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function Brand($record) {
        return $this->getDataObj($record)->SilvercartManufacturer()->Title;
    }
}
