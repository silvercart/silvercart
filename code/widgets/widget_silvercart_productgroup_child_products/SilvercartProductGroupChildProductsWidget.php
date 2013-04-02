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
 * Provides a view of items of the child product groups of the current product
 * group if there are no products assigned to the current product group.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 13.11.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
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
                    'SilvercartProductGroupChildProductsWidgetLanguages' => _t('SilvercartConfig.TRANSLATIONS')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Returns the input fields for this widget.
     *
     * @return FieldList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2012
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'ExtraCssClasses', false);

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
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductGroupChildProductsWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Product elements
     *
     * @var ArrayList
     */
    protected $elements = null;

    /**
     * Returns the elements
     *
     * @return ArrayList
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * Returns a number of products from the chosen productgroup.
     *
     * @return ArrayList
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.03.2013
     */
    public function getElementsByProductGroup() {
        $cache                = false;
        $productGroupPage     = Controller::curr();
        $elements             = new PaginatedList(new ArrayList());

        if (method_exists($productGroupPage, 'getProductsPerPageSetting')) {
            $elements->pageLength = $productGroupPage->getProductsPerPageSetting();
            $elements->pageStart  = $productGroupPage->getSqlOffset();
        }
        $pageEnd              = $elements->pageStart + $elements->pageLength;
        $elementIdx           = 0;
        $products             = new ArrayList();

        if (!$productGroupPage instanceof SilvercartProductGroupPage_Controller ||
             $productGroupPage->getProducts()->count() > 0) {

            return $elements;
        }

        $pageIDsToWorkOn = $productGroupPage->getDescendantIDList();

        foreach ($pageIDsToWorkOn as $pageID) {
            $page           = DataObject::get_by_id('SiteTree', $pageID);
            $productsOfPage = $page->getProducts(1000, false, true);
            $productsOfPage = new ArrayList($productsOfPage->toArray());
            
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
        $productGroupPage->addTotalNumberOfProducts($elements->totalSize);

        return $elements;
    }

    /**
     * Sets the elements
     *
     * @param ArrayList $elements Elements to set
     *
     * @return void
     */
    public function setElements(ArrayList $elements) {
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
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.11.2012
     */
    public function Elements() {
        if ($this->elements !== null) {
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
        return $this->Elements()->count() > $count;
    }

    /**
     * Returns the products.
     *
     * @return SS_List
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
     * @return SS_List
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

        if (method_exists($controller, 'isProductDetailView') &&
            $controller->isProductDetailView()) {

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
