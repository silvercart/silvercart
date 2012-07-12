<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright 2012 pixeltricks GmbH
 * @since 16.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartOrderAdmin extends ModelAdmin {

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
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Silvercart Orders';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartOrder' => array(
            'collection_controller' => 'SilvercartOrder_CollectionController',
            'record_controller'     => 'SilvercartOrder_RecordController'
        )
    );

    /**
     * Class name of the form field used for the results list.  Overloading this in subclasses
     * can let you customise the results table field.
     * 
     * @var string
     */
    protected $resultsTableClassName = 'SilvercartEditableTableListField';

    /**
     * Constructor
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartOrder.PLURALNAME');
        
        parent::__construct();
    }
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.07.2012
     */
    public function init() {
        parent::init();
        $baseUrl = SilvercartTools::getBaseURLSegment();

        Requirements::javascript($baseUrl.'sapphire/thirdparty/jquery-ui/jquery-ui-1.8rc3.custom.js');
        Requirements::javascript($baseUrl.'sapphire/thirdparty/jquery-ui/jquery.datepicker.js');
        Requirements::css($baseUrl.'sapphire/thirdparty/jquery-ui-themes/smoothness/jquery-ui-1.8rc3.custom.css');

        Requirements::javascript($baseUrl.'silvercart/script/jQuery-UI-Date-Range-Picker/js/date.js');
        Requirements::javascript($baseUrl.'silvercart/script/jQuery-UI-Date-Range-Picker/js/daterangepicker.jQuery.js');
        Requirements::css($baseUrl.'silvercart/script/jQuery-UI-Date-Range-Picker/css/ui.daterangepicker.css');

        $orderStatusDropdownLink = Director::baseURL();
        if (empty($orderStatusDropdownLink)) {
            $orderStatusDropdownLink = '/';
        }
        $orderStatusDropdownLink .= $this->Link();
        $orderStatusDropdownLink .= 'SilvercartOrder/OrderStatusDropdown';
        
        Requirements::customScript(
                sprintf(
                        "
            function silvercartBatch_changeOrderStatus() {
                (function($) {
                    $.ajax({
                        url     : '%s',
                        async   : false,
                        success : function(data) {
                            $('.silvercart-batch-option-callback-target').html(data);
                            $('select[name=\"SilvercartOrderStatus\"]').live('change', function() {
                                var status = $('select[name=\"SilvercartOrderStatus\"] option:selected').val();
                                $('input[name=\"silvercart-batch-option-callback-data\"]').val(status);
                            });
                        }
                    });
                })(jQuery);
            }
            (function($) {
                $(document).ready(function() { 
                    //Date picker
                    $('#Form_SearchForm_SilvercartOrder_Created').daterangepicker({
                        arrows: false,
                        dateFormat: 'dd.mm.yy'
                    });
                });
            })(jQuery);",
                        $orderStatusDropdownLink
                )
        );

        $this->extend('updateInit');
    }
}
