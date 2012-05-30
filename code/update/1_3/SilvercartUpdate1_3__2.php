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
 * @subpackage Update
 */

/**
 * Update 1.3 - 2
 * Updates all product slider widgets to use groupviews
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.05.2012
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_3__2 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.05.2012
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '2',
        'Description'               => 'Updates all product slider widgets to use groupviews.',
    );
    
    /**
     * Executes the update logic.
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.05.2012
     */
    public function executeUpdate() {
        $this->updateWidget('SilvercartProductGroupItemsWidget');
        $this->updateWidget('SilvercartBargainProductsWidget');
        if (class_exists('SilvercartMarketingCrossSellingWidget')) {
            $this->updateWidget('SilvercartMarketingCrossSellingWidget');
        }
        return true;
    }
    
    /**
     * Executes the update for one widget
     *
     * @param string $widgetName Name of the widget to update
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.05.2012
     */
    public function updateWidget($widgetName) {
        $query = DB::query(
                sprintf(
                        "SELECT `ID`, `useListView` FROM `%s`",
                        $widgetName
                )
        );
        if ($query) {
            foreach ($query as $result) {
                $widget         = DataObject::get_by_id($widgetName, $result['ID']);
                $useListView    = $result['useListView'];
                
                $groupView = 'tile';
                if ($useListView) {
                    $groupView = 'list';
                }
                
                $widget->GroupView = $groupView;
                $widget->write();
            }
        }
        DB::query(
                sprintf(
                        "ALTER TABLE `%s` DROP `useListView`",
                        $widgetName
                )
        );
        return true;
    }
}