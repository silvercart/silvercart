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
 * @subpackage Widgets
 */

/**
 * Provides a login form and links to the registration page as a widget.
 * 
 * If a customer is logged in already the widget shows links to the my account
 * section.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartLoginWidget extends WidgetSetWidget {
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Title() {
        return _t('SilvercartLoginWidget.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function CMSTitle() {
        return _t('SilvercartLoginWidget.CMSTITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Description() {
        return _t('SilvercartLoginWidget.DESCRIPTION');
    }
}

/**
 * Provides a login form and links to the registration page as a widget.
 * 
 * If a customer is logged in already the widget shows links to the my account
 * section.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartLoginWidget_Controller extends WidgetSetWidget_Controller {
    
    /**
     * We register the search form on the page controller here.
     * 
     * @param string $widget Not documented in parent class unfortunately
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function __construct($widget = null) {
        parent::__construct($widget);
        
        Controller::curr()->registerCustomHtmlForm(
            'SilvercartLoginWidgetForm'.$this->classInstanceIdx,
            new SilvercartLoginWidgetForm(
                Controller::curr(),
                array(
                    'redirect_to' => Controller::curr()->Link()
                )
            )
        );
    }
    
    /**
     * Returns the HTML code for the search form.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function InsertCustomHtmlForm() {
        return Controller::curr()->InsertCustomHtmlForm('SilvercartLoginWidgetForm'.$this->classInstanceIdx);
    }
    
    /**
     * Returns the "My Account" page object.
     * 
     * @return SilvercartMyAccountHolder
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function MyAccountPage() {
        return SilvercartTools::PageByIdentifierCode('SilvercartMyAccountHolder');
    }
    
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 02.07.2012
     */
    public function WidgetCacheKey() {
        $key = i18n::get_locale().'_'.Member::currentUserID();
        
        if ((int) $key > 0) {
            $permissions = $this->MyAccountPage()->Children()->map('ID', 'CanView');

            foreach ($permissions as $pageID => $permission) {
                $key .= '_'.$pageID.'-'.((string) $permission);
            }
        }
        
        return $key;
    }
}