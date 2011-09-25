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
 * The Silvercart administration backend.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 01.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShopAdministrationAdmin extends ModelAdmin {
    
    /**
     * Managed models
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public static $managed_models = array(
        'SilvercartOrder' => array(
            'collection_controller' => 'SilvercartOrder_CollectionController'
        ),
        'SilvercartContactMessage' => array(
            'collection_controller' => 'SilvercartContactMessageAdmin_CollectionController'
        ),
        'SilvercartProduct' => array(
            'collection_controller' => 'SilvercartProduct_CollectionController'
        ),
        'SilvercartProductExporter' => array(
            'collection_controller' => 'SilvercartProductExportAdmin_CollectionController',
            'record_controller'     => 'SilvercartProductExportAdmin_RecordController'
        ),
        'SilvercartManufacturer',
        'SilvercartRegularCustomer',
        'SilvercartBusinessCustomer',
        'SilvercartAnonymousCustomer',
        'SilvercartGoogleMerchantTaxonomy' => array(
            'collection_controller' => 'SilvercartGoogleMerchantTaxonomy_CollectionController',
        )
    );
    
    /**
     * Definition of the Importers for the managed model.
     *
     * @var array
     *
     * @author Sascha Koehler
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public static $model_importers = array(
        'SilvercartProduct'                 => 'SilvercartProductCsvBulkLoader',
        'SilvercartManufacturer'            => 'CsvBulkLoader',
        'SilvercartGoogleMerchantTaxonomy'  => 'CsvBulkLoader'
    );
    
    /**
     * The URL segment
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public static $url_segment = 'silvercart-administration';
    
    /**
     * constructor
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartShopAdministrationAdmin.TITLE');
        
        self::$managed_models['SilvercartProductExporter']['title']         = _t('SilvercartProductExport.SINGULAR_NAME');
        self::$managed_models['SilvercartGoogleMerchantTaxonomy']['title']  = _t('SilvercartGoogleMerchantTaxonomy.SINGULAR_NAME');
        
        parent::__construct();
    }
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public function init() {
        parent::init();
        $this->extend('updateInit');
    }
    
    /**
     * Provides a way to use different result tables for the managed models.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public function resultsTableClassName() {
    $className = $this->resultsTableClassName;

    if (isset($this->urlParams['Action']) ) {
        if ($this->urlParams['Action'] == 'SilvercartProduct') {
            $className = 'SilvercartProductTableListField';
        }
        if ($this->urlParams['Action'] == 'SilvercartProductExporter') {
            $className = 'SilvercartProductExportTableListField';
        }
    }

    return $className;
    }
}

/**
 * Modifies the model admin search panel.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 07.07.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductExportAdmin_CollectionController extends ModelAdmin_CollectionController {

    public $showImportForm = false;

    /**
     * Shows results from the "search" action in a TableListField. 
     * 
     * @param string $searchCriteria ???
     *
     * @return Form
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public function ResultsForm($searchCriteria) {
        $form = parent::ResultsForm($searchCriteria);
        $form->setActions(new FieldSet());
        return $form;
    }
}

/**
 * The Silvercart product export record controller.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 06.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductExportAdmin_RecordController extends SilvercartMultiSelectAndOrderField_RecordController {
}

/**
 * Modifies the model admin search panel.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 08.08.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartGoogleMerchantTaxonomy_CollectionController extends ModelAdmin_CollectionController {
    
    public $showImportForm = true;

}

/**
 * Modifies the model admin search panel.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 08.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartContactMessageAdmin_CollectionController extends ModelAdmin_CollectionController {

    public $showImportForm = false;

    /**
     * Disable the creation of SilvercartContactMessage DataObjects.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2011
     */
    public function alternatePermissionCheck() {
        return false;
    }
}
