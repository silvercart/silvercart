<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 07.07.2011
 * @license see license file in modules root directory
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
     * @param array $record An array with raw product data.
     *
     * @return string
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
     * @param array $record An array with raw product data.
     *
     * @return string
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
     * @param array $record An array with raw product data.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function ShopProductCategory($record) {
        return $this->getDataObj($record)->SilvercartProductGroup()->Breadcrumbs(20, true);
    }
    
    /**
     * Returns the value for the condition.
     *
     * @param array $record An array with raw product data.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function ImageLink($record) {
        if ($this->getDataObj($record)->SilvercartImages()->count() > 0) {
            return Director::absoluteURL($this->getDataObj($record)->SilvercartImages()->first()->Image()->Link());
        }
    }
    
    /**
     * Returns the value for the condition.
     *
     * @param array $record An array with raw product data.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function Condition($record) {
        $condition = $this->getDataObj($record)->getCondition();
        
        return $condition;
    }
    
    /**
     * Returns the value for the condition.
     *
     * @param array $record An array with raw product data.
     *
     * @return string
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
     * @param array $record An array with raw product data.
     *
     * @return string
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
     * @param array $record An array with raw product data.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 08.08.2011
     */
    public function Brand($record) {
        return $this->getDataObj($record)->SilvercartManufacturer()->Title;
    }
}
