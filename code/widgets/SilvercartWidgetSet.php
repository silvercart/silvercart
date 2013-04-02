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
 * Contains an arbitrary number of widgets.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 20.02.2013
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartWidgetSet extends DataExtension {

    /**
     * used to override the WidgetSet::getCMSFields to use the
     * SilverCarts scaffholding with excluded attributes and relations
     * 
     * @return array
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 20.02.2013
     */
    public function overrideGetCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this->owner);
        $fields->addFieldsToTab(
            'Root.Main',
             $this->owner->scaffoldWidgetAreaFields()
        );
        return $fields;

    }
    
    /**
     * exclude these fields from form scaffolding
     *
     * @return array the field names in a numeric array 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 20.02.2013
     */
    public function excludeFromScaffolding() {
        $excludedFields = array(
            'WidgetArea'
        );
        return $excludedFields;
    }
}
