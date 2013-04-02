<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartLoginWidget extends SilvercartWidget {
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
    */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        return $fields;
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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartLoginWidget_Controller extends SilvercartWidget_Controller {
    
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