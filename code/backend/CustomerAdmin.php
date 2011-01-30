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
    public static $menu_title = 'Kunden';

}