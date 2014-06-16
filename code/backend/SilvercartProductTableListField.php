<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @todo Check to delete SilvercartProductTableListField.
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
    public static $fieldListCsvSpecial = array(
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
        'SilvercartTaxID'                       => 'SilvercartTaxID',
        'SilvercartProductMirrorGroupIDs'       => 'SilvercartProductMirrorGroupIDs'
    );

    /**
     * Clears the complete CSV field list
     *
     * @return void
     *
     * @since 2013-03-04
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     */
    public static function clearCsvFieldList() {
        self::$fieldListCsvSpecial = array();
    }

    /**
     * Add a new field to the CSV field list
     *
     * @param string $fieldName  The field name
     * @param string $fieldTitle The title of the field
     *
     * @return void
     *
     * @since 2013-03-04
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     */
    public static function addToCsvFieldList($fieldName, $fieldTitle) {
        self::$fieldListCsvSpecial[$fieldName] = $fieldTitle;
    }
    
    /**
     * We have to replace some field contents here to gain real CSV
     * compatibility.
     *
     * @param int &$numColumns Number of columns
     * @param int &$numRows    Number of rows
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.07.2011
     */
    public function generateExportFileData(&$numColumns, &$numRows) {
        
        $separator = $this->csvSeparator;
        $fileData = '';
        $columnData = array();

        if ($this->csvHasHeader) {
            $fileData .= "\"" . implode("\"{$separator}\"", array_values(self::$fieldListCsvSpecial)) . "\"";
            $fileData .= "\n";
        }

        if (isset($this->customSourceItems)) {
            $items = $this->customSourceItems;
        } else {
            $dataQuery = $this->getCsvQuery();
            $items = $dataQuery->execute();
        }
        
        // temporary override to adjust TableListField_Item behaviour
        $this->setFieldFormatting(array());

        if ($items) {
            foreach ($items as $item) {
                
                if (is_array($item)) {
                    $className = isset($item['RecordClassName']) ? $item['RecordClassName'] : $item['ClassName'];
                    $item = new $className($item);
                }

                $fields     = self::$fieldListCsvSpecial;
                $columnData = array();

                if ($fields) {
                    foreach ($fields as $fieldName => $fieldTitle) {
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
