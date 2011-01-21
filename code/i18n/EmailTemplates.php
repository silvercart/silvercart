<?php

/**
 *
 * @author Roland Lehmann
 * @copyright Pixeltricks GmbH
 */
class EmailTemplates extends DataObject {

    static $singular_name = "Emailvorlage";
    static $plural_name = "Emailvorlagen";
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

    //public static $searchable_fields = array();
    //public static $summary_fields = array();

    function getByCategoryAction() {
        
    }

    function requireDefaultRecords() {
        parent::requireDefaultRecords();
        $className = $this->ClassName;
        $objectAmount = 0;
        if (!DataObject::get($className)) {
            for ($i = 1; $i <= $objectAmount; $i++) {
                $obj = new $className();
                $obj->Title = $className . $i;
                $obj->write();
            }
        }
    }

}
