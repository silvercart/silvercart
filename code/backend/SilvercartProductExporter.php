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
 * This product exporter class contains all information needed to create an
 * export file for price / product portals.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 05.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductExporter extends DataObject {

    /**
     * Contains the name of the object of the exporter. Typically this would
     * be 'SilvercartProduct'.
     * 
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    protected $obj;
    
    /**
     * Contains the path to the export directory.
     * 
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    protected $exportDirectory;
    
    /**
     * singular name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public static $singular_name = "Silvercart product exporter";

    /**
     * plural name for backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public static $plural_name = "Silvercart product exporters";
    
    /**
     * Attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public static $db = array(
        'name'                                  => 'VarChar(255)',
        'selectOnlyProductsWithProductGroup'    => 'Boolean',
        'selectOnlyProductsWithImage'           => 'Boolean',
        'selectOnlyProductsWithManufacturer'    => 'Boolean',
        'selectOnlyProductsWithQuantity'        => 'Boolean',
        'selectOnlyProductsQuantity'            => 'Int',
        'csvSeparator'                          => 'VarChar(10)',
        'updateInterval'                        => 'Int',
        'updateIntervalPeriod'                  => "Enum('Minutes,Hours,Days,Weeks,Months,Years','Hours')",
        'pushEnabled'                           => 'Boolean',
        'pushToUrl'                             => 'VarChar(255)',
        'activateCsvHeaders'                    => 'Boolean',
        'lastExportDateTime'                    => 'SS_Datetime'
    );
    
    /**
     * Has-many relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public static $has_many = array(
        'SilvercartProductExporterFields' => 'SilvercartProductExporterField'
    );
    
    /**
     * We initialise the obj variable here.
     * 
     * @param array|null $record      This will be null for a new database record.  Alternatively, you can pass an array of field values.
     *                                Normally this contructor is only used by the internal systems that get objects from the database.
	 * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.
     *                                Singletons don't have their defaults set.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        
        $this->obj              = 'SilvercartProduct';
        $this->exportDirectory  = Director::baseFolder().'/silvercart/product_exports/';
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'name'                                  => _t('SilvercartProductExport.FIELD_NAME'),
                'selectOnlyProductsWithProductGroup'    => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_WITH_GOUP'),
                'selectOnlyProductsWithImage'           => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_WITH_IMAGE'),
                'selectOnlyProductsWithManufacturer'    => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_WITH_MANUFACTURER'),
                'selectOnlyProductsWithQuantity'        => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_WITH_QUANTITY'),
                'selectOnlyProductsQuantity'            => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_QUANTITY'),
                'csvSeparator'                          => _t('SilvercartProductExport.FIELD_CSV_SEPARATOR'),
                'updateInterval'                        => _t('SilvercartProductExport.FIELD_UPDATE_INTERVAL'),
                'updateIntervalPeriod'                  => _t('SilvercartProductExport.FIELD_UPDATE_INTERVAL_PERIOD'),
                'pushEnabled'                           => _t('SilvercartProductExport.FIELD_PUSH_ENABLED'),
                'pushToUrl'                             => _t('SilvercartProductExport.FIELD_PUSH_TO_URL'),
                'activateCsvHeaders'                    => _t('SilvercartProductExport.ACTIVATE_CSV_HEADERS')
            )
        );
        
        return $fieldLabels;
    }
    
    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 05.07.2011
     */
    public function searchableFields() {
        $searchableFields = array(
            'updateIntervalPeriod'  => array(
                'title'     => _t('SilvercartProductExportAdmin.UPDATE_INTERVAL_PERIOD_LABEL'),
                'filter'    => 'ExactMatchFilter'
            ),
            'pushEnabled' => array(
                'title'     => _t('SilvercartProductExportAdmin.PUSH_ENABLED_LABEL'),
                'filter'    => 'ExactMatchFilter'
            )
        );
        
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'name'                  => _t('SilvercartProductExport.FIELD_NAME'),
            'updateInterval'        => _t('SilvercartProductExportAdmin.UPDATE_INTERVAL_LABEL'),
            'updateIntervalPeriod'  => _t('SilvercartProductExportAdmin.UPDATE_INTERVAL_PERIOD_LABEL'),
            'lastExportDateTime'    => _t('SilvercartProductExport.FIELD_LAST_EXPORT_DATE_TIME'),
            'pushEnabled'           => _t('SilvercartProductExportAdmin.PUSH_ENABLED_LABEL'),
            'csvSeparator'          => _t('SilvercartProductExportAdmin.CSV_SEPARATOR_LABEL')
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Sets the GUI for the storeadmin..
     *
     * @param array $params See {@link scaffoldFormFields()}
     *
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function getCMSFields($params = null) {
        $fields     = parent::getCMSFields($params);
        $tabset     = new TabSet('Sections');
        $dbFields   = DataObject::database_fields('SilvercartProduct');
        $lastExport = $this->lastExportDateTime;
        
        if (!$lastExport) {
            $lastExport = '---';
        }
        
        // --------------------------------------------------------------------
        // Basic settings
        // --------------------------------------------------------------------
        $tabBasic = new Tab('Basic', _t('SilvercartProductExportAdmin.TAB_BASIC_SETTINGS', 'Basic settings'));
        $tabset->push($tabBasic);
        
        $tabBasic->setChildren(
            new FieldSet(
                $fields->dataFieldByName('name'),
                $fields->dataFieldByName('csvSeparator'),
                $fields->dataFieldByName('updateInterval'),
                $fields->dataFieldByName('updateIntervalPeriod'),
                $fields->dataFieldByName('pushEnabled'),
                $fields->dataFieldByName('pushToUrl'),
                new LiteralField('lastExportDateTime', '<p>'._t('SilvercartProductExport.FIELD_LAST_EXPORT_DATE_TIME').': '.$lastExport.'</p>')
            )
        );
        
        // --------------------------------------------------------------------
        // Product selection
        // --------------------------------------------------------------------
        $tabProductSelection = new Tab('ProductSelection', _t('SilvercartProductExportAdmin.TAB_PRODUCT_SELECTION', 'Product selection'));
        $tabset->push($tabProductSelection);
        
        $tabProductSelection->setChildren(
            new FieldSet(
                new HeaderField('selectOnlyHeadline', _t('SilvercartProductExport.FIELD_SELECT_ONLY_HEADLINE'), 2),
                $fields->dataFieldByName('selectOnlyProductsWithProductGroup'),
                $fields->dataFieldByName('selectOnlyProductsWithImage'),
                $fields->dataFieldByName('selectOnlyProductsWithManufacturer'),
                $fields->dataFieldByName('selectOnlyProductsWithQuantity'),
                $fields->dataFieldByName('selectOnlyProductsQuantity')
            )
        );
        
        // --------------------------------------------------------------------
        // Header configuration
        // --------------------------------------------------------------------
        $tabHeaderConfiguration = new Tab('HeaderConfiguration', _t('SilvercartProductExportAdmin.TAB_HEADER_CONFIGURATION', 'Header configuration'));
        $tabset->push($tabHeaderConfiguration);
        
        $tabHeaderFieldSet = new FieldSet();
        
        $tabHeaderFieldSet->push(
            $fields->dataFieldByName('activateCsvHeaders')
        );
        
        // Create exporterField list
        $exporterFields = $this->SilvercartProductExporterFields();
        $exporterFields->sort('sortOrder', 'ASC');
        
        foreach($exporterFields as $exporterField) {
            if (empty($exporterField->headerTitle)) {
                $headerTitle = $exporterField->name;
            } else {
                $headerTitle = $exporterField->headerTitle;
            }
            
            $mappingField = new TextField('SilvercartProductExporterFields_'.$exporterField->name, $exporterField->name, $headerTitle);
            $tabHeaderFieldSet->push($mappingField);
        }
        
        $tabHeaderConfiguration->setChildren(
            $tabHeaderFieldSet
        );
        
        // --------------------------------------------------------------------
        // Export field definitions
        // --------------------------------------------------------------------
        $tabExportFieldDefinitions = new Tab('ExportFieldDefinitions', _t('SilvercartProductExportAdmin.TAB_EXPORT_FIELD_DEFINITIONS', 'Export field definitions'));
        $tabset->push($tabExportFieldDefinitions);
        
        $attributedFields   = array();
        $availableFields    = array();
        
        foreach($dbFields as $fieldName => $fieldType) {
            $availableFields[$fieldName] = $fieldName;
        }
        
        $multiSelect2SideField = new SilvercartMultiSelectAndOrderField(
            $this->ID,
            'availableExportFields',
            _t('SilvercartProductExport.FIELD_AVAILABLE_EXPORT_FIELDS'),
            $availableFields,
            '',
            10,
            true
        );
        
        $tabExportFieldDefinitions->setChildren(
            new FieldSet(
                new HeaderField(
                    'attributeExportFieldsLabel',
                    _t('SilvercartProductExport.ATTRIBUTE_EXPORT_FIELDS_LABEL'),
                    2
                ),
                $multiSelect2SideField
            )
        );
        
        return new FieldSet($tabset);
    }
    
    /**
     * Create an export file.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public function doExport() {
        $fileName = $this->name.'.csv';
        $products = DataObject::get(
            $this->obj,
            $this->getSqlFilter()
        );
        
        if ($fp = fopen($this->exportDirectory.$fileName, 'w')) {
            
            if ($this->activateCsvHeaders) {
                fwrite($fp, $this->getHeaderRow());
            }
            
            if ($products) {
                foreach ($products as $product) {
                    fwrite($fp, $this->getCsvRowFromProduct($product));
                }
            }
            fclose($fp);
        }
        
        $this->setField('lastExportDateTime', date('Y-m-d H:i:s'));
        $this->write();
    }
    
    /**
     * Returns the filter for the SQL query as string.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    protected function getSqlFilter() {
        $filter = "isActive = 1";
        
        if ($this->selectOnlyProductsWithProductGroup) {
            $filter .= " AND SilvercartProductGroupID > 0";
        }
        if ($this->selectOnlyProductsWithImage) {
            
        }
        if ($this->selectOnlyProductsWithManufacturer) {
            $filter .= " AND SilvercartManufacturerID > 0";
        }
        if ($this->selectOnlyProductsWithQuantity) {
            $filter .= sprintf(
                " AND Quantity > %d",
                $this->selectOnlyProductsQuantity
            );
        }
        
        return $filter;
    }
    
    /**
     * Returns a string in CSV format from the given product's data.
     *
     * @param SilvercartProduct $productObj The product to extract the data from
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    protected function getCsvRowFromProduct($productObj) {
        $includeRow     = true;
        $rowElements    = array();
        $row            = '';
        $callbackClass  = $this->class.'_'.$this->name;
        
        if (class_exists($callbackClass)) {
            $callbackReflectionClass = new ReflectionClass($callbackClass);
        }
        
        $exportFields = $this->SilvercartProductExporterFields();
        $exportFields->sort('sortOrder', 'ASC');
        
        if (class_exists($callbackClass)) {
            if ($callbackReflectionClass->hasMethod('includeRow')) {
                $includeRow = $callbackClass::includeRow($productObj);
            }
        }
        
        if ($includeRow) {
            foreach ($exportFields as $exportField) {
                if ($exportField->isCallbackField) {
                    $fieldValue = '';
                } else {
                    $fieldValue = $productObj->getField($exportField->name);
                }

                // If a callback class and method exist for this exporter and field
                // we use it's return value as field value.
                if (class_exists($callbackClass)) {
                    $callbackMethod = $exportField->name;
                    if ($callbackReflectionClass->hasMethod($callbackMethod)) {
                        $fieldValue = $callbackClass::$callbackMethod($productObj, $fieldValue);
                    }
                }

                $rowElements[]  = $fieldValue;
            }
        }
        
        $row  = implode($this->getPreparedCsvSeparator(), $rowElements);
        $row .= "\n";
        
        return $row;
    }
    
    /**
     * Returns a string in CSV format that is used as header.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    protected function getHeaderRow() {
        $rowElements = array();
        $row         = '';
        
        $exportFields = $this->SilvercartProductExporterFields();
        $exportFields->sort('sortOrder', 'ASC');
        
        foreach ($exportFields as $exportField) {
            if (empty($exportField->headerTitle)) {
                $headerTitle = $exportField->name;
            } else {
                $headerTitle = $exportField->headerTitle;
            }
            $rowElements[] = $headerTitle;
        }
        
        $row  = implode($this->getPreparedCsvSeparator(), $rowElements);
        $row .= "\n";
        
        return $row;
    }
    
    /**
     * Returns the separator for the CSV fields.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    protected function getPreparedCsvSeparator() {
        $separator = $this->csvSeparator;
        
        if ($separator == '\t') {
            $separator = "\t";
        }
        
        return $separator;
    }
}
