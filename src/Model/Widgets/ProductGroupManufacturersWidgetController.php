<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Pages\ProductGroupPageController;
use SilverCart\Model\Widgets\WidgetController;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;

/**
 * ProductGroupManufacturersWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupManufacturersWidgetController extends WidgetController {

    /**
     * Returns a ArrayList of all manufacturers for this page.
     *
     * @return DataList|ArrayList
     */
    public function getManufacturers() {
        $manufacturers  = new ArrayList();
        $controller     = Controller::curr();

        if ($controller instanceof ProductGroupPage ||
            $controller instanceof ProductGroupPageController) {

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

        if ($controller instanceof ProductGroupPage ||
            $controller instanceof ProductGroupPageController) {

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

        if ($controller instanceof ProductGroupPageController) {
            if ($controller->isProductDetailView() === false) {
                $showWidget = true;
            }
        } else {
            $showWidget = true;
        }

        return $showWidget;
    }
}