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
 * @subpackage Backend
 */

/**
 * Decorator for TableListField
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 18.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTableListFieldDecorator extends DataObjectDecorator {
    
    /**
     * Adds the print action to the decorated TableListField
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012 
     */
    public function addPrintAction() {
        $this->owner->actions = array_merge(
                $this->owner->actions,
                array(
                    'printDataObject' => array(
                            'label'         => _t('Silvercart.PRINT'),
                            'icon'          => 'silvercart/images/icons/16x16_print.png',
                            'icon_disabled' => 'silvercart/images/icons/16x16_print.png',
                            'class'         => 'printlink'
                    )
                )
        );
        $permissions = array_merge(
                $this->owner->getPermissions(),
                array(
                    'printDataObject',
                )
        );
        $this->owner->setPermissions($permissions);
    }
    
}

/**
 * Decorator for TableListField_Item
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 18.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTableListField_ItemDecorator extends DataObjectDecorator {

    /**
     * Returns the link to trigger the copy action
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.03.2011
     */
    public function PrintDataObjectLink() {
        return Controller::join_links($this->owner->Link(), "printDataObject");
    }

}

/**
 * Decorates the default TableListField_ItemRequest to inject some custom Actions.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 18.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTableListField_ItemRequestDecorator extends DataObjectDecorator {
    
    public static $allowed_actions = array(
        'printDataObject',
    );

    /**
     * Proveides the copy action.
     *
     * @return SilvercartUpdateTableListField
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function printDataObject() {
        if ($this->owner->ctf->Can('printDataObject') !== true) {
            return false;
        }
        if (Director::is_ajax()) {
            $collectionController = Controller::curr();
            $ResultsForm = $collectionController->ResultsForm(array());
            $additionalJavaScript   = sprintf(
                    "<script language=\"javascript\">window.open('%s');</script>",
                    SilvercartPrint::getPrintURL($this->owner->dataObj())
            );
            return $ResultsForm->forAjaxTemplate() . $additionalJavaScript;
        } else {
            Director::redirect($this->owner->Link());
        }
    }

}