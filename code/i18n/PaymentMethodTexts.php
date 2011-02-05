<?php

/**
 * ???
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 02.02.2011
 * @license none
 */
class PaymentMethodTexts extends DataObject {

    static $singular_name = "payment method text";
    static $plural_name = "payment method texts";
    public static $db = array(
        'Title' => 'VarChar',
        'Description' => 'Text'
    );
    /**
     * Enable translatable
     * @var <type> array
     * @author Roland Lehmann
     */
    static $extensions = array(
        "Translatable"
    );
    public static $has_one = array(
        'owner' => 'PaymentMethod'
    );
    public static $has_many = array(
    );
    public static $many_many = array(
    );
    public static $belongs_many_many = array(
    );

}
