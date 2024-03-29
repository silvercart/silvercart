<?php

namespace SilverCart\Model\Order;

use Moo\HasOneSelector\Form\Field as HasOneSelector;
use SilverCart\Dev\Tools;
use SilverCart\Extensions\Model\DataValuable;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Pages\OrderHolder;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\QuantityUnit;
use SilverCart\ORM\ExtensibleDataObject;
use SilverCart\ORM\FieldType\DBMoney as SilverCartDBMoney;
use SilverCart\ORM\Filters\DateRangeSearchFilter;
use SilverCart\View\RenderableDataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\ORM\Filters\PartialMatchFilter;
use SilverStripe\Security\Member;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use function _t;

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
 * @property SilverCartDBMoney $Price                              Price (single)
 * @property SilverCartDBMoney $PriceTotal                         Price total
 * @property bool              $isChargeOrDiscount                 Is charge or discount?
 * @property bool              $isIncludedInTotal                  Is included in total?
 * @property string            $chargeOrDiscountModificationImpact Charge or discount modification impact
 * @property float             $Tax                                Tax
 * @property float             $TaxTotal                           Tax total
 * @property float             $TaxRate                            Tax rate
 * @property string            $ProductDescription                 Product description
 * @property float             $Quantity                           Quantity
 * @property string            $Title                              Title
 * @property string            $ProductNumber                      Product number
 * @property int               $numberOfDecimalPlaces              Number of decimal places
 * @property bool              $IsNonTaxable                       Is non taxable
 * @property int               $ExternalID                         External ID
 * @property int               $OrderID                            Order ID
 * @property int               $ProductID                          Product ID
 * 
 * @method Order   Order()   Returns the related Order.
 * @method Product Product() Returns the related Product.
 * 
 * @mixin DataValuable
 */
class OrderPosition extends DataObject
{
    use ExtensibleDataObject;
    use RenderableDataObject;
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
        'Price'                              => SilverCartDBMoney::class,
        'PriceTotal'                         => SilverCartDBMoney::class,
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
        'ExternalID'                         => 'Varchar(64)',
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
     * Extensions
     *
     * @var string[]
     */
    private static $extensions = [
        DataValuable::class,
    ];
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
     * @return bool
     */
    public function canView($member = null) : bool
    {
        return $this->Order()->canView($member);
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member current member
     *
     * @return bool
     */
    public function canEdit($member = null) : bool
    {
        return $this->Order()->canEdit($member);
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member current member
     *
     * @return bool
     */
    public function canDelete($member = null) : bool
    {
        return $this->Order()->canDelete($member);
    }

    /**
     * Indicates wether the product can be reordered.
     *
     * @param Member|null $member Member
     *
     * @return bool
     */
    public function canReorder(Member|null $member = null) : bool
    {
        $extended = $this->extendedCan('canReorder', $member);
        if ($extended !== null) {
            return $extended;
        }
        $holder = Page::PageByIdentifierCode('SilvercartOrderHolder');
        $can    = $holder instanceof OrderHolder
               && $holder->AllowReorder
               && $this->Product()->canAddToCart();
        $this->extend('updateCanReorder', $can);
        return $can;
    }
    
    /**
     * Returns the CSV export columns.
     * 
     * @return array
     */
    public function exportColumns() : array
    {
        $exportColumns = [];
        $this->extend('updateExportColumns', $exportColumns);
        if (empty($exportColumns)) {
            $exportColumns = array_merge(
                    [
                        'Order.Created' => $this->Order()->fieldLabel('Created'),
                    ],
                    $this->summaryFields()
            );
            if (array_key_exists('Order.CreatedNice', $exportColumns)) {
                unset($exportColumns['Order.CreatedNice']);
            }
        }
        return $exportColumns;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     */
    public function summaryFields() : array
    {
        $summaryFields = [
            'Order.CreatedNice'           => $this->Order()->fieldLabel('Created'),
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
     * @return DBHTMLText
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
     * @return DBText
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
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if (class_exists(HasOneSelector::class)) {
                $orderField = HasOneSelector::create('Order', $this->fieldLabel('Order'), $this, Order::class)
                        ->setLeftTitle($this->fieldLabel('Order'))
                        ->removeAddable()
                        ->removeLinkable();
                $orderField->getConfig()
                        ->removeComponentsByType(GridFieldDeleteAction::class)
                        ->addComponent(new GridFieldTitleHeader());
                $fields->replaceField('OrderID', $orderField);
                $productField = HasOneSelector::create('Product', $this->fieldLabel('Product'), $this, Product::class)
                        ->setLeftTitle($this->fieldLabel('Product'))
                        ->removeAddable();
                $productField->getConfig()
                        ->removeComponentsByType(GridFieldDeleteAction::class)
                        ->addComponent(new GridFieldTitleHeader());
                $fields->replaceField('ProductID', $productField);
            }
            if (empty($this->ExternalID)) {
                $fields->removeByName('ExternalID');
            } else {
                $fields->dataFieldByName('ExternalID')->setDescription($this->fieldLabel('ExternalIDDesc'));
            }
        });
        return parent::getCMSFields();
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
        $this->Title = strip_tags((string) $this->Title);
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
     * @return DBHTMLText
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
}