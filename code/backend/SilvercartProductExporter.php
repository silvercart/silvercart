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
     */
    protected $objName;
    
    /**
     * Contains the path to the export directory.
     * 
     * @var string
     */
    protected $exportDirectory;
    
    /**
     * Contains the URL to the export directory.
     * 
     * @var string
     */
    protected $exportURL;
    
    /**
     * Character to quote a text with special characters.
     *
     * @var string
     */    
    protected $quoteCharacter = '"';
    
    /**
     * Contains the objects we're operating on.
     *
     * @var array
     */
    protected $dataObjects = array();
    
    /**
     * Attributes
     *
     * @var array
     */
    public static $db = array(
        'isActive'                              => 'Boolean(0)',
        'name'                                  => 'VarChar(255)',
        'selectOnlyProductsWithProductGroup'    => 'Boolean',
        'selectOnlyProductsWithImage'           => 'Boolean',
        'selectOnlyProductsWithManufacturer'    => 'Boolean',
        'selectOnlyProductsWithQuantity'        => 'Boolean',
        'selectOnlyProductsQuantity'            => 'Int',
        'selectOnlyProductsOfRelatedGroups'     => 'Boolean',
        'csvSeparator'                          => 'VarChar(10)',
        'updateInterval'                        => 'Int',
        'updateIntervalPeriod'                  => "Enum('Minutes,Hours,Days,Weeks,Months,Years','Hours')",
        'pushEnabled'                           => 'Boolean',
        'pushToUrl'                             => 'VarChar(255)',
        'activateCsvHeaders'                    => 'Boolean',
        'BreadcrumbDelimiter'                   => 'VarChar(10)',
        'lastExportDateTime'                    => 'SS_Datetime',
        'createTimestampFile'                   => 'Boolean(0)',
        'ProtocolForLinks'                      => "Enum('http,https','http')",
        'BaseUrlForLinks'                       => 'VarChar(255)',
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartCountry' => 'SilvercartCountry',
        'Group'             => 'Group',
    );
    
    /**
     * Has-many relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartProductExporterFields' => 'SilvercartProductExporterField',
    );
    
    /**
     * Many-many relationships.
     *
     * @var array
     */
    public static $many_many = array(
        'SilvercartProductGroupPages' => 'SilvercartProductGroupPage',
    );
    
    /**
     * Default values
     *
     * @var array
     */
    public static $defaults = array(
        'BreadcrumbDelimiter'   => '>',
    );
    
    /**
     * Indicator to detect what http settings to set when switching
     *
     * @var bool
     */
    protected $switchedHttpHostToExport     = false;
    
    /**
     * ssl setting to restore after switching
     *
     * @var string
     */
    protected $server_ssl_to_restore        = null;
    
    /**
     * https setting to restore after switching
     *
     * @var string
     */
    protected $server_https_to_restore      = null;
    
    /**
     * http host setting to restore after switching
     *
     * @var string
     */
    protected $server_http_host_to_restore  = null;
    
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        
        $this->objName          = 'SilvercartProduct';
        $this->exportDirectory  = Director::baseFolder() . '/silvercart/product_exports/';
        $this->exportURL        = Director::absoluteBaseURL() . 'silvercart/product_exports/';
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.02.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'isActive'                              => _t('SilvercartProductExport.IS_ACTIVE'),
                'name'                                  => _t('SilvercartProductExport.FIELD_NAME'),
                'selectOnlyProductsWithProductGroup'    => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_WITH_GOUP'),
                'selectOnlyProductsWithImage'           => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_WITH_IMAGE'),
                'selectOnlyProductsWithManufacturer'    => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_WITH_MANUFACTURER'),
                'selectOnlyProductsWithQuantity'        => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_WITH_QUANTITY'),
                'selectOnlyProductsQuantity'            => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_QUANTITY'),
                'selectOnlyProductsOfRelatedGroups'     => _t('SilvercartProductExport.FIELD_SELECT_ONLY_PRODUCTS_OF_RELATED_GROUPS'),
                'csvSeparator'                          => _t('SilvercartProductExport.FIELD_CSV_SEPARATOR'),
                'updateInterval'                        => _t('SilvercartProductExport.FIELD_UPDATE_INTERVAL'),
                'updateIntervalPeriod'                  => _t('SilvercartProductExport.FIELD_UPDATE_INTERVAL_PERIOD'),
                'pushEnabled'                           => _t('SilvercartProductExport.FIELD_PUSH_ENABLED'),
                'pushToUrl'                             => _t('SilvercartProductExport.FIELD_PUSH_TO_URL'),
                'activateCsvHeaders'                    => _t('SilvercartProductExport.ACTIVATE_CSV_HEADERS'),
                'createTimestampFile'                   => _t('SilvercartProductExport.CREATE_TIMESTAMP_FILE'),
                'BreadcrumbDelimiter'                   => _t('SilvercartProductExport.BREADCRUMB_DELIMITER'),
                'SilvercartCountry'                     => _t('SilvercartCountry.SINGULARNAME'),
                'SilvercartProductExporterFields'       => _t('SilvercartProductExporterField.PLURALNAME'),
                'SilvercartProductGroupPages'           => _t('SilvercartProductGroupPage.PLURALNAME'),
                'ProtocolForLinks'                      => _t('SilvercartProductExport.PROTOCLOFORLINKS'),
                'BaseUrlForLinks'                       => _t('SilvercartProductExport.BASEURLFORLINKS'),
                'Group'                                 => _t('Group.SINGULARNAME'),
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
            'isActive'              => _t('SilvercartProductExport.IS_ACTIVE'),
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    public function getCMSFields($params = null) {
        $fields             = parent::getCMSFields($params);
        $tabset             = new TabSet('Sections');
        $dbFields           = DataObject::database_fields('SilvercartProduct');
        $languageDbFields   = DataObject::database_fields('SilvercartProductLanguage');
        $lastExport         = $this->lastExportDateTime;
        
        if (!$lastExport) {
            $lastExport = '---';
        }
        
        // --------------------------------------------------------------------
        // Basic settings
        // --------------------------------------------------------------------
        $tabBasic = new Tab('Basic', _t('SilvercartProductExportAdmin.TAB_BASIC_SETTINGS', 'Basic settings'));
        $tabset->push($tabBasic);
        
        $fields->dataFieldByName('BreadcrumbDelimiter')->setRightTitle(_t('SilvercartProductExport.BREADCRUMB_DELIMITER_DESCRIPTION'));
        $fields->dataFieldByName('SilvercartCountryID')->setRightTitle(_t('SilvercartProductExport.COUNTRY_DESCRIPTION'));
        $fields->dataFieldByName('SilvercartCountryID')->setSource(SilvercartCountry::getPrioritiveDropdownMap());
        
        if (empty($this->BaseUrlForLinks)) {
            $this->BaseUrlForLinks = $_SERVER['HTTP_HOST'];
            $fields->dataFieldByName('BaseUrlForLinks')->setValue($_SERVER['HTTP_HOST']);
        }
        
        $tabBasic->setChildren(
            new FieldSet(
                $fields->dataFieldByName('isActive'),
                $fields->dataFieldByName('name'),
                $fields->dataFieldByName('csvSeparator'),
                $fields->dataFieldByName('BreadcrumbDelimiter'),
                $fields->dataFieldByName('ProtocolForLinks'),
                $fields->dataFieldByName('BaseUrlForLinks'),
                $fields->dataFieldByName('GroupID'),
                $fields->dataFieldByName('SilvercartCountryID'),
                $fields->dataFieldByName('createTimestampFile'),
                $fields->dataFieldByName('updateInterval'),
                $fields->dataFieldByName('updateIntervalPeriod'),
                $fields->dataFieldByName('pushEnabled'),
                $fields->dataFieldByName('pushToUrl'),
                new LiteralField('lastExportDateTime', '<p>'._t('SilvercartProductExport.FIELD_LAST_EXPORT_DATE_TIME').': '.$lastExport.'</p>'),
                new LiteralField('exportURL', _t('SilvercartProductExporter.URL') . ':<br/><a href="' . $this->getExportFileURL() . '" target="blank">' . $this->getExportFileURL() . '</a>')
            )
        );
        
        // --------------------------------------------------------------------
        // Product selection
        // --------------------------------------------------------------------
        $tabProductSelection = new Tab('ProductSelection', _t('SilvercartProductExportAdmin.TAB_PRODUCT_SELECTION', 'Product selection'));
        $tabset->push($tabProductSelection);
        
        $productGroupHolder                 = SilvercartTools::PageByIdentifierCode('SilvercartProductGroupHolder');
        $silvercartProductGroupPagesField   = new TreeMultiselectField(
                'SilvercartProductGroupPages',
                $this->fieldLabel('SilvercartProductGroupPages'),
                'SiteTree'
        );
        $silvercartProductGroupPagesField->setTreeBaseID($productGroupHolder->ID);
        
        $tabProductSelection->setChildren(
            new FieldSet(
                new HeaderField('selectOnlyHeadline', _t('SilvercartProductExport.FIELD_SELECT_ONLY_HEADLINE'), 2),
                $fields->dataFieldByName('selectOnlyProductsWithProductGroup'),
                $fields->dataFieldByName('selectOnlyProductsWithImage'),
                $fields->dataFieldByName('selectOnlyProductsWithManufacturer'),
                $fields->dataFieldByName('selectOnlyProductsWithQuantity'),
                $fields->dataFieldByName('selectOnlyProductsQuantity'),
                $fields->dataFieldByName('selectOnlyProductsOfRelatedGroups'),
                $silvercartProductGroupPagesField
            )
        );
        
        // --------------------------------------------------------------------
        // Export field definitions
        // --------------------------------------------------------------------
        $tabExportFieldDefinitions = new Tab('ExportFieldDefinitions', _t('SilvercartProductExportAdmin.TAB_EXPORT_FIELD_DEFINITIONS', 'Export field definitions'));
        $tabset->push($tabExportFieldDefinitions);
        
        $availableFields    = array();
        $product            = singleton('SilvercartProduct');
        
        $dbFields['Link']                               = 'Text';
        $dbFields['AbsoluteLink']                       = 'Text';
        $dbFields['SilvercartProductGroupBreadcrumbs']  = 'Text';
        $dbFields['DefaultShippingFee']                 = 'Text';
        
        foreach ($dbFields as $fieldName => $fieldType) {
            $fieldLabelTarget = $fieldName;
            if (substr($fieldName, -2) === 'ID') {
                $fieldLabelTarget = substr($fieldName, 0, -2);
            }
            $availableFields[$fieldName] = $product->fieldLabel($fieldLabelTarget) . ' [' . $fieldName . ']';
        }
        foreach ($languageDbFields as $fieldName => $fieldType) {
            if ($fieldName == 'Locale') {
                continue;
            }
            $availableFields[$fieldName] = $product->fieldLabel($fieldName) . ' [' . $fieldName . ']';
        }
        
        asort($availableFields);
        
        $multiSelect2SideField = new SilvercartMultiSelectAndOrderField(
            $this,
            'SilvercartProductExporterFields',
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
        
        foreach ($exporterFields as $exporterField) {
            if (empty($exporterField->headerTitle)) {
                $headerTitle = $exporterField->name;
            } else {
                $headerTitle = $exporterField->headerTitle;
            }
            
            $fieldLabelTarget = $exporterField->name;
            if (substr($exporterField->name, -2) === 'ID') {
                $fieldLabelTarget = substr($exporterField->name, 0, -2);
            }
            $mappingFieldlabel  = $product->fieldLabel($fieldLabelTarget) . ' [' . $exporterField->name . ']';
            $mappingField       = new TextField('SilvercartProductExporterFields[' . $exporterField->ID . ']', $mappingFieldlabel, $headerTitle);
            $tabHeaderFieldSet->push($mappingField);
        }
        
        $tabHeaderConfiguration->setChildren(
            $tabHeaderFieldSet
        );
        
        return new FieldSet($tabset);
    }
    
    /**
     * Saves ExporterFields on after write
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.07.2012
     */
    protected function onAfterWrite() {
        parent::onAfterWrite();
        if (array_key_exists('SilvercartProductExporterFields', $_POST) &&
            is_array($_POST['SilvercartProductExporterFields'])) {
            foreach ($_POST['SilvercartProductExporterFields'] as $ID => $headerTitle) {
                $exporterField = $this->SilvercartProductExporterFields()->find('ID', $ID);
                if ($exporterField) {
                    $exporterField->headerTitle = $headerTitle;
                    $exporterField->write();
                }
            }
        }
    }

    /**
     * Switches the http settings to export propper product links
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.07.2012
     */
    public function switchHttpHost() {
        if ($this->switchedHttpHostToExport) {
            if (isset($_SERVER['SSL'])) {
                $_SERVER['SSL'] = $this->server_ssl_to_restore;
                if (is_null($this->server_ssl_to_restore)) {
                    unset($_SERVER['SSL']);
                }
            } elseif (!is_null($this->server_ssl_to_restore)) {
                $_SERVER['SSL'] = $this->server_ssl_to_restore;
            }
            if (isset($_SERVER['HTTPS'])) {
                $_SERVER['HTTPS'] = $this->server_https_to_restore;
                if (is_null($this->server_https_to_restore)) {
                    unset($_SERVER['HTTPS']);
                }
            } elseif (!is_null($this->server_https_to_restore)) {
                $_SERVER['HTTPS'] = $this->server_https_to_restore;
            }
            $_SERVER['HTTP_HOST'] = $this->server_http_host_to_restore;
            $this->switchedHttpHostToExport     = false;
            $this->server_ssl_to_restore        = null;
            $this->server_https_to_restore      = null;
            $this->server_http_host_to_restore  = null;
            Director::setBaseURL(null);
        } else {
            $this->server_ssl_to_restore        = null;
            $this->server_https_to_restore      = null;
            $this->server_http_host_to_restore  = $_SERVER['HTTP_HOST'];
            if (isset($_SERVER['SSL'])) {
                $this->server_ssl_to_restore = $_SERVER['SSL'];
            }
            if (isset($_SERVER['HTTPS'])) {
                $this->server_https_to_restore = $_SERVER['HTTPS'];
            }
            if ($this->ProtocolForLinks == 'https') {
                $_SERVER['SSL']     = true;
                $_SERVER['HTTPS']   = 'on';
            } else {
                if (isset($_SERVER['SSL'])) {
                    unset($_SERVER['SSL']);
                }
                if (isset($_SERVER['HTTPS'])) {
                    unset($_SERVER['HTTPS']);
                }
            }
            $_SERVER['HTTP_HOST']           = $this->BaseUrlForLinks;
            Director::setBaseURL('/');
            $this->switchedHttpHostToExport = true;
        }
    }
    
    /**
     * Create an export file.
     *
     * @param Int $exportTimeStamp The timestamp to use as last
     *                                       export date.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.02.2013
     */
    public function doExport($exportTimeStamp = null) {
        $this->switchHttpHost();
        $obj        = singleton($this->objName);
        if ($obj->hasExtension('SilvercartDataObjectMultilingualDecorator')) {
            $selectionFields = array();
            foreach (SilvercartProductLanguage::$db as $fieldName => $fieldType) {
                $selectionFields[] = sprintf(
                        "`%s`.`%s`",
                        $this->objName . 'Language',
                        $fieldName
                );
            }
            $query = sprintf("
                SELECT
                    %s.*,
                    %s
                FROM
                    `%s`
                LEFT JOIN
                    `%s`
                    ON (`%s`.`ID` = `%s`.`%sID`)
                WHERE
                    `%s`.`Locale` = '%s'
                    AND (%s)
                ",
                $this->objName,
                implode(',', $selectionFields),
                $this->objName,
                $this->objName . 'Language',
                $this->objName,
                $this->objName . 'Language',
                $this->objName,
                $this->objName . 'Language',
                Translatable::get_current_locale(),
                $this->getSqlFilter()
            );
        } else {
            $query = sprintf("
                SELECT
                    *
                FROM
                    `%s`
                WHERE
                    %s
                ",
                $this->objName,
                $this->getSqlFilter()
            );
        }
        $records    = DB::query($query);
        
        if ($records) {
            file_put_contents($this->getExportFilePath(), '');
            if ($this->activateCsvHeaders) {
                file_put_contents($this->getExportFilePath(), $this->getHeaderRow(), FILE_APPEND);
            }

            foreach ($records as $record) {
                $exportFields   = $this->SilvercartProductExporterFields();
                $amount         = null;
                if ($exportFields->find('name', 'PriceGrossAmount')) {
                    $amount = $record['PriceGrossAmount'];
                } elseif ($exportFields->find('name', 'PriceNetAmount')) {
                    $amount = $record['PriceNetAmount'];
                }
                $group              = null;
                if ($this->GroupID > 0) {
                    $group = $this->Group();
                }
                $product            = new SilvercartProduct($record);
                $defaultShippingFee = $product->getDefaultShippingFee($this->SilvercartCountry(), $group);
                if ($defaultShippingFee) {
                    $defaultShippingFeePriceAmount = $defaultShippingFee->getPriceAmount(true, $amount, $this->SilvercartCountry());
                } else {
                    $defaultShippingFeePriceAmount = 0;
                }
                $record['Link']                                 = $product->Link();
                $record['AbsoluteLink']                         = $product->AbsoluteLink();
                $record['SilvercartProductGroupBreadcrumbs']    = $product->getSilvercartProductGroupBreadcrumbs(true, $this->BreadcrumbDelimiter);
                $record['DefaultShippingFee']                   = $defaultShippingFeePriceAmount;
                file_put_contents($this->getExportFilePath(), $this->getCsvRowFromRecord($record), FILE_APPEND);
            }
        }
        
        if (!$exportTimeStamp) {
            $exportTimeStamp = time();
        }
        $this->setField('lastExportDateTime', date('Y-m-d H:i:s', $exportTimeStamp));
        $this->write();
        
        // Create timestamp file according to configuration
        if ($this->createTimestampFile) {
            file_put_contents($this->getTimeStampFilePath(), $exportTimeStamp);
        }
        $this->switchHttpHost();
    }
    
    /**
     * Returns the export file path
     *
     * @return string
     */
    public function getExportFilePath() {
        return $this->exportDirectory . $this->name . '.csv';
    }
    
    /**
     * Returns the export file URL
     *
     * @return string
     */
    public function getExportFileURL() {
        return $this->exportURL . $this->name . '.csv';
    }
    
    /**
     * Returns the export file path
     *
     * @return string
     */
    public function getTimeStampFilePath() {
        return $this->exportDirectory . $this->name . '_timestamp.txt';
    }
    
    /**
     * Returns the export file URL
     *
     * @return string
     */
    public function getTimeStampFileURL() {
        return $this->exportURL . $this->name . '_timestamp.txt';
    }

    /**
     * Returns the filter for the SQL query as string.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    protected function getSqlFilter() {
        $filter = "`" . $this->objName . "`.`isActive` = 1";
        
        if ($this->selectOnlyProductsWithProductGroup) {
            $filter .= " AND `" . $this->objName . "`.`SilvercartProductGroupID` > 0";
        }
        if ($this->selectOnlyProductsWithImage) {
            
        }
        if ($this->selectOnlyProductsWithManufacturer) {
            $filter .= " AND `" . $this->objName . "`.`SilvercartManufacturerID` > 0";
        }
        if ($this->selectOnlyProductsWithQuantity) {
            $filter .= sprintf(
                " AND `" . $this->objName . "`.`Quantity` > %d",
                $this->selectOnlyProductsQuantity
            );
        }
        if ($this->selectOnlyProductsOfRelatedGroups &&
            $this->SilvercartProductGroupPages()->Count() > 0) {
            $productGroups  = $this->SilvercartProductGroupPages();
            $productIDs     = array();
            foreach ($productGroups as $productGroup) {
                $products   = $productGroup->getProducts(false, false, true);
                $productIDs = array_merge(
                        $productIDs,
                        $products->map('ID','ID')
                );
            }
            if (count($productIDs) > 0) {
                $filter .= sprintf(
                    " AND `" . $this->objName . "`.`ID` IN (%s)",
                    implode(',', $productIDs)
                );
            }
        }
        
        return $filter;
    }
    
    /**
     * Returns a string in CSV format from the given product's data.
     *
     * @param SilvercartProduct $record The product to extract the data from
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2013
     */
    protected function getCsvRowFromRecord($record) {
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
                // use eval instead of generic static method call to keep
                // compatible with PHP version < 5.3
                // eval is dirty, but it works for older versions...
                $includeRow = eval('return ' . $callbackClass . '::includeRow($record);');
            }
        }
        
        if ($includeRow) {
            foreach ($exportFields as $exportField) {
                $fieldValue = '';
                if ($exportField->isCallbackField) {
                    $fieldValue = '';
                    if (array_key_exists('ID', $record)) {
                        $product = DataObject::get_by_id('SilvercartProduct', $record['ID']);
                        if ($product) {
                            $methodName     = $exportField->name;
                            $methodParam    = null;
                            if (strpos($exportField->name, '__')) {
                                list(
                                        $methodName,
                                        $methodParam
                                ) = explode('__', $exportField->name);
                            }
                            if ($methodName == 'DefaultShippingFee') {
                                $amount = null;
                                if ($exportFields->find('name', 'PriceGrossAmount')) {
                                    $amount = $product->PriceGrossAmount;
                                } elseif ($exportFields->find('name', 'PriceNetAmount')) {
                                    $amount = $product->PriceNetAmount;
                                }
                                $defaultShippingFee = $product->getDefaultShippingFee($this->SilvercartCountry());
                                if ($defaultShippingFee) {
                                    $fieldValue = $defaultShippingFee->getPriceAmount(true, $amount, $this->SilvercartCountry());
                                } else {
                                    $fieldValue = 0;
                                }
                            } else {
                                if ($product) {
                                    if ($product->hasMethod($methodName)) {
                                        if (is_null($methodParam)) {
                                            $fieldValue = $product->{$methodName}();
                                        } else {
                                            $fieldValue = $product->{$methodName}($methodParam);
                                        }
                                    } else {
                                        if (strpos($methodName, '.') !== false) {
                                            $parts  = explode('.', $methodName);
                                            $result = $product;
                                            foreach ($parts as $part) {
                                                $result = $result->{$part}();
                                            }
                                            $fieldValue = $result;
                                        } else {
                                            $fieldValue = @$product->{$methodName};
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (array_key_exists('ID', $record)) {
                        $product        = DataObject::get_by_id('SilvercartProduct', $record['ID']);
                        $propertyName   = $exportField->name;
                        if ($product &&
                            substr($propertyName, -2) == 'ID' &&
                            is_numeric($product->{$propertyName}) &&
                            class_exists(substr($propertyName, 0, -2)) &&
                            singleton(substr($propertyName, 0, -2)) instanceof DataObject) {
                            $className  = substr($propertyName, 0, -2);
                            $relation   = DataObject::get_by_id($className, $product->{$propertyName});
                            if ($relation) {
                                $fieldValue = $relation->Title;
                            }
                        }
                    }
                    if (empty($fieldValue)) {
                        $fieldValue = $record[$exportField->name];
                    }
                }

                // If a callback class and method exist for this exporter and field
                // we use it's return value as field value.
                if (class_exists($callbackClass)) {
                    $callbackMethod = $exportField->name;
                    if ($callbackReflectionClass->hasMethod($callbackMethod)) {
                        // use eval instead of generic static method call to keep
                        // compatible with PHP version < 5.3
                        // eval is dirty, but it works for older versions...
                        //$fieldValue = $callbackClass::$callbackMethod($productObj, $fieldValue);
                        $fieldValue = eval('return ' . $callbackClass . '::' . $callbackMethod . '($record, $fieldValue);');
                    }
                }

                $rowElements[]  = $this->quoteValue($fieldValue);
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
    
    /**
     * Quote a field value according to the csv specifications.
     *
     * @param mixed $value ???
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    protected function quoteValue($value) {
        $value = str_replace(
            $this->quoteCharacter,
            $this->quoteCharacter.$this->quoteCharacter,
            (string) $value
        );
        
        $value = $this->quoteCharacter.$value.$this->quoteCharacter;
        
        return $value;
    }
    
    /**
     * Returns the DataObject for the current record
     *
     * @param DataObject $record ???
     * 
     * @return DataObject
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.08.2011
     */
    public function getDataObj($record) {
        if (!array_key_exists('Record'.$record['ID'], $this->dataObjects)) {
            $dataObject = DataObject::get_by_id(
                $record['ClassName'],
                $record['ID']
            );

            $this->dataObjects['Record'.$record['ID']] = $dataObject;
        }
        
        return $this->dataObjects['Record'.$record['ID']];
    }
}
