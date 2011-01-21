<?php

/**
 * All article properties that need a translation end up here.
 * @author Roland Lehmann
 * @copyright Pixeltricks GmbH
 */
class ArticleTexts extends DataObject {

    static $singular_name = "Artikelübersetzungstext";
    static $plural_name = "Artikelübersetzungstexte";
    public static $db = array(
        'Title' => 'VarChar',
        'LongDescription' => 'Text',
        'ShortDescription' => 'VarChar',
        'MetaDescription' => 'VarChar',
        'MetaTitle' => 'VarChar',
        'MetaKeywords' => 'VarChar'
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
        'owner' => 'Article'
    );
    public static $has_many = array(
    );
    public static $many_many = array(
    );
    public static $belongs_many_many = array(
    );

    //public static $searchable_fields = array();
    //public static $summary_fields = array();

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
