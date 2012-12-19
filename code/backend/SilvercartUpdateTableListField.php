<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * A modified TableListField for the SilverCart Update Manager.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.03.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdateTableListField extends TableListField {

    /**
     * Template to use
     * 
     * @var $template string Template-Overrides
     */
    protected $template = "SilvercartUpdateTableListField";
    
    /**
     * Class name for the items
     *
     * @var string
     */
    public $itemClass = 'SilvercartUpdateTableListField_Item';

    /**
     * Available actions
     *
     * @var array
     */
    public $actions = array(
            'update' => array(
                    'label' => 'Update',
                    'icon' => 'cms/images/arrow_refresh.gif',
                    'icon_disabled' => 'cms/images/arrow_refresh.gif',
                    'class' => 'updatelink'
            )
    );
    
    /**
     * Allowed permissions
     *
     * @var array
     */
    protected $permissions = array(
            "update",
    );

    /**
     * Handles the request with a modified handler.
     *
     * @param SS_HTTPRequest $request The given HTTP request
     *
     * @return SilvercartUpdateTableListField_ItemRequest
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function handleItem($request) {
        return new SilvercartUpdateTableListField_ItemRequest($this, $request->param('ID'));
    }

    /**
     * Adds additional Requirements for SilverCart Update Manager.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function FieldHolder() {
        Requirements::themedCSS('SilvercartUpdateTableListField');
        return parent::FieldHolder();
    }
}

/**
 * A modified TableListField_Item for the SilverCart Update Manager.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.03.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdateTableListField_Item extends TableListField_Item {

    /**
     * Disable the actions when update status is not 'remaining'.
     *
     * @return DataObjectSet
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function Actions() {
        if ($this->item->Status == 'remaining') {
            return parent::Actions();
        }
        $allowedActions = new DataObjectSet();

        $allowedActions->push(new ArrayData(array(
                'Name' => 'finished',
                'Link' => 'javascript:;',
                'Icon' => 'cms/images/check.png',
                'IconDisabled' => 'cms/images/check.png',
                'Label' => 'Finished',
                'Class' => '',
                'Default' => '',
                'IsAllowed' => false,
        )));

        return $allowedActions;
    }

    /**
     * Builds and returns the link to trigger the update action.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function UpdateLink() {
        return Controller::join_links($this->Link(), "update");
    }

    /**
     * Returns the updates status as highlight class name.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function HighlightClasses() {
        return $this->item->Status;
    }
}

/**
 * A modified TableListField_ItemRequest for the SilverCart Update Manager.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.03.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdateTableListField_ItemRequest extends TableListField_ItemRequest {

    /**
     * Proveides the update action.
     *
     * @return SilvercartUpdateTableListField
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function update() {
        if ($this->ctf->Can('update') !== true) {
            return false;
        }
        $this->dataObj()->doUpdate();
        
        if (Director::is_ajax()) {
            $collectionController = new SilvercartUpdateAdmin_CollectionController(new SilvercartUpdateAdmin(), 'SilvercartUpdate');
            $ResultsForm = $collectionController->ResultsForm(array());
            return $ResultsForm->forAjaxTemplate();
        } else {
            Director::redirect($this->owner->Link());
        }
    }
}