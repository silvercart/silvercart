<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\QuantityUnit;
use SilverCart\ORM\DataObjectExtension;
use SilverCart\ORM\Filters\DateRangeSearchFilter;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * The OrderPosition object.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverCart\ORM\FieldType\DBMoney $Price                              Price (single)
 * @property \SilverCart\ORM\FieldType\DBMoney $PriceTotal                         Price total
 * @property bool                              $isChargeOrDiscount                 Is charge or discount?
 * @property bool                              $isIncludedInTotal                  Is included in total?
 * @property string                            $chargeOrDiscountModificationImpact Charge or discount modification impact
 * @property float                             $Tax                                Tax
 * @property float                             $TaxTotal                           Tax total
 * @property float                             $TaxRate                            Tax rate
 * @property string                            $ProductDescription                 Product description
 * @property float                             $Quantity                           Quantity
 * @property string                            $Title                              Title
 * @property string                            $ProductNumber                      Product number
 * @property int                               $numberOfDecimalPlaces              Number of decimal places
 * @property bool                              $IsNonTaxable                       Is non taxable
 * @property int                               $OrderID                            Order ID
 * @property int                               $ProductID                          Product ID
 * 
 * @method Order   Order()   Returns the related Order.
 * @method Product Product() Returns the related Product.
 */
class OrderPosition extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * Indicates whether changes and creations of order positions should
     * be logged or not.
     *
     * @var boolean
     */
    public $log = true;
    /**
     * Indicates whether the order should be recalculated in method
     * "onAfterWrite".
     *
     * @var boolean
     */
    protected $doRecalculate = false;
    /**
     * Indicates whether the position has been created. Used in onBeforeWrite.
     *
     * @var boolean
     */
    public $objectCreated = false;
    /**
     * Indicates whether the position has been deleted. Used in onBeforeDelete.
     *
     * @var boolean
     */
    public $objectDeleted = false;
    /**
     * attributes
     *
     * @var array
     */
    private static $db = [
        'Price'                              => \SilverCart\ORM\FieldType\DBMoney::class,
        'PriceTotal'                         => \SilverCart\ORM\FieldType\DBMoney::class,
        'isChargeOrDiscount'                 => 'Boolean(0)',
        'isIncludedInTotal'                  => 'Boolean(0)',
        'chargeOrDiscountModificationImpact' => "Enum('none,productValue,totalValue','none')",
        'Tax'                                => 'Float',
        'TaxTotal'                           => 'Float',
        'TaxRate'                            => 'Float',
        'ProductDescription'                 => 'Text',
        'Quantity'                           => 'Decimal',
        'Title'                              => 'Varchar(255)',
        'ProductNumber'                      => 'Varchar',
        'numberOfDecimalPlaces'              => 'Int',
        'IsNonTaxable'                       => 'Boolean(0)',
    ];
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_one = [
        'Order'   => Order::class,
        'Product' => Product::class,
    ];
    /**
     * casted attributes
     *
     * @var array
     */
    private static $casting = [
        'CreatedNice'       => 'Text',
        'PriceNice'         => 'Varchar(255)',
        'PriceTotalNice'    => 'Varchar(255)',
        'FullTitle'         => 'HtmlText',
        'ShortDescription'  => 'Text',
        'TitleNoHTML'       => 'Text',
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartOrderPosition';
    /**
     * Grant API access on this item.
     *
     * @var bool
     */
    private static $api_access = true;

    /**
     * Field labels for display in tables.
     *
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Created'                            => _t(Page::class . '.ORDER_DATE', 'Order date'),
            'Product'                            => Product::singleton()->singular_name(),
            'Price'                              => _t(OrderPosition::class . '.PRICE', 'Price'),
            'PriceTotal'                         => _t(OrderPosition::class . '.PRICETOTAL', 'Price total'),
            'isChargeOrDiscount'                 => _t(OrderPosition::class . '.ISCHARGEORDISCOUNT', 'Is charge or discount'),
            'Tax'                                => _t(OrderPosition::class . '.TAX', 'Vat'),
            'TaxTotal'                           => _t(OrderPosition::class . '.TAXTOTAL', 'Vat total'),
            'TaxRate'                            => _t(OrderPosition::class . '.TAXRATE', 'Vat rate'),
            'ProductDescription'                 => _t(OrderPosition::class . '.PRODUCTDESCRIPTION', 'Description'),
            'Quantity'                           => _t(OrderPosition::class . '.QUANTITY', 'Quantity'),
            'Title'                              => _t(OrderPosition::class . '.TITLE', 'Title'),
            'ProductNumber'                      => _t(OrderPosition::class . '.PRODUCTNUMBER', 'Product no.'),
            'chargeOrDiscountModificationImpact' => _t(OrderPosition::class . '.CHARGEORDISCOUNTMODIFICATIONIMPACT', 'Charge/Discount Type'),
            'numberOfDecimalPlaces'              => QuantityUnit::singleton()->fieldLabel('numberOfDecimalPlaces'),
        ]);
    }

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);  
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member current member
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canView($member = null)
    {
        return $this->Order()->canView($member);
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member current member
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canEdit($member = null)
    {
        return $this->Order()->canEdit($member);
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member current member
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canDelete($member = null)
    {
        return $this->Order()->canDelete($member);
    }
    
    /**
     * Returns the CSV export columns.
     * 
     * @return array
     */
    public function exportColumns() : array
    {
        $exportColumns = [];
        $this->owner->extend('updateExportColumns', $exportColumns);
        if (empty($exportColumns)) {
            $exportColumns = $this->summaryFields();
        }
        return $exportColumns;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.06.2012
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'CreatedNice'                 => $this->Order()->fieldLabel('Created'),
            'Order.OrderNumber'           => $this->Order()->fieldLabel('OrderNumber'),
            'Order.Member.CustomerNumber' => $this->Order()->Member()->fieldLabel('CustomerNumber'),
            'ProductNumber'               => $this->fieldLabel('ProductNumber'),
            'FullTitle'                   => $this->fieldLabel('Title'),
            'PriceNice'                   => $this->fieldLabel('Price'),
            'TaxRate'                     => $this->fieldLabel('TaxRate'),
            'Quantity'                    => $this->fieldLabel('Quantity'),
            'PriceTotalNice'              => $this->fieldLabel('PriceTotal'),
        ];
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Searchable fields.
     * 
     * @return array
     */
    public function searchableFields() : array
    {
        return [
            'Created' => [
                'title'  => $this->fieldLabel('Created'),
                'filter' => DateRangeSearchFilter::class,
                'field'  => TextField::class,
            ],
            'Order.OrderNumber' => [
                'title'  => $this->Order()->fieldLabel('OrderNumber'),
                'filter' => PartialMatchFilter::class,
            ],
            'ProductNumber' => [
                'title'  => $this->fieldLabel('ProductNumber'),
                'filter' => PartialMatchFilter::class,
            ],
            'Title' => [
                'title'  => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ],
            'ProductDescription' => [
                'title'  => $this->fieldLabel('ProductDescription'),
                'filter' => PartialMatchFilter::class,
            ],
        ];
    }
    
    /**
     * Returns some additional content to insert right after the nice price is 
     * rendered.
     * 
     * @return DBHTMLText
     */
    public function AfterPriceNiceContent() : DBHTMLText
    {
        $content = '';
        $this->extend('updateAfterPriceNiceContent', $content);
        return DBHTMLText::create()->setValue($content);
    }
    
    /**
     * Returns some additional content to insert right before the nice price is 
     * rendered.
     * 
     * @return DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function BeforePriceNiceContent() : DBHTMLText
    {
        $content = '';
        $this->extend('updateBeforePriceNiceContent', $content);
        return DBHTMLText::create()->setValue($content);
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return DBHTMLText
     */
    public function getPriceNice() : DBHTMLText
    {
        $priceNice = $this->renderWith(self::class . '_PriceNice');
        $this->extend('updatePriceNice', $priceNice);
        return DBHTMLText::create()->setValue($priceNice);
    }
    
    /**
     * Returns some additional content to insert right after the nice price is 
     * rendered.
     * 
     * @return DBHTMLText
     */
    public function AfterPriceTotalNiceContent() : DBHTMLText
    {
        $content = '';
        $this->extend('updateAfterPriceTotalNiceContent', $content);
        return DBHTMLText::create()->setValue($content);
    }
    
    /**
     * Returns some additional content to insert right before the nice price is 
     * rendered.
     * 
     * @return DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function BeforePriceTotalNiceContent() : DBHTMLText
    {
        $content = '';
        $this->extend('updateBeforePriceTotalNiceContent', $content);
        return DBHTMLText::create()->setValue($content);
    }
    
    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return DBHTMLText
     */
    public function getPriceTotalNice() : DBHTMLText
    {
        $priceNice = $this->renderWith(self::class . '_PriceTotalNice');
        $this->extend('updatePriceTotalNice', $priceNice);
        return DBHTMLText::create()->setValue($priceNice);
    }
    
    /**
     * Returns the tax value as a money object.
     * 
     * @return DBMoney
     */
    public function getTaxMoney() : DBMoney
    {
        return DBMoney::create()->setAmount($this->Tax)->setCurrency($this->Price->getCurrency());
    }
    
    /**
     * Returns the tax total value as a money object.
     * 
     * @return DBMoney
     */
    public function getTaxTotalMoney() : DBMoney
    {
        return DBMoney::create()->setAmount($this->TaxTotal)->setCurrency($this->PriceTotal->getCurrency());
    }

    /**
     * Returns the quantity according to the Product quantity type
     * setting.
     *
     * @return mixed
     */
    public function getTypeSafeQuantity()
    {
        $quantity = $this->Quantity;

        if ($this->numberOfDecimalPlaces == 0) {
            $quantity = (int) $quantity;
        }

        return $quantity;
    }

    /**
     * returns the order positions Title with extensions
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getFullTitle() : DBHTMLText
    {
        $fullTitle = $this->Title . '<br/>' . $this->addToTitle();
        return Tools::string2html($fullTitle);
    }

    /**
     * returns the order positions Title without HTML.
     *
     * @return string
     */
    public function getTitleNoHTML() : string
    {
        return strip_tags($this->Title);
    }

    /**
     * returns the order positions Title with extensions
     *
     * @return \SilverStripe\ORM\FieldType\DBText
     */
    public function getShortDescription($numWords = 28)
    {
        $html = Tools::string2html($this->ProductDescription);
        return trim(str_replace([PHP_EOL], " ", strip_tags($html->LimitWordCount($numWords))), "\xC2\xA0\n ");
    }

    /**
     * returns the orders creation date formated: dd.mm.yyyy hh:mm
     *
     * @return string
     */
    public function getCreatedNice() : string
    {
        return Tools::getDateWithTimeNice($this->Created);
    }
    
    /**
     * Returns the product number as a string.
     * 
     * @return string
     */
    public function getProductNumber() : string
    {
        return (string) $this->getField('ProductNumber');
    }

    /**
     * Returns true if this position has a quantity of more than 1.
     *
     * @return bool
     */
    public function MoreThanOneProduct() : bool
    {
        return $this->Quantity > 1;
    }

    /**
     * Customize scaffolding fields for the backend
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $fields = DataObjectExtension::getCMSFields($this);
        if ($this->exists()) {
            $fields->makeFieldReadonly('Price');
            $fields->makeFieldReadonly('PriceTotal');
            $fields->makeFieldReadonly('Tax');
            $fields->makeFieldReadonly('TaxTotal');
            $fields->makeFieldReadonly('TaxRate');
            $fields->makeFieldReadonly('Quantity');
            $fields->makeFieldReadonly('ProductDescription');
            $fields->makeFieldReadonly('Title');
            $fields->makeFieldReadonly('ProductNumber');
            $fields->makeFieldReadonly('isChargeOrDiscount');
            $fields->makeFieldReadonly('chargeOrDiscountModificationImpact');
            $fields->makeFieldReadonly('OrderID');
            $fields->makeFieldReadonly('ProductID');
            $fields->removeByName('isIncludedInTotal');
            $fields->removeByName('numberOfDecimalPlaces');
            $fields->removeByName('IsNonTaxable');
        }
        return $fields;
    }

    /**
     * If the attributed product gets changed we adjust all order position
     * fields accordingly.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
     */
    public function onBeforeWrite() : void
    {
        $changedFields = $this->getChangedFields();
        if (!$this->objectCreated
         && array_key_exists('OrderID', $changedFields)
        ) {
            $this->saveNew($changedFields);
            $this->objectCreated = true;
        } elseif (!$this->objectCreated) {
            $this->saveChanges($changedFields);
        }
        $this->extend('updateOnBeforeWrite', $changedFields, $this->doRecalculate);
        parent::onBeforeWrite();
    }

    /**
     * Saves changes on an existing position.
     *
     * @param array $changedFields The changed fields
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
     */
    public function saveChanges(array $changedFields) : void
    {
        $price = $this->Price->getAmount();
        if (array_key_exists('Price', $changedFields)) {
            if ($changedFields['Price']['before'] !== $changedFields['Price']['after']) {
                $newPrice = $changedFields['Price']['after'];
                $this->Price->setAmount($newPrice->getAmount());
                $this->PriceTotal->setAmount($newPrice->getAmount() * $this->Quantity);
                $price = $newPrice->getAmount();
                $this->doRecalculate = true;
            }
        }
        if (array_key_exists('Quantity', $changedFields)) {
            if ($changedFields['Quantity']['before'] !== $changedFields['Quantity']['after']) {
                $this->PriceTotal->setAmount($price * $changedFields['Quantity']['after']);
                $this->doRecalculate = true;
                $this->extend('recalculate');
            }
        }
        if (array_key_exists('ProductID', $changedFields)) {
            if ($changedFields['ProductID']['before'] !== $changedFields['ProductID']['after']) {
                $newProduct = Product::get()->byID($changedFields['ProductID']['after']);
                if ($newProduct instanceof Product
                 && $newProduct->exists()
                ) {
                    $this->Price->setAmount($newProduct->getPrice()->getAmount());
                    $this->PriceTotal->setAmount($newProduct->getPrice()->getAmount() * $this->Quantity);
                    $this->Tax                = $newProduct->getTaxAmount();
                    $this->TaxTotal           = $newProduct->getTaxAmount() * $this->Quantity;
                    $this->TaxRate            = $newProduct->getTaxRate();
                    $this->ProductDescription = $newProduct->LongDescription;
                    $this->Title              = $newProduct->Title;
                    $this->ProductNumber      = $newProduct->ProductNumberShop;
                    $this->doRecalculate      = true;
                }
            }
        }
        $this->extend('updateSaveChanges', $changedFields, $price, $this->doRecalculate);
    }

    /**
     * Saves changes for a new position.
     *
     * @param array $changedFields The changed fields
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.09.2017
     */
    public function saveNew(array $changedFields) : void
    {
        if (array_key_exists('ProductID', $changedFields)) {
            $productId = $changedFields['ProductID']['after'];
            $product  = Product::get()->byID($productId);
            if ($product) {
                if (array_key_exists('Quantity', $changedFields)
                 && (int) $changedFields['Quantity']['after'] > 0
                ) {
                    $quantity = (int) $changedFields['Quantity']['after'];
                } else {
                    $quantity = 1;
                }
                $this->Price->setAmount($product->getPrice()->getAmount());
                $this->Price->setCurrency($product->getPrice()->getCurrency());
                $this->PriceTotal->setAmount($product->getPrice()->getAmount() * $quantity);
                $this->PriceTotal->setCurrency($product->getPrice()->getCurrency());
                $this->Quantity           = $quantity;
                $this->Tax                = $product->getTaxAmount();
                $this->TaxTotal           = $product->getTaxAmount() * $quantity;
                $this->TaxRate            = $product->getTaxRate();
                $this->ProductDescription = $product->LongDescription;
                $this->Title              = $product->Title;
                $this->ProductNumber      = $product->ProductNumberShop;
                $this->doRecalculate      = true;
            }
            $this->extend('updateSaveNew', $changedFields, $this->doRecalculate);
        }
    }

    /**
     * Recalculate the order if necessary.
     *
     * @return void
     */
    public function onAfterWrite() : void
    {
        parent::onAfterWrite();
        if ($this->doRecalculate
         && $this->Order()->ID != 0
        ) {
            $this->Order()->recalculate();
            $this->doRecalculate = false;
        }
    }

    /**
     * Make onAfterDelete extendable.
     *
     * @return void
     */
    public function onAfterDelete() : void
    {
        $this->extend('updateOnAfterDelete');
        if ($this->Order()->exists()) {
            $this->Order()->recalculate();
        }
        parent::onAfterDelete();
    }

    /**
     * Make onBeforeDelete extendable.
     *
     * @return void
     */
    public function onBeforeDelete() : void
    {
        if (!$this->objectDeleted) {
            $this->extend('updateOnBeforeDelete');
            $this->objectDeleted = true;
        }

        parent::onBeforeDelete();
    }

    /**
     * Returns additional tile information provided by plugins
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function addToTitle() : DBHTMLText
    {
        $addToTitle = '';
        $this->extend('addToTitle', $addToTitle);
        return Tools::string2html($addToTitle);
    }
    
    /**
     * Returns the rendered position.
     * 
     * @param string $templateAddition Optional template name addition
     * 
     * @return DBHTMLText
     */
    public function forTemplate(string $templateAddition = '') : DBHTMLText
    {
        $addition = empty($templateAddition) ? '' : "_{$templateAddition}";
        return $this->renderWith(static::class . $addition);
    }
}