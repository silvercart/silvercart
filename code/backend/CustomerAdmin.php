<?php

/**
 * admin backend for customers; CRUD for the defined classes
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 22.10.2010
 */
class CustomerAdmin extends ModelAdmin {

    public static $managed_models = array(
        'RegularCustomer',
        'AnonymousCustomer',
        'Member',
        'BusinessCustomer'
    );
    public static $url_segment = 'customers';
    public static $menu_title = 'customers';

    /**
     * constructor
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function  __construct() {
        self::$menu_title = _t('CustomerAdmin.customers', 'customers');
        parent::__construct();
    }

}