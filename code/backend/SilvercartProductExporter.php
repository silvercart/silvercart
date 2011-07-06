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
        'csvSeparator'                          => 'VarChar(10)',
        'updateInterval'                        => 'Int',
        'updateIntervalPeriod'                  => "Enum('Minutes,Hours,Days,Weeks,Months,Years','Hours')",
        'pushEnabled'                           => 'Boolean',
        'pushToUrl'                             => 'VarChar(255)',
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
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function init() {
        $this->obj = 'SilvercartProduct';
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
                'csvSeparator'                          => _t('SilvercartProductExport.FIELD_CSV_SEPARATOR'),
                'updateInterval'                        => _t('SilvercartProductExport.FIELD_UPDATE_INTERVAL'),
                'updateIntervalPeriod'                  => _t('SilvercartProductExport.FIELD_UPDATE_INTERVAL_PERIOD'),
                'pushEnabled'                           => _t('SilvercartProductExport.FIELD_PUSH_ENABLED'),
                'pushToUrl'                             => _t('SilvercartProductExport.FIELD_PUSH_TO_URL')
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
            'updateInterval'        => _t('SilvercartProductExportAdmin.UPDATE_INTERVAL_LABEL'),
            'updateIntervalPeriod'  => _t('SilvercartProductExportAdmin.UPDATE_INTERVAL_PERIOD_LABEL'),
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
        $fields = parent::getCMSFields($params);
        $tabset = new TabSet('Sections');
        
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
                $fields->dataFieldByName('pushToUrl')
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
                $fields->dataFieldByName('selectOnlyProductsWithManufacturer')
            )
        );
        
        // --------------------------------------------------------------------
        // Header configuration
        // --------------------------------------------------------------------
        $tabHeaderConfiguration = new Tab('HeaderConfiguration', _t('SilvercartProductExportAdmin.TAB_HEADER_CONFIGURATION', 'Header configuration'));
        $tabset->push($tabHeaderConfiguration);
        
        $tabHeaderConfiguration->setChildren(
            new FieldSet(
            )
        );
        
        // --------------------------------------------------------------------
        // Export field definitions
        // --------------------------------------------------------------------
        $tabExportFieldDefinitions = new Tab('ExportFieldDefinitions', _t('SilvercartProductExportAdmin.TAB_EXPORT_FIELD_DEFINITIONS', 'Export field definitions'));
        $tabset->push($tabExportFieldDefinitions);
        
        $attributedFields   = array();
        $availableFields    = array();
        $dbFields           = DataObject::database_fields('SilvercartProduct');
        
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
}
