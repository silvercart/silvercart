<?php

/**
 * backend interface to CRUD the defined classes
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class OrderAdmin extends ModelAdmin {

    public static $managed_models = array(
        'Order',
        'Country',
        'OrderStatus',
        'ShippingMethod',
        'Carrier',
        'Zone',
        'PaymentMethod',
        'ShippingFee'
    );
    public static $url_segment = 'orders';
    public static $menu_title = 'Bestellungen';

}
