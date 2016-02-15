<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for SilvercartOrders.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 16.01.2012
 * @license see license file in modules root directory
 */
class SilvercartOrderAdmin extends SilvercartModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'orders';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 10;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-orders';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartOrder'
    );
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @param bool $skipUpdateInit Set to true to skip the parents updateInit extension
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function init($skipUpdateInit = false) {
        parent::init(true);
        $baseUrl = SilvercartTools::getBaseURLSegment();

        Requirements::javascript($baseUrl.FRAMEWORK_DIR.'/thirdparty/jquery-ui/jquery-ui-1.8rc3.custom.js');
        Requirements::javascript($baseUrl.FRAMEWORK_DIR.'/thirdparty/jquery-ui/jquery.datepicker.js');
        Requirements::css($baseUrl.FRAMEWORK_DIR.'/thirdparty/jquery-ui-themes/smoothness/jquery-ui-1.8rc3.custom.css');

        Requirements::javascript($baseUrl.'silvercart/admin/javascript/jquery-ui/date-range-picker/js/date.js');
        Requirements::javascript($baseUrl.'silvercart/admin/javascript/jquery-ui/date-range-picker/js/daterangepicker.jQuery.js');
        Requirements::css($baseUrl.'silvercart/admin/javascript/jquery-ui/date-range-picker/css/ui.daterangepicker.css');
        
        Requirements::customScript(
                sprintf(
                        "
            (function($) {
                $(document).ready(function() { 
                    //Date picker
                    $('#Form_SearchForm_q-Created').daterangepicker({
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
                        _t('SilvercartDateRangePicker.TODAY'),
                        _t('SilvercartDateRangePicker.LAST_7_DAYS'),
                        _t('SilvercartDateRangePicker.THIS_MONTH'),
                        _t('SilvercartDateRangePicker.THIS_YEAR'),
                        _t('SilvercartDateRangePicker.LAST_MONTH'),
                        _t('SilvercartDateRangePicker.DATE'),
                        _t('SilvercartDateRangePicker.ALL_BEFORE'),
                        _t('SilvercartDateRangePicker.ALL_AFTER'),
                        _t('SilvercartDateRangePicker.PERIOD'),
                        _t('SilvercartDateRangePicker.START_DATE'),
                        _t('SilvercartDateRangePicker.END_DATE'),
                        _t('SilvercartDateRangePicker.NEXT'),
                        _t('SilvercartDateRangePicker.PREVIOUS')
                )
        );

        $this->extend('updateInit');
    }
    
    /**
     * Manipulate search form to add some grouping.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.06.2014
     */
    public function SearchForm() {
        $searchForm             = parent::SearchForm();
        $fields                 = $searchForm->Fields();
        $order                  = singleton('SilvercartOrder');
        
        $basicLabelField        = new HeaderField(  'BasicLabelField',          $order->fieldLabel('BasicData'));
        $customerLabelField     = new HeaderField(  'CustomerLabelField',       $order->fieldLabel('CustomerData'));
        $positionLabelField     = new HeaderField(  'PositionLabelField',       $order->fieldLabel('OrderPositionData'));
        $miscLabelField         = new HeaderField(  'MiscLabelField',           $order->fieldLabel('MiscData'));
        
        $origOrderStatusField   = $fields->dataFieldByName('q[SilvercartOrderStatus__ID]');
        $orderStatusField       = new SilvercartMultiDropdownField('q[SilvercartOrderStatus__ID]', $origOrderStatusField->Title(), $origOrderStatusField->getSource());
        $positionQuantityField  = new TextField(    'q[OrderPositionQuantity]',                    $order->fieldLabel('OrderPositionQuantity'));
        $positionIsLimitField   = new CheckboxField('q[OrderPositionIsLimit]',                     $order->fieldLabel('OrderPositionIsLimit'));
        $limitField             = new TextField(    'q[SearchResultsLimit]',                       $order->fieldLabel('SearchResultsLimit'));
        
        $fields->insertBefore($basicLabelField,                      'q[OrderNumber]');
        $fields->insertAfter($fields->dataFieldByName('q[Created]'), 'q[OrderNumber]');
        $fields->insertAfter($orderStatusField,                      'q[IsSeen]');
        $fields->insertBefore($customerLabelField,                   'q[Member__CustomerNumber]');
        $fields->insertBefore($positionLabelField,                   'q[SilvercartOrderPositions__ProductNumber]');
        $fields->insertAfter($positionQuantityField,                 'q[SilvercartOrderPositions__ProductNumber]');
        $fields->insertAfter($positionIsLimitField,                  'q[OrderPositionQuantity]');
        $fields->insertAfter($miscLabelField,                        'q[OrderPositionIsLimit]');
        $fields->insertAfter($limitField,                            'q[MiscLabelField]');
        
        $fields->dataFieldByName('q[SilvercartOrderStatus__ID]')->setEmptyString(                       _t('SilvercartOrderSearchForm.PLEASECHOOSE'));
        $fields->dataFieldByName('q[SilvercartPaymentMethod__ID]')->setEmptyString(                     _t('SilvercartOrderSearchForm.PLEASECHOOSE'));
        $fields->dataFieldByName('q[SilvercartShippingMethod__ID]')->setEmptyString(                    _t('SilvercartOrderSearchForm.PLEASECHOOSE'));
        $fields->dataFieldByName('q[SilvercartShippingAddress__SilvercartCountry__ID]')->setEmptyString(_t('SilvercartOrderSearchForm.PLEASECHOOSE'));
        
        return $searchForm;
    }
    
}
