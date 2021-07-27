<?php

namespace SilverCart\Model\Customer;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\DataObject;

class DeletedCustomer extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name.
     * 
     * @var string
     */
    private static $table_name = 'SilverCart_DeletedCustomer';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static $db = [
        'CustomerID' => 'Int',
        'ReasonID'   => 'Int',
        'ReasonText' => 'Text',
    ];

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }

    /**
     * i18n for field labels
     *
     * @param bool $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, []);
    }
}