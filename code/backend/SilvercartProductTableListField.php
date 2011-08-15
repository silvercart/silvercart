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
 * @subpackage Backend
 */

/**
 * A modified TableListField for the SilverCart product export manager.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 25.07.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductTableListField extends TableListField {
    
    /**
     * We don't want the HTML tags in some fields to be replaced by
     * Silverstripe's automatic mechanism.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.07.2011
     */
    public $csvFieldFormatting = array(
        "Title"             => '$Title',
        "ShortDescription"  => '$ShortDescription',
        "LongDescription"   => '$LongDescription',
        "MetaTitle"         => '$MetaTitle',
        "MetaDescription"   => '$MetaDescription',
    );
    
    /**
     * The normal fieldListCsv gets overwritten by ModelAdmin, so we use our
     * own definition.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.08.2011
     */
    protected $fieldListCsvSpecial = array(
        'ID'                                    => 'ID',
        'Title'                                 => 'Title',
        'ShortDescription'                      => 'ShortDescription',
        'LongDescription'                       => 'LongDescription',
        'MetaDescription'                       => 'MetaDescription',
        'MetaTitle'                             => 'MetaTitle',
        'MetaKeywords'                          => 'MetaKeywords',
        'ProductNumberShop'                     => 'ProductNumberShop',
        'ProductNumberManufacturer'             => 'ProductNumberManufacturer',
        'PurchasePriceAmount'                   => 'PurchasePriceAmount',
        'PurchasePriceCurrency'                 => 'PurchasePriceCurrency',
        'MSRPriceAmount'                        => 'MSRPriceAmount',
        'MSRPriceCurrency'                      => 'MSRPriceCurrency',
        'PriceGrossAmount'                      => 'PriceGrossAmount',
        'PriceGrossCurrency'                    => 'PriceGrossCurrency',
        'PriceNetAmount'                        => 'PriceNetAmount',
        'PriceNetCurrency'                      => 'PriceNetCurrency',
        'Weight'                                => 'Weight',
        'isFreeOfCharge'                        => 'isFreeOfCharge',
        'EANCode'                               => 'EANCode',
        'isActive'                              => 'isActive',
        'PurchaseMinDuration'                   => 'PurchaseMinDuration',
        'PurchaseMaxDuration'                   => 'PurchaseMaxDuration',
        'PurchaseTimeUnit'                      => 'PurchaseTimeUnit',
        'StockQuantity'                         => 'StockQuantity',
        'StockQuantityOverbookable'             => 'StockQuantityOverbookable',
        'SilvercartProductGroupID'              => 'SilvercartProductGroupID',
        'SilvercartManufacturerID'              => 'SilvercartManufacturerID',
        'SilvercartAvailabilityStatusID'        => 'SilvercartAvailabilityStatusID',
    );
    
    /**
     * We have to replace some field contents here to gain real CSV
     * compatibility.
     *
     * @return void
     *
     * @param 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.07.2011
     */
    function generateExportFileData(&$numColumns, &$numRows) {
        
        $separator = $this->csvSeparator;
        $fileData = '';
        $columnData = array();

        if($this->csvHasHeader) {
            $fileData .= "\"" . implode("\"{$separator}\"", array_values($this->fieldListCsvSpecial)) . "\"";
            $fileData .= "\n";
        }

        if(isset($this->customSourceItems)) {
            $items = $this->customSourceItems;
        } else {
            $dataQuery = $this->getCsvQuery();
            $items = $dataQuery->execute();
        }
        
        // temporary override to adjust TableListField_Item behaviour
        $this->setFieldFormatting(array());

        if($items) {
            foreach($items as $item) {
                
                if(is_array($item)) {
                    $className = isset($item['RecordClassName']) ? $item['RecordClassName'] : $item['ClassName'];
                    $item = new $className($item);
                }

                $fields = $this->fieldListCsvSpecial;
                $columnData = array();

                if($fields) foreach($fields as $fieldName => $fieldTitle) {
                    $value = str_replace(
                        array(
                            "\n",
                            '"'
                        ),
                        array(
                            "<br />",
                            '""'
                        ),
                        $item->$fieldName
                    );

                    $tmpColumnData = '"' . $value . '"';
                    $columnData[] = $tmpColumnData;
                }

                $fileData .= implode($separator, $columnData)."\n";

                $item->destroy();
                unset($item);
            }

            return $fileData;
        } else {
            return null;
        }
    }
}
