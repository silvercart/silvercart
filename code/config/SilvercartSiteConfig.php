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
 * @subpackage Config
 */

/**
 * This class is used to add a dashboard to the original SiteConfig object in
 * the cms section.
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 21.10.2011
 * @license LGPL
 */
class SilvercartSiteConfig extends DataObjectDecorator {
    
    /**
     * Adds a dashboard section
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.10.2011
     */
    public function updateCMSFields(&$fields) {
        $dashboardTab = new Tab('silvercartDashboard', _t('SilvercartSiteConfig.DASHBOARD_TAB'));
        $fields->addFieldToTab('Root', $dashboardTab, 'Main');

        $dashboardField = new LiteralField(
            'silvercartDashboardField',
            sprintf(
                "<h2>%s</h2>",
                _t('SilvercartSiteConfig.WELCOME_TO_SILVERCART')
            )
        );
        $dashboardTab->push($dashboardField);
        
        // Button for testdata and setting generation
        $products = DataObject::get_one('SilvercartProduct');
        
        if (!$products) {
            $dashboardTestDataField = new LiteralField(
                'silvercartDashboardTestDataField',
                sprintf(
                    "<br /><h3>%s</h3><p>%s</p><p><a href=\"%sadmin/silvercart-configuration/#Root_General_set_TestData\">%s</a></p>",
                    _t('SilvercartSiteConfig.TESTDATA_HEADLINE'),
                    _t('SilvercartSiteConfig.TESTDATA_TEXT'),
                    Director::absoluteBaseURL(),
                    _t('SilvercartSiteConfig.TESTDATA_LINKTEXT')
                )
            );
            $dashboardTab->push($dashboardTestDataField);
        }
    }
}