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
 * @since 07.07.2011
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartProductExportTableListField extends TableListField {
    
    /**
     * Sets the template to be used for this class.
     * 
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    protected $template = "SilvercartProductExportTableListField";
    
    /**
     * Defines the item class
     * 
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public $itemClass = 'SilvercartProductExportTableListField_Item';
    
    /**
     * Configures the actions for this table field.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public $actions = array(
        'export' => array(
            'label'         => 'Export',
            'icon'          => 'cms/images/network-save-bw.gif',
            'icon_disabled' => 'cms/images/network-save-bw.gif',
            'class'         => 'exportlink'
        ),
        'delete' => array(
            'label'         => 'Delete',
            'icon'          => 'cms/images/delete.gif',
            'icon_disabled' => 'cms/images/delete_disabled.gif',
            'class'         => 'deletelink' 
        )
    );
    
    /**
     * Defines the allowed actions for this table field.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    protected $permissions = array(
        "export",
        "delete",
    );

    /**
     * Handles the request with a modified handler.
     *
     * @param SS_HTTPRequest $request The given HTTP request
     *
     * @return SilvercartUpdateTableListField_ItemRequest
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public function handleItem($request) {
        return new SilvercartProductExportTableListField_ItemRequest($this, $request->param('ID'));
    }

    /**
     * Adds additional Requirements for the SilverCart product export manager.
     * 
     * @param array $properties Properties
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.01.2013
     */
    public function FieldHolder($properties = array()) {
        Requirements::themedCSS('SilvercartProductExportTableListField');
        return parent::FieldHolder($properties);
    }
}

/**
 * A modified TableListField_Item for the SilverCart product export manager.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 07.07.2011
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartProductExportTableListField_Item extends TableListField_Item {

    /**
     * Disable the actions when update status is not 'remaining'.
     *
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public function Actions() {
        $allowedActions = parent::Actions();
        
        return $allowedActions;
    }

    /**
     * Builds and returns the link to trigger the export action.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.07.2011
     */
    public function ExportLink() {
        return Controller::join_links($this->Link(), "export");
    }
}

/**
 * A modified TableListField_ItemRequest for the SilverCart product export
 * manager.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 07.07.2011
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartProductExportTableListField_ItemRequest extends TableListField_ItemRequest {

    /**
     * Provides the export action.
     *
     * @return SilvercartProductExportTableListField
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    public function export() {
        if ($this->ctf->Can('export') !== true) {
            return false;
        }
        $this->dataObj()->doExport();
        
        if (Director::is_ajax()) {
            $collectionController = new SilvercartProductExportAdmin_CollectionController(new SilvercartProductExporterAdmin(), 'SilvercartProductExporter');
            $ResultsForm = $collectionController->ResultsForm(array());
            return $ResultsForm->forAjaxTemplate();
        } else {
            Controller::curr()->redirect($this->Link());
        }
    }
}
