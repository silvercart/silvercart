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

        Requirements::javascript($baseUrl.'silvercart/script/jQuery-UI-Date-Range-Picker/js/date.js');
        Requirements::javascript($baseUrl.'silvercart/script/jQuery-UI-Date-Range-Picker/js/daterangepicker.jQuery.js');
        Requirements::css($baseUrl.'silvercart/script/jQuery-UI-Date-Range-Picker/css/ui.daterangepicker.css');
        
        Requirements::customScript(
                sprintf(
                        "
            (function($) {
                $(document).ready(function() { 
                    //Date picker
                    $('#Form_SearchForm_SilvercartOrder_Created').daterangepicker({
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
    
}
