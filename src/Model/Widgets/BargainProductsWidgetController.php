<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Widgets\BargainProductsWidget;
use SilverCart\Model\Widgets\ProductSliderWidgetController;
use SilverCart\Model\Widgets\WidgetController;
use SilverCart\Model\Widgets\WidgetTools;
use SilverStripe\ORM\ArrayList;

/**
 * Provides the a view of the bargain products.
 * You can define the number of products to be shown.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class BargainProductsWidgetController extends WidgetController {
    
    use ProductSliderWidgetController;

    /**
     * Plain product elements
     *
     * @var ArrayList 
     */
    protected $products= null;
    
    /**
     * Register forms for the contained products.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.07.2011
     */
    protected function init() {
        parent::init();
        WidgetTools::initProductSliderWidget($this);
    }
    
    /**
     * Returns a number of bargain products.
     * 
     * @return SS_List
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function Elements() {
        if (is_null($this->elements)) {
            if (!$this->numberOfProductsToFetch) {
                $this->numberOfProductsToFetch = BargainProductsWidget::$defaults['numberOfProductsToFetch'];
            }

            if (Config::Pricetype() == 'net') {
                $priceField = 'PriceNetAmount';
            } else {
                $priceField = 'PriceGrossAmount';
            }

            $productTable = Tools::get_table_name(Product::class);
            
            switch ($this->fetchMethod) {
                case 'sortOrderAsc':
                    $sort = '"' . $productTable . '"."MSRPriceAmount" - "' . $productTable . '"."PriceGrossAmount" ASC';
                    break;
                case 'sortOrderDesc':
                    $sort = '"' . $productTable . '"."MSRPriceAmount" - "' . $productTable . '"."PriceGrossAmount" DESC';
                    break;
                case 'random':
                default:
                    $sort = "RAND()";
            }
            $this->listFilters = array();

            if (count(self::$registeredFilterPlugins) > 0) {
                foreach (self::$registeredFilterPlugins as $registeredPlugin) {
                    $pluginFilters = $registeredPlugin->filter();

                    if (is_array($pluginFilters)) {
                        $this->listFilters = array_merge(
                            $this->listFilters,
                            $pluginFilters
                        );
                    }
                }
            }

            $filter = sprintf(
                            '"' . $productTable . '"."MSRPriceAmount" IS NOT NULL 
                            AND "' . $productTable . '"."MSRPriceAmount" > 0
                            AND "' . $productTable . '"."%s" < "' . $productTable . '"."MSRPriceAmount"',
                            $priceField
            );

            foreach ($this->listFilters as $listFilterIdentifier => $listFilter) {
                $filter .= ' ' . $listFilter;
            }

            $products = Product::getProducts(
                    $filter,
                    $sort,
                    null,
                    "0," . $this->numberOfProductsToFetch
            );
            
            $this->elements = $products;

            foreach ($this->elements as $element) {
                $element->addCartFormIdentifier = $this->ID.'_'.$element->ID;
            }
        }
        return $this->elements;
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
     * Returns a number of products from the chosen productgroup.
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.03.2012
     */
    public function ProductPages() {
        if (is_null($this->elements)) {
            $this->Elements();
            $pages          = array();
            $pageProducts   = array();
            $pageNr         = 0;
            $PageProductIdx = 1;
            $isFirst        = true;
            if ($this->elements) {
                foreach ($this->elements as $product) {
                    $product->addCartFormIdentifier = $this->ID.'_'.$product->ID;
                    $pageProducts[] = $product;
                    $PageProductIdx++;

                    if ($pageNr > 0) {
                        $isFirst = false;
                    }
                    if ($PageProductIdx > $this->numberOfProductsToShow) {
                        $pages['Page'.$pageNr] = array(
                            'Elements'  => new ArrayList($pageProducts),
                            'IsFirst'   => $isFirst
                        );
                        $PageProductIdx = 1;
                        $pageProducts   = array();
                        $pageNr++;
                    }
                }
            }

            if (!array_key_exists('Page'.$pageNr, $pages) &&
                !empty($pageProducts)) {
                if ($pageNr > 0) {
                    $isFirst = false;
                }
                $pages['Page'.$pageNr] = array(
                    'Elements'  => new ArrayList($pageProducts),
                    'IsFirst'   => $isFirst,
                );
            }
            $this->elements = new ArrayList($pages);
        } else {
            foreach ($this->elements as $page) {
                $page->Content = Controller::curr()->customise($page)->renderWith(WidgetTools::getGroupViewTemplateName($this));
            }
        }
        return $this->elements;
    }
    
    /**
     * Returns the products to inject into a product group
     *
     * @return ArrayList
     */
    public function getProducts() {
        $this->products = new ArrayList();
        if (!$this->useSlider &&
            !$this->useRoundabout) {
            $this->products = $this->Elements();
        }
        return $this->products;
    }
    
    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2012
     */
    public function WidgetCacheKey() {
        $key = WidgetTools::ProductWidgetCacheKey($this);
        return $key;
    }
    
    /**
     * Returns the number of products to show
     * 
     * @return int
     */
    public function numberOfProductsToShowForGroupView() {
        switch ($this->GroupView) {
            case 'tile':
                $divisor = 2;
                break;
            case 'threetile':
                $divisor = 3;
                break;
            case 'fourtile':
                $divisor = 4;
                break;
            default:
                $divisor = 1;
                break;
        }
        return ceil($this->numberOfProductsToShow / $divisor);
    }
}