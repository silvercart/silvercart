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
 * Provides a view of items of the child product groups of the current product
 * group if there are no products assigned to the current product group.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 13.11.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartProductGroupChildProductsWidget extends SilvercartWidget {

    /**
     * Set whether to use the widget container divs or not.
     *
     * @var bool
     * @since 2012-11-14
     */
    public $useWidgetContainer = false;

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartProductGroupChildProductsWidgetLanguages' => 'SilvercartProductGroupChildProductsWidgetLanguage'
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
     * Getter for the front title depending on the set language
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function getFrontTitle() {
        return $this->getLanguageFieldValue('FrontTitle');
    }

    /**
     * Getter for the FrontContent depending on the set language
     *
     * @return string The HTML front content
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function getFrontContent() {
        return $this->getLanguageFieldValue('FrontContent');
    }

    /**
     * Returns the title of this widget.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
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
     * @since 13.11.2012
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
     * @since 13.11.2012
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
     * @since 13.11.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                SilvercartWidgetTools::fieldLabelsForProductSliderWidget($this),
                array(
                    'Title'       => _t('SilvercartProductGroupChildProductsWidget.TITLE'),
                    'CMSTitle'    => _t('SilvercartProductGroupChildProductsWidget.CMSTITLE'),
                    'Description' => _t('SilvercartProductGroupChildProductsWidget.DESCRIPTION'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Returns the input fields for this widget.
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2012
     */
    public function getCMSFields() {
        $fields = new FieldSet();
        $rootTabSet                 = new TabSet('Root');

        $titleField                 = new TextField('FrontTitle',               $this->fieldLabel('FrontTitle'));
        $contentField               = new TextareaField('FrontContent',         $this->fieldLabel('FrontContent'), 10);

        $basicTab = new Tab('Basic',          $this->fieldLabel('BasicTab'));
        $basicTab->push($titleField);
        $basicTab->push($contentField);

        $translationTab         = new Tab('Translations',   $this->fieldLabel('TranslationsTab'));
        $translationsTableField = new ComplexTableField($this, 'SilvercartProductGroupChildProductsWidgetLanguages', 'SilvercartProductGroupChildProductsWidgetLanguage');
        $translationTab->push($translationsTableField);

        $fields->push($rootTabSet);
        $rootTabSet->push($basicTab);
        $fields->addFieldToTab('Root', $translationTab);

        return $fields;
    }
}

/**
 * Provides a view of items of the child product groups of the current product
 * group if there are no products assigned to the current product group.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 13.11.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartProductGroupChildProductsWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Product elements
     *
     * @var DataObjectSet
     */
    protected $elements = null;

    /**
     * Returns the elements
     *
     * @return DataObjectSet
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * Returns a number of products from the chosen productgroup.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2011
     */
    public function getElementsByProductGroup() {
        $cache                = false;
        $productGroupPage     = Controller::curr();
        $elements             = new DataObjectSet();
        $elements->pageLength = $productGroupPage->getProductsPerPageSetting();
        $elements->pageStart  = $productGroupPage->getSqlOffset();
        $pageEnd              = $elements->pageStart + $elements->pageLength;
        $elementIdx           = 0;
        $products             = new DataObjectSet();

        if (!$productGroupPage instanceof SilvercartProductGroupPage_Controller ||
             $productGroupPage->getProducts()->Count() > 0) {

            return $elements;
        }

        $result          = false;
        $pageIDsToWorkOn = $productGroupPage->getDescendantIDList();

        if (count($pageIDsToWorkOn) > 0) {
            $childSiteTreeObjects = DataObject::get_one('SiteTree', "SiteTree_Live.ID IN (".implode(',', $pageIDsToWorkOn).")", "LastEdited DESC");

            if ($childSiteTreeObjects) {
                $cacheKey  = $childSiteTreeObjects->ID;
                $cacheKey .= $elements->pageLength.'-'.$elements->pageStart.'-';
                $cacheKey .= Translatable::get_current_locale();
                $cacheKey  = md5($cacheKey);

                $cache    = SS_Cache::factory($cacheKey);
                $result   = $cache->load($cacheKey);
            }
        }

        if ($result) {
            $elements = unserialize($result);
        } else {
            foreach ($pageIDsToWorkOn as $pageID) {
                $page           = DataObject::get_by_id('SiteTree', $pageID);
                $productsOfPage = $page->getProducts(100, false, true);

                foreach ($productsOfPage as $product) {
                    $product->addCartFormIdentifier = $this->ID.'_'.$product->ID;
                    $products->push($product);
                }
            }

            $sortElems    = explode(" ", SilvercartProduct::defaultSort());
            $sortElems[0] = str_replace('SilvercartProduct.', '', $sortElems[0]);
            $products->sort($sortElems[0], $sortElems[1]);

            foreach ($products as $product) {
                if ($elementIdx >= $elements->pageStart &&
                    $elementIdx < $pageEnd) {
                    $elements->push($product);
                }
                $elementIdx++;
            }

            $elements->totalSize = $elementIdx;

            if ($cache) {
                $cache->save(serialize($elements));
            }
        }

        return $elements;
    }

    /**
     * Sets the elements
     *
     * @param DataObjectSet $elements Elements to set
     *
     * @return void
     */
    public function setElements(DataObjectSet $elements) {
        $this->elements = $elements;
    }

    /**
     * Register forms for the contained products.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function init() {
        $this->Elements();

        if ($this->getElements()) {
            $elementIdx = 0;

            foreach ($this->getElements() as $element) {
                SilvercartWidgetTools::registerAddCartFormForProductWidget($this, $element, $elementIdx);
                $elementIdx++;
            }
        }
    }

    /**
     * Returns the elements for this product group.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function Elements() {
        if ($this->elements != null) {
            return $this->elements;
        }

        $this->elements = $this->getElementsByProductGroup();

        return $this->elements;
    }

    /**
     * Returns the content for non slider widgets
     *
     * @param string $templateBase Base name of the template to render group view with
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function RenderProductGroupPageGroupView($templateBase = 'SilvercartProductGroupPage') {
        $controller = Controller::curr();
        $output     = '';

        if ($controller instanceof SilvercartProductGroupPage_Controller) {
            $elements = array(
                'Elements' => $this->Elements(),
            );
            $output = $this->customise($elements)->renderWith(
                array(
                    $controller->getProductGroupPageTemplateName($templateBase),
                    'Includes/'.$controller->getProductGroupPageTemplateName($templateBase)
                )
            );
        }

        return $output;
    }

    /**
     * Returns the products.
     *
     * @param int $count The number to check against
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function HasMoreProductsThan($count) {
        return $this->Elements()->Count() > $count;
    }

    /**
     * Returns the products.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function ActiveSilvercartProducts() {
        return $this->Elements();
    }

    /**
     * Returns the products.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function Products() {
        return $this->Elements();
    }

    /**
     * Return whether to show the widget.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function ShowWidget() {
        $controller = Controller::curr();
        $showWidget = true;

        if ($controller->isProductDetailView()) {
            $showWidget = false;
        }

        return $showWidget;
    }

    /**
     * returns HTML markup for the requested form
     *
     * @param string $formIdentifier   unique form name which can be called via template
     * @param Object $renderWithObject object array; in those objects context the forms shall be created
     *
     * @return CustomHtmlForm
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function InsertCustomHtmlForm($formIdentifier, $renderWithObject = null) {
        $controller = Controller::curr();
        return $controller->InsertCustomHtmlForm($formIdentifier, $renderWithObject);
    }
}
