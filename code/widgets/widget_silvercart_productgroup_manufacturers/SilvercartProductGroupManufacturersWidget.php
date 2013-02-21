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
 * @subpackage Widgets
 */

/**
 * Provides a view of all manufacturers from a product group.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.10.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
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
        $fields                     = new FieldList();
        $titleField                 = new TextField('FrontTitle',               $this->fieldLabel('FrontTitle'));
        $contentField               = new TextareaField('FrontContent',         $this->fieldLabel('FrontContent'), 10);

        $rootTabSet                 = new TabSet('Root');
        $basicTab                   = new Tab('Basic',          $this->fieldLabel('BasicTab'));
        $translationTab             = new Tab('Translations',   $this->fieldLabel('TranslationsTab'));
        $translationsTableField     = new ComplexTableField($this, 'SilvercartProductGroupManufacturersWidgetLanguages', 'SilvercartProductGroupManufacturersWidgetLanguage');

        $fields->push($rootTabSet);
        $rootTabSet->push($basicTab);
        $rootTabSet->push($translationTab);

        $basicTab->push($titleField);
        $basicTab->push($contentField);

        $translationTab->push($translationsTableField);

        return $fields;
    }

    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.10.2012
     */
    public function Title() {
        return $this->fieldLabel('Title');
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
                    'TranslationsTab' => _t('SilvercartConfig.TRANSLATIONS'),
                    'DisplayTab'      => _t('SilvercartConfig.TRANSLATIONS'),
                    'Title'           => _t('SilvercartProductGroupManufacturersWidget.TITLE'),
                    'CMSTitle'        => _t('SilvercartProductGroupManufacturersWidget.CMSTITLE'),
                    'Description'     => _t('SilvercartProductGroupManufacturersWidget.DESCRIPTION'),
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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartProductGroupManufacturersWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Returns a ArrayList of all manufacturers for this page.
     *
     * @return DataObjectSet
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
