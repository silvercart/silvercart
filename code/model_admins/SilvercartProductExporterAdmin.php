<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for SilvercartProductExporters
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 16.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductExporterAdmin extends ModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'products';

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-product-exporters';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Silvercart product exporters';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartProductExporter' => array(
            'collection_controller' => 'SilvercartProductExportAdmin_CollectionController',
            'record_controller'     => 'SilvercartProductExportAdmin_RecordController'
        )
    );

    /**
     * Class name of the form field used for the results list.  Overloading this in subclasses
     * can let you customise the results table field.
     * 
     * @var string
     */
    protected $resultsTableClassName = 'SilvercartProductExportTableListField';

    /**
     * Constructor
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartProductExporter.PLURALNAME');
        self::$managed_models['SilvercartProductExporter']['title'] = _t('SilvercartProductExport.SINGULARNAME');
        
        parent::__construct();
    }
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    public function resultsTableClassName() {
        $className = $this->resultsTableClassName;
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

    /**
     * indicates whether to show import form or not
     *
     * @var bool
     */
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
