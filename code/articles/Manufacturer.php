<?php

/**
 * abstract for a manufacturer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class Manufacturer extends DataObject {
        static $singular_name = "Hersteller";
    static $plural_name = "Hersteller";

    public static $db = array(
        'Title' => 'VarChar',
        'URL' => 'VarChar'
    );
    public static $has_one = array(
        'logo' => 'Image'
    );
    public static $has_many = array(
        'articles' => 'Article'
    );
    public static $summary_fields = array(
        'Title' => 'Name'
    );

}

