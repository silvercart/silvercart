<?php
/**
 * abstract for destinguishing customers that may have special prices
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 */
class CustomerCategory extends DataObject {
    public static $singular_name = "Kundengruppe";
    public static $plural_name = "Kundengruppen";
    public static $db = array(
        'Title' => 'VarChar'
    );
    public static $has_many = array(
        'prices' => 'Price',
        'customers' => 'Member'
    );
    public static $summary_fields = array(
        "Title" => 'Name'
    );
}
