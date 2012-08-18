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
 * The Silvercart configuration backend.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.03.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdateAdmin extends ModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'config';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 140;

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSection = 'maintenance';

    /**
     * Managed models
     *
     * @var array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public static $managed_models = array(
        'SilvercartUpdate',
    );
    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-update';
    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Silvercart Updates';
    /**
     * The collection controller class to use for the shop configuration.
     *
     * @var string
     */
    public static $collection_controller_class = 'SilvercartUpdateAdmin_CollectionController';
    
    protected $resultsTableClassName = 'SilvercartUpdateTableListField';

    public static $menu_priority = -1;

    /**
     * constructor
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function __construct() {
        if (DataObject::get('SilvercartUpdate',"`Status`='remaining'")) {
            self::$menu_title .= ' (' . DataObject::get('SilvercartUpdate',"`Status`='remaining'")->Count() . ')';
        }
        parent::__construct();
    }
    
    /**
     * title in the top bar of the CMS
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.08.2012
     */
    public function SectionTitle() {
        return _t('SilvercartUpdateAdmin.SILVERCART_UPDATE');
    }

}

/**
 * Modifies the model admin search panel.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.03.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdateAdmin_CollectionController extends ModelAdmin_CollectionController {

    public $showImportForm = false;

    /**
     * Disable the creation of SilvercartUpdate DataObjects.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function alternatePermissionCheck() {
        return false;
    }

    /**
     * Overwrite the TableField permissions. Only update is allowed.
     *
     * @param array $searchCriteria passed through from ResultsForm
     *
     * @return TableListField
     */
    public function  getResultsTable($searchCriteria) {
        $tableListField = parent::getResultsTable($searchCriteria);
        $tableListField->setPermissions(array(
            'update'
        ));
        return $tableListField;
    }

    /**
     * Return a modified search form.
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function SearchForm() {
        $form = parent::SearchForm();
        $fields = $form->Fields();

        // load SilvercartVersions
        $silvercartVersions = array(
            '' => '',
        );
        $silvercartVersionsFromDb = DB::query("
            SELECT DISTINCT `SilvercartVersion`
            FROM `SilvercartUpdate`
            ORDER BY `SilvercartVersion`");

        $silvercartVersions = array_merge(
                $silvercartVersions,
                $silvercartVersionsFromDb->keyedColumn()
        );
        // load SilverCart update status
        $silvercartUpdateStatus = array(
            '' => '',
        );
        $silvercartUpdateStatusFromDb = DB::query("
            SELECT DISTINCT `Status`
            FROM `SilvercartUpdate`
            ORDER BY `Status`");

        $silvercartUpdateStatus = array_merge(
                $silvercartUpdateStatus,
                $silvercartUpdateStatusFromDb->keyedColumn()
        );

        foreach ($silvercartUpdateStatus as $value => $label) {
            if (empty ($value)) {
                continue;
            }
            $silvercartUpdateStatus[$value] = _t('SilvercartUpdate.STATUS_' . strtoupper($value), $label);
        }

        // remove fields
        $fields->removeByName('SilvercartVersion');
        $fields->removeByName('Status');

        $newFields = array();

        while (count($fields->items) > 0) {
            $newFields[] = $fields->shift();
        }

        $fields->push(new DropdownField('SilvercartVersion', _t('SilvercartUpdate.SILVERCARTVERSION'), $silvercartVersions, ''));
        $fields->push(new DropdownField('Status', _t('SilvercartUpdate.STATUS'), $silvercartUpdateStatus, ''));

        foreach ($newFields as $field) {
            $fields->push($field);
        }

        return $form;
    }

}
