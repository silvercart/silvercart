<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Pages\ProductGroupPageController;
use SilverCart\Model\Widgets\WidgetController;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;

/**
 * ProductGroupChildProductsWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupChildProductsWidgetController extends WidgetController {

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
     * Returns the products of all children (recursively) of the current product group page.
     *
     * @return PaginatedList
     */
    public function getElementsByProductGroup() {
        $productGroupPage = Controller::curr()->data();
        if (!$productGroupPage instanceof ProductGroupPage ||
             $productGroupPage->getProducts()->count() > 0) {

            return PaginatedList::create(ArrayList::create());
        }
        return $productGroupPage->getProductsFromChildren();
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
     * Returns the products.
     * Alias for self::Elements().
     * 
     * @return PaginatedList
     */
    public function getProducts() {
        return $this->Elements();
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
    public function RenderProductGroupPageGroupView($templateBase = 'ProductGroupPage') {
        $controller = Controller::curr();
        $output     = '';

        if ($controller instanceof ProductGroupPageController) {
            $elements = [
                'Elements' => $this->Elements(),
            ];

            $output = $this->customise($elements)->renderWith(
                [
                    $controller->getProductGroupPageTemplateName($templateBase)
                ]
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
    public function ActiveProducts() {
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

        if (!$this->Elements()->exists() ||
            (method_exists($controller, 'isProductDetailView') &&
             $controller->isProductDetailView())) {

            $showWidget = false;
        }

        return $showWidget;
    }
    
}