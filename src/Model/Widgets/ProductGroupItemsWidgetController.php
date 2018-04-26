<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Widgets\ProductGroupItemsWidget;
use SilverCart\Model\Widgets\ProductSliderWidgetController;
use SilverCart\Model\Widgets\WidgetController;
use SilverCart\Model\Widgets\WidgetTools;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

/**
 * ProductGroupItemsWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ProductGroupItemsWidgetController extends WidgetController {
    
    use ProductSliderWidgetController;
    
    /**
     * Register forms for the contained products.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.03.2012
     */
    protected function init() {
        parent::init();
        WidgetTools::initProductSliderWidget($this);
    }
    
    /**
     * Returns a number of products from the chosen productgroup.
     * 
     * @return ArrayList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.08.2013
     */
    public function ProductPages() {
        if ($this->elements !== null &&
            $this->elements !== false) {
            $template = WidgetTools::getGroupViewTemplateName($this);
            foreach ($this->elements as $page) {
                $page->Content = $page->renderWith($template);
            }
            return $this->elements;
        }
        switch ($this->useSelectionMethod) {
            case 'products':
                $this->elements = $this->getElementsByProducts();
                break;
            case 'productGroup':
            default:
                $this->elements = $this->getElementsByProductGroup();
                break;
        }

        if (!$this->ProductGroupPageID) {
            return false;
        }
        
        if (!$this->numberOfProductsToFetch) {
            $this->numberOfProductsToFetch = ProductGroupItemsWidget::$defaults['numberOfProductsToFetch'];
        }
        if (!$this->numberOfProductsToShow) {
            $this->numberOfProductsToShow = ProductGroupItemsWidget::$defaults['numberOfProductsToShow'];
        }

        if ($this->numberOfProductsToFetch < $this->numberOfProductsToShow) {
            $this->numberOfProductsToFetch = $this->numberOfProductsToShow;
        }

        if ($this->numberOfProductsToFetch == $this->numberOfProductsToShow ||
            $this->useSelectionMethod == 'products') {
            $products = $this->elements;
        } else {
            $productGroupPage = ProductGroupPage::get()->byID($this->ProductGroupPageID);

            if (!($productGroupPage instanceof ProductGroupPage) ||
                !$productGroupPage->exists()) {
                return false;
            }
            $productGroupPageSiteTree = ModelAsController::controller_for($productGroupPage);

            switch ($this->fetchMethod) {
                case 'sortOrderAsc':
                    $products = $productGroupPageSiteTree->getProducts($this->numberOfProductsToFetch);
                    break;
                case 'random':
                default:
                    $products = $productGroupPageSiteTree->getRandomProducts($this->numberOfProductsToFetch);
            }
        }

        $pages          = array();
        $pageProducts   = array();
        $pageNr         = 0;
        $pageProductIdx = 1;
        $isFirst        = true;

        if ($products->exists()) {
            $products = new ArrayList($products->toArray());
            foreach ($products as $product) {
                $product->addCartFormIdentifier = $this->ID.'_'.$product->ID;
                $pageProducts[] = $product;
                $pageProductIdx++;

                if ($pageNr > 0) {
                    $isFirst = false;
                }
                if ($pageProductIdx > $this->numberOfProductsToShow) {
                    $pages[$pageNr] = new ArrayData(
                            array(
                                'Elements' => new ArrayList($pageProducts),
                                'IsFirst'  => $isFirst
                            )
                    );
                    $pageProductIdx = 1;
                    $pageProducts   = array();
                    $pageNr++;
                }
            }
        }

        if (!array_key_exists($pageNr, $pages) &&
            !empty($pageProducts)) {

            if ($pageNr > 0) {
                $isFirst = false;
            }

            $pages[$pageNr] = new ArrayData(
                    array(
                        'Elements' => new ArrayList($pageProducts),
                        'IsFirst'  => $isFirst
                    )
            );
        }

        $this->elements = new ArrayList($pages);
        
        return $this->elements;
    }

    /**
     * Returns the elements for the static slider view.
     * 
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function Elements() {
        if (is_null($this->elements)) {
            switch ($this->useSelectionMethod) {
                case 'products':
                    $this->elements = $this->getElementsByProducts();
                    break;
                case 'productGroup':
                default:
                    $this->elements = $this->getElementsByProductGroup();
                    break;
            }

            $this->elements = new ArrayList($this->elements->toArray());

            foreach ($this->elements as $element) {
                $element->addCartFormIdentifier = $this->ID.'_'.$element->ID;
            }
        }

        return $this->elements;
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
        $key = WidgetTools::ProductWidgetCacheKey($this);
        return $key;
    }
    
    /**
     * Returns the content for non slider widgets
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.05.2012
     */
    public function ElementsContent() {
        return $this->customise(array(
            'Elements' => $this->Elements(),
        ))->renderWith(WidgetTools::getGroupViewTemplateName($this));
    }

    /**
     * Returns the manually chosen products.
     * 
     * @return ArrayList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.02.2012
     */
    public function getElementsByProducts() {
        $products = $this->Products()->where(Product::get_frontend_sql_filter());
        
        foreach ($products as $product) {
            $product->addCartFormIdentifier = $this->ID.'_'.$product->ID;
        }

        return $products;
    }

    /**
     * Returns a number of products from the chosen productgroup.
     * 
     * @return DataList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function getElementsByProductGroup() {
        $elements = new ArrayList();

        if (!$this->ProductGroupPageID) {
            return $elements;
        }

        if (!$this->numberOfProductsToShow) {
            $defaults = $this->getWidget()->config()->get('defaults');
            $this->numberOfProductsToShow = $defaults['numberOfProductsToShow'];
        }

        $productGroupPage = ProductGroupPage::get()->byID($this->ProductGroupPageID);

        if (!($productGroupPage instanceof ProductGroupPage) ||
            !$productGroupPage->exists()) {
            return $elements;
        }
        $productGroupPageSiteTree = ModelAsController::controller_for($productGroupPage);

        if (!Widget::$use_product_pages_for_slider &&
            $this->useSlider) {
            $fetchLimit = $this->numberOfProductsToFetch;
        } else {
            $fetchLimit = $this->numberOfProductsToShow;
        }
        
        switch ($this->fetchMethod) {
            case 'sortOrderAsc':
                $elements = $productGroupPageSiteTree->getProducts()->limit($fetchLimit);
                break;
            case 'random':
            default:
                $elements = $productGroupPageSiteTree->getRandomProducts($fetchLimit);
        }
        
        return $elements;
    }
    
    /**
     * Returns the title of the product group that items are shown.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.04.2011
     */
    public function ProductGroupTitle() {
        $title = '';
        
        if (!$this->ProductGroupPageID) {
            return $title;
        }
        
        $productGroupPage = ProductGroupPage::get()->byID($this->ProductGroupPageID);
        
        if ($productGroupPage instanceof ProductGroupPage &&
            $productGroupPage->exists()) {
            $title = $productGroupPage->MenuTitle;
        }
        
        
        return $title;
    }
}