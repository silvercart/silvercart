<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Dev\Tools;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\Model\Payment\PaymentStatus;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

/**
 * ModelAdmin for Orders.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class OrderAdmin extends ModelAdmin
{
    const SESSION_KEY     = 'SilverCart.OrderAdmin';
    const SESSION_KEY_TAB = 'SilverCart.OrderAdmin.Tab';
    
    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'orders';
    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 10;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-orders';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = [
        Order::class,
    ];
    /**
     * Current tab
     *
     * @var string
     */
    protected $currentTab = null;

    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 14.09.2018
     */
    protected function init()
    {
        $this->beforeUpdateInit(function() {
            Requirements::javascript('silvercart/silvercart:client/admin/javascript/jquery-ui/jquery.ui.datepicker.js');
            Requirements::javascript('silvercart/silvercart:client/admin/javascript/jquery-ui/jquery.ui.daterangepicker.date.js');
            Requirements::javascript('silvercart/silvercart:client/admin/javascript/jquery-ui/jquery.ui.daterangepicker.js');
            Requirements::css('silvercart/silvercart:client/admin/css/jquery-ui/daterangepicker.css');

            Requirements::customScript(
                    sprintf("
                (function($) {
                    $(document).ready(function() { 
                        //Date picker
                        $('#Form_SearchForm_q_Created').daterangepicker({
                            arrows: false,
                            dateFormat: 'dd.mm.yy',
                            presetRanges: [
                                {text: '%s', dateStart: 'today', dateEnd: 'today' },
                                {text: '%s', dateStart: 'today-7days', dateEnd: 'today' },
                                {text: '%s', dateStart: function(){ return Date.parse('today').moveToFirstDayOfMonth();  }, dateEnd: 'today' },
                                {text: '%s', dateStart: function(){ var x= Date.parse('today'); x.setMonth(0); x.setDate(1); return x; }, dateEnd: 'today' },
                                {text: '%s', dateStart: function(){ return Date.parse('1 month ago').moveToFirstDayOfMonth();  }, dateEnd: function(){ return Date.parse('1 month ago').moveToLastDayOfMonth();  } }
                            ],
                            presets: {
                                specificDate: '%s',
                                allDatesBefore: '%s',
                                allDatesAfter: '%s',
                                dateRange: '%s'
                            },
                            rangeStartTitle: '%s',
                            rangeEndTitle: '%s',
                            nextLinkText: '%s',
                            prevLinkText: '%s'
                        });
                    });
                })(jQuery);",
                            _t(OrderAdmin::class . '.DateRangePickerTODAY', 'Today'),
                            _t(OrderAdmin::class . '.DateRangePickerLAST_7_DAYS', 'Last 7 days'),
                            _t(OrderAdmin::class . '.DateRangePickerTHIS_MONTH', 'This month'),
                            _t(OrderAdmin::class . '.DateRangePickerTHIS_YEAR', 'This year'),
                            _t(OrderAdmin::class . '.DateRangePickerLAST_MONTH', 'Last month'),
                            _t(OrderAdmin::class . '.DateRangePickerDATE', 'Date'),
                            _t(OrderAdmin::class . '.DateRangePickerALL_BEFORE', 'All before'),
                            _t(OrderAdmin::class . '.DateRangePickerALL_AFTER', 'All after'),
                            _t(OrderAdmin::class . '.DateRangePickerPERIOD', 'Period'),
                            _t(OrderAdmin::class . '.DateRangePickerSTART_DATE', 'Start date'),
                            _t(OrderAdmin::class . '.DateRangePickerEND_DATE', 'End date'),
                            _t(OrderAdmin::class . '.DateRangePickerNEXT', 'Next'),
                            _t(OrderAdmin::class . '.DateRangePickerPREVIOUS', 'Previous')
                    )
            );
        });
        return parent::init();
    }
    
    /**
     * Returns the current model context list.
     * Adds a filter by order or payment status if necessary by the chosen tab.
     * 
     * @return \SilverCart\ORM\DataList
     */
    public function getList()
    {
        return $this->getStatusList($this->getCurrentTab());
    }
    
    /**
     * Adds a filter by order or payment status dependent on the given tab.
     * 
     * @param string $tab Tab
     * 
     * @return \SilverCart\ORM\DataList
     */
    protected function getStatusList($tab)
    {
        $list = parent::getList();
        if (!is_null($tab)
         && $tab !== 'all'
        ) {
            list($statusType, $statusCode) = explode('-', $tab);
            if ($statusType === 'order') {
                $orderStatus = OrderStatus::get()->filter('Code', $statusCode)->first();
                if ($orderStatus instanceof OrderStatus
                 && $orderStatus->exists()
                ) {
                    $list = $list->filter('OrderStatusID', $orderStatus->ID);
                }
            } elseif ($statusType === 'payment') {
                $paymentStatus = PaymentStatus::get()->filter('Code', $statusCode)->first();
                if ($paymentStatus instanceof PaymentStatus
                 && $paymentStatus->exists()
                ) {
                    $list = $list->filter('PaymentStatusID', $paymentStatus->ID);
                }
            }
        }
        return $list;
    }
    
    /**
     * Adds some additional order tabs to have a fast way to filter orders by
     * important order or payment status.
     * 
     * @return \SilverStripe\ORM\ArrayList
     */
    protected function getManagedModelTabs()
    {
        $forms = parent::getManagedModelTabs();
        $tabs  = ['order-new', 'order-inprogress', 'payment-open'];
        $link  = $this->Link($this->sanitiseClassName(Order::class));
        
        if (strpos($link, '?') === false) {
            $link = "{$link}?tab=";
        } else {
            $link = "{$link}&tab=";
        }
        
        foreach ($forms as $form) {
            if ($form->ClassName === Order::class) {
                $form->Link          = $link . 'all';
                $form->LinkOrCurrent = (Order::class == $this->modelClass && $this->getCurrentTab() === 'all') ? 'current' : 'link';
            }
        }
        
        foreach ($tabs as $tab) {
            list($statusType, $statusCode) = explode('-', $tab);
            $forms->push(ArrayData::create([
                        'Title'         => _t(Order::class . '.ModelAdminTab' . ucfirst($statusType) . ucfirst($statusCode), ucfirst($statusCode)) . " ({$this->getStatusList($tab)->count()})",
                        'ClassName'     => Order::class,
                        'Link'          => $link . $tab,
                        'LinkOrCurrent' => (Order::class == $this->modelClass && $this->getCurrentTab() === $tab) ? 'current' : 'link'
            ]));
        }
        

        return $forms;
    }
    
    /**
     * Returns the current (tab stored in session).
     * If given by HTTP GET parameter, the current tab will be updated.
     * 
     * @return string
     */
    protected function getCurrentTab()
    {
        if (is_null($this->currentTab)) {
            $this->currentTab = $this->getRequest()->getVar('tab');
            if (!is_null($this->currentTab)) {
                Tools::Session()->set(self::SESSION_KEY_TAB, $this->currentTab);
                Tools::saveSession();
            } else {
                $this->currentTab = Tools::Session()->get(self::SESSION_KEY_TAB);
            }
        }
        if (is_null($this->currentTab)) {
            $this->currentTab = 'all';
        }
        return $this->currentTab;
    }
}