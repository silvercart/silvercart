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
 * The Silvercart product export backend.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 05.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductExportAdmin extends ModelAdmin {

    /**
     * Managed models
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public static $managed_models = array(
        'SilvercartProductExporter'
    );
    
    /**
     * We use our own RecordController class.
     * 
	 * @param string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
	 */
	public static $record_controller_class = "SilvercartProductExportAdmin_RecordController";
    
    /**
     * The URL segment
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public static $url_segment = 'silvercart-product-export';
    
    /**
     * The menu title
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public static $menu_title = 'Silvercart product export';

    /**
     * Set the menu priority for ordering purposes
     * 
     * @var Int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public static $menu_priority = -1;

    /**
     * constructor
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartProductExportAdmin.SILVERCART_PRODUCT_EXPORT_ADMIN_LABEL', 'SilverCart product export');
        parent::__construct();
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
class SilvercartProductExportAdmin_RecordController extends ModelAdmin_RecordController {
    
    /**
     * Adds the abillity to execute additional actions to the model admin's
     * action handling.
     *
     * @param SS_HTTPRequest $request
     * 
     * @return mixed
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function handleAction(SS_HTTPRequest $request) {
        $vars = $request->requestVars();

        if (array_key_exists('doAttributeItems', $vars)) {
            return $this->doAttributeItems($vars);
        } elseif (array_key_exists('doRemoveItems', $vars)) {
            return $this->doRemoveItems($vars);
        } elseif (array_key_exists('doMoveUpItems', $vars)) {
            return $this->doMoveUpItems($vars);
        } elseif (array_key_exists('doMoveDownItems', $vars)) {
            return $this->doMoveDownItems($vars);
        } else {
            return parent::handleAction($request);
        }
    }
    
    /**
     * Attributes fields to the exporter.
     * 
     * @param array $vars The request parameters as associative array
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function doAttributeItems($vars) {
        
        if (isset($vars['ID'])) {
            $exporterObj = DataObject::get_by_id(
                'SilvercartProductExporter',
                Convert::raw2sql($vars['ID'])
            );
            
            if ($exporterObj) {
                if (is_array($vars['availableItems'])) {
                    foreach ($vars['availableItems'] as $field) {
                        
                        if (!$exporterObj->SilvercartProductExporterFields()->find('name', $field)) {
                            $silvercartProductExporterField = new SilvercartProductExporterField();
                            $silvercartProductExporterField->setField('name', $field);
                            $silvercartProductExporterField->setField('SilvercartProductExporterID', $exporterObj->ID);
                            $silvercartProductExporterField->setField('sortOrder', $exporterObj->SilvercartProductExporterFields()->Count() + 1);
                            $silvercartProductExporterField->write();
                            
                            $exporterObj->SilvercartProductExporterFields()->push($silvercartProductExporterField);
                            $exporterObj->write();
                        }
                        
                    }
                }
            }
        }
        
        // Behaviour switched on ajax.
		if(Director::is_ajax()) {
			return $this->edit($request);
		} else {
			Director::redirectBack();
		}
    }
    
    /**
     * Removes fields from the exporter.
     * 
     * @param array $vars The request parameters as associative array
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function doRemoveItems($vars) {
        
        if (isset($vars['ID'])) {
            $exporterObj = DataObject::get_by_id(
                'SilvercartProductExporter',
                Convert::raw2sql($vars['ID'])
            );
            
            if ($exporterObj) {
                if (is_array($vars['selectedItems'])) {
                    foreach ($vars['selectedItems'] as $field) {
                        
                        $itemToDelete = $exporterObj->SilvercartProductExporterFields()->find('name', $field);
                        
                        if ($itemToDelete) {
                            $removedSortOrder = $itemToDelete->sortOrder;
                            
                            foreach ($exporterObj->SilvercartProductExporterFields() as $productExporterField) {
                                if ($productExporterField->sortOrder > $removedSortOrder) {
                                    $productExporterField->setField('sortOrder', $productExporterField->sortOrder - 1);
                                    $productExporterField->write();
                                }
                            }
                            
                            $itemToDelete->delete();
                        }
                    }
                }
            }
        }
        
        // Behaviour switched on ajax.
		if(Director::is_ajax()) {
			return $this->edit($request);
		} else {
			Director::redirectBack();
		}
    }
    
    /**
     * Moves a field up in the sort hierarchy.
     * 
     * @param array $vars The request parameters as associative array
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function doMoveUpItems($vars) {
        
        if (isset($vars['ID'])) {
            $exporterObj = DataObject::get_by_id(
                'SilvercartProductExporter',
                Convert::raw2sql($vars['ID'])
            );
            
            if ($exporterObj) {
                if (is_array($vars['selectedItems'])) {
                    foreach ($vars['selectedItems'] as $field) {
                        
                        $itemToMove = $exporterObj->SilvercartProductExporterFields()->find('name', $field);
                        
                        if ($itemToMove) {
                            if ($itemToMove->sortOrder > 1) {
                                $moveFromPosition = $itemToMove->sortOrder;
                                $moveToPosition   = $moveFromPosition - 1;
                                
                                $this->changePositions($exporterObj, $itemToMove, $moveFromPosition, $moveToPosition);
                            }
                        }
                    }
                }
            }
        }
        
        // Behaviour switched on ajax.
		if(Director::is_ajax()) {
			return $this->edit($request);
		} else {
			Director::redirectBack();
		}
    }
    
    /**
     * Moves a field down in the sort hierarchy.
     * 
     * @param array $vars The request parameters as associative array
     * 
     * @return html
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function doMoveDownItems($vars) {
        
        if (isset($vars['ID'])) {
            $exporterObj = DataObject::get_by_id(
                'SilvercartProductExporter',
                Convert::raw2sql($vars['ID'])
            );
            
            if ($exporterObj) {
                if (is_array($vars['selectedItems'])) {
                    foreach ($vars['selectedItems'] as $field) {
                        
                        $itemToMove = $exporterObj->SilvercartProductExporterFields()->find('name', $field);
                        
                        if ($itemToMove) {
                            if ($itemToMove->sortOrder < $exporterObj->SilvercartProductExporterFields()->Count()) {
                                $moveFromPosition = $itemToMove->sortOrder;
                                $moveToPosition   = $moveFromPosition + 1;
                                
                                $this->changePositions($exporterObj, $itemToMove, $moveFromPosition, $moveToPosition);
                            }
                        }
                    }
                }
            }
        }
        
        // Behaviour switched on ajax.
		if(Director::is_ajax()) {
			return $this->edit($request);
		} else {
			Director::redirectBack();
		}
    }
    
    /**
     * Changes positions of two SilvercartProductExporterFields.
     * 
     * @param SilvercartProductExporter      $exporterObj      The exporter object to which the fields are connected
     * @param SilvercartProductExporterField $itemToMove       The item to move
     * @param int                            $moveFromPosition Position to move the field from
     * @param int                            $moveToPosition   Position to move the field to
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    protected function changePositions(SilvercartProductExporter $exporterObj, SilvercartProductExporterField $itemToMove, $moveFromPosition, $moveToPosition) {
        
        if (!$exporterObj ||
            !$itemToMove) {
            
            return false;
        }
        
        $changerObject = DataObject::get_one(
            'SilvercartProductExporterField',
            sprintf(
                "SilvercartProductExporterID = %d AND sortOrder = %d",
                $exporterObj->ID,
                $moveToPosition
            )
        );

        if ($changerObject) {
            $changerObject->setField('sortOrder', $moveFromPosition);
            $changerObject->write();
            
            $itemToMove->setField('sortOrder', $moveToPosition);
            $itemToMove->write();
        }
    }
}
