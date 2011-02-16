<?php

/**
 * abstract for email template
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 02.02.2011
 * @license none
 */
class SilvercartEmailTemplates extends DataObject {

    static $singular_name = "email template";
    static $plural_name = "email templates";
    public static $db = array(
        'Category' => 'VarChar',
        'Action' => 'VarChar',
        'Subject' => 'VarChar',
        'HtmlText' => 'HTMLText',
        'PlainText' => 'Text'
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
    );
    public static $has_many = array(
    );
    public static $many_many = array(
    );
    public static $belongs_many_many = array(
    );

    /**
     * ???
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 02.02.2011
     * @return void
     */
    public function getByCategoryAction() {
        
    }

}
