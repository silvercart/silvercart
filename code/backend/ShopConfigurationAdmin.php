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
        'Tax',
        'OrderStatus',
        'ShopEmail'
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
    /**
     * The collection controller class to use for the shop configuration.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $collection_controller_class = 'ShopConfigurationAdmin_CollectionController';

    /**
     * constructor
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function __construct() {
        self::$menu_title = _t('ShopConfigurationAdmin.SILVERCART_CONFIG', 'silvercart configuration');
        parent::__construct();
    }

}

/**
 * Modifies the model admin search panel.
 *
 * @package silvercart
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 31.01.2011
 * @license none
 */
class ShopConfigurationAdmin_CollectionController extends ModelAdmin_CollectionController {

    /**
     * Return a modified search form.
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function SearchForm() {
        $form = parent::SearchForm();

        switch ($this->getModelClass()) {
            case 'Country':
                break;
            case 'Zone':
                break;
            case 'PaymentMethod':
                $form = $this->adjustSearchFormForPaymentMethod($form);
                break;
            case 'ShippingMethod':
                break;
            case 'ShippingFee':
                break;
            case 'Carrier':
                break;
            case 'OrderStatus':
                break;
        }

        return $form;
    }

    /**
     * Adjust the search form for the PaymentMethod model.
     *
     * @param Form $form the searchform object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    protected function adjustSearchFormForPaymentMethod(Form $form) {
        return $form;
    }

}