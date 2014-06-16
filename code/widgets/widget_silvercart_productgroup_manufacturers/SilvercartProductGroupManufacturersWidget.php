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
 * Provides a view of all manufacturers from a product group.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.10.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductGroupManufacturersWidget extends SilvercartWidget {
    
    /**
     * Attributes.
     * 
     * @var array
     */
    public static $db = array(
    );

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartProductGroupManufacturersWidgetLanguages' => 'SilvercartProductGroupManufacturersWidgetLanguage'
    );

    /**
     * field casting
     *
     * @var array
     */
    public static $casting = array(
        'FrontTitle'                    => 'VarChar(255)',
        'FrontContent'                  => 'Text',
    );
    
    /**
     * Set default values.
     * 
     * @var array
     */
    public static $defaults = array(
    );
    
    /**
     * Getter for the front title depending on the set language
     *
     * @return string  
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public function getFrontTitle() {
        return $this->getLanguageFieldValue('FrontTitle');
    }
    
    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string The HTML front content 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.01.2012
     */
    public function getFrontContent() {
        return $this->getLanguageFieldValue('FrontContent');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'ExtraCssClasses', false);

        return $fields;
    }

    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2013
     */
    public function Title() {
        $title = $this->FrontTitle;
        if (empty($title)) {
            $title = $this->fieldLabel('Title');
        }
        return $title;
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2012
     */
    public function CMSTitle() {
        return $this->fieldLabel('CMSTitle');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2012
     */
    public function Description() {
        return $this->fieldLabel('Description');
    }
    
    /**
     * Returns the extra css classes.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.04.2013
     */
    public function ExtraCssClasses() {
        return 'silvercart-product-group-manufacturers-widget';
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                SilvercartWidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'SilvercartProductGroupManufacturersWidgetLanguages' => _t('Silvercart.TRANSLATIONS'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}

/**
 * Provides a view of all manufacturers from a product group.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.10.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductGroupManufacturersWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Returns a ArrayList of all manufacturers for this page.
     *
     * @return DataList|ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2012
     */
    public function getSilvercartManufacturers() {
        $manufacturers  = new ArrayList();
        $controller     = Controller::curr();

        if ($controller instanceof SilvercartProductGroupPage ||
            $controller instanceof SilvercartProductGroupPage_Controller) {

            $manufacturers = $controller->getManufacturers();
        }

        return $manufacturers;
    }

    /**
     * Checks whether the product list should be filtered by manufacturer.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.10.2012
     */
    public function isFilteredByManufacturer() {
        $isFiltered = false;
        $controller = Controller::curr();

        if ($controller instanceof SilvercartProductGroupPage ||
            $controller instanceof SilvercartProductGroupPage_Controller) {

            $isFiltered = $controller->isFilteredByManufacturer();
        }

        return $isFiltered;
    }

    /**
     * Returns the link to the controller page.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2012
     */
    public function PageLink() {
        return Controller::curr()->Link();
    }

    /**
     * Returns whether to show the widget or not.
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.10.2012
     */
    public function ShowWidget() {
        $showWidget = false;
        $controller = Controller::curr();

        if ($controller instanceof SilvercartProductGroupPage_Controller) {
            if ($controller->isProductDetailView() === false) {
                $showWidget = true;
            }
        } else {
            $showWidget = true;
        }

        return $showWidget;
    }
}
