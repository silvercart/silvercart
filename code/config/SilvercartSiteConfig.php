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
        $dashboardTab = new Tab('silvercartDashboard', 'SilverCart Dashboard');
        $fields->addFieldToTab('Root', $dashboardTab, 'Main');

        $dashboardField = new LiteralField('silvercartDashboardField', '<h2>Willkommen bei SilverCart</h2>');
        $dashboardTab->push($dashboardField);
        
        // Button for testdata and setting generation
        $products = DataObject::get_one('SilvercartProduct');
        
        if (!$products) {
            $dashboardTestDataField = new LiteralField('silvercartDashboardTestDataField', '<br /><h3>Testdaten und -konfiguration</h3><p>Es sind noch keine Produkte vorhanden. Wollen Sie Testprodukte erstellen?</p><p><a href="/admin/silvercart-configuration/#Root_General_set_TestData">Zur Konfigurationssektion für Testdaten springen</a></p>');
            $dashboardTab->push($dashboardTestDataField);
        }
        
        /*
        $configurationLinkField = new LiteralField(
            'silvercartDashboardConfigurationField',
            '<br /><h3>Konfiguration</h3><p><a href="/admin/silvercart-configuration/">Allgemeine Konfiguration</a></p><p><a href="/admin/silvercart-configuration/?jumpTo=SilvercartShopEmail">Emails konfigurieren</a></p><p><a href="/admin/silvercart-configuration/?jumpTo=SilvercartWidgetSet">Widgets konfigurieren</a></p>'
        );
        $dashboardTab->push($configurationLinkField);
        */
    }
    
}