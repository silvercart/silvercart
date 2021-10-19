<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MyAccountHolder;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\View\ArrayData;

/**
 * shows an overview of a customers orders.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class OrderHolder extends MyAccountHolder
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilverCart_OrderHolder';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'AllowReorder' => 'Boolean',
    ];
    /**
     * Indicates whether this page type can be root
     *
     * @var bool
     */
    private static $can_be_root = false;
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/my_account_holder-file.gif";
    
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
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        $this->beforeUpdateFieldLabels(function(&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
                        'ButtonReorder'         => _t(self::class . '.ButtonReorder', 'Add to cart'),
                        'ButtonReorderDesc'     => _t(self::class . '.ButtonReorderDesc', 'Adds this order\'s items to the shopping cart again.'),
                        'ButtonReorderFull'     => _t(self::class . '.ButtonReorderFull', 'Add to cart and checkout'),
                        'ButtonReorderFullDesc' => _t(self::class . '.ButtonReorderFullDesc', 'Adds this order\'s items to the shopping cart and directs to the checkout process using address, shipment and payment data of this order.'),
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->insertAfter('IdentifierCode', CheckboxField::create('AllowReorder', $this->fieldLabel('AllowReorder'))->setDescription($this->fieldLabel('AllowReorderDesc')));
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns whether this page has a summary.
     * 
     * @return bool
     */
    public function hasSummary() : bool
    {
        return true;
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return DBHTMLText
     */
    public function getSummary() : DBHTMLText {
        return $this->renderWith('SilverCart/Model/Pages/Includes/OrderSummary');
    }
    
    /**
     * Returns the summary of this page.
     * 
     * @return string
     */
    public function getSummaryTitle() : string
    {
        return _t(MyAccountHolder::class . '.YOUR_MOST_CURRENT_ORDERS', 'Your most current orders');
    }
    
    /**
     * Adds the add/edit address title to the bradcrumbs by context.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     * 
     * @return ArrayList
     */
    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false) : ArrayList
    {
        $items = parent::getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        $ctrl  = Controller::curr();
        if ($ctrl->getAction() == 'detail') {
            $order = $ctrl->CustomersOrder();
            $title = DBText::create();
            $title->setValue($order->Title);
            $items->push(ArrayData::create([
                'MenuTitle' => $title,
                'Title'     => $title,
                'Link'      => $ctrl->OrderDetailLink($order->ID),
            ]));
        }
        return $items;
    }
    
    /**
     * Returns the link to reoder an order.
     * 
     * @param int $orderID Order ID
     * 
     * @return string
     */
    public function ReoderLink(int $orderID = null) : string
    {
        $link = '';
        if (Controller::has_curr()) {
            if ($orderID === null) {
                $orderID = Controller::curr()->getOrderID();
            }
            $link    = $this->Link("placeorder/{$orderID}");
        }
        return $link;
    }
    
    /**
     * Returns the link to reoder an order full.
     * 
     * @param int $orderID Order ID
     * 
     * @return string
     */
    public function ReoderFullLink(int $orderID = null) : string
    {
        $link = '';
        if (Controller::has_curr()) {
            if ($orderID === null) {
                $orderID = Controller::curr()->getOrderID();
            }
            $link    = $this->Link("placeorder-full/{$orderID}");
        }
        return $link;
    }
}