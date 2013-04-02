<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * SilvercartDownloadPage 
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory 
 */
class SilvercartDownloadPage extends Page {
    
    /**
     * 1:n relations
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartFiles'   => 'SilvercartFile',
    );

    /**
     * returns the singular name
     * 
     * @return string 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }
    
    /**
     * returns the plural name
     * 
     * @return string 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
    
    /**
     * adds a Files Tab to the page with a ComplexTableField
     * 
     * @return Fieldset
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $silvercartFileField = new GridField(
                'SilvercartFiles',
                $this->fieldLabel('SilvercartFiles'),
                $this->SilvercartFiles(),
                SilvercartGridFieldConfig_RelationEditor::create()
        );
        $fields->findOrMakeTab('Root.SilvercartFiles', $this->fieldLabel('SilvercartFiles'));
        $fields->addFieldToTab('Root.SilvercartFiles', $silvercartFileField);
        
        return $fields;
    }
    
    /**
     * fieldLabels method
     * 
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'SilvercartFiles'   => _t('SilvercartFile.PLURALNAME'),
                )
        );
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
}

/**
 * SilvercartDownloadPage_Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory 
 */
class SilvercartDownloadPage_Controller extends Page_Controller {
    
}