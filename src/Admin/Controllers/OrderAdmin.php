<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\Order\Order;
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
class OrderAdmin extends ModelAdmin {

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
    private static $managed_models = array(
        Order::class,
    );
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @param bool $skipUpdateInit Set to true to skip the parents updateInit extension
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.04.2018
     */
    protected function init($skipUpdateInit = false) {
        parent::init(true);
        
        Requirements::javascript('silvercart/silvercart:client/admin/javascript/jquery-ui/jquery.ui.datepicker.js');
        Requirements::javascript('silvercart/silvercart:client/admin/javascript/jquery-ui/jquery.ui.daterangepicker.date.js');
        Requirements::javascript('silvercart/silvercart:client/admin/javascript/jquery-ui/jquery.ui.daterangepicker.js');
        Requirements::css('silvercart/silvercart:client/admin/css/jquery-ui/daterangepicker.css');
        
        Requirements::customScript(
                sprintf(
                        "
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

        $this->extend('updateInit');
    }
    
}
