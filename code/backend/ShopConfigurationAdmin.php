<?php
/**
 * The Silvercart configuration backend.
 *
 * @package silvercart
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 31.01.2011
 * @license none
 */
class ShopConfigurationAdmin extends ModelAdmin {

    /**
     * Managed models
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $managed_models = array(
        'Country',
        'Zone',
        'PaymentMethod',
        'ShippingMethod',
        'ShippingFee',
        'Carrier',
        'OrderStatus'
    );

    /**
     * The URL segment
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $url_segment = 'silvercart-configuration';

    /**
     * The menu title
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $menu_title = 'Silvercart Konfiguration';
}