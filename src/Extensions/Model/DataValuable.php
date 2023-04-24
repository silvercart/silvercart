<?php

namespace SilverCart\Extensions\Model;

use SilverCart\Model\DataValue;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\SS_List;

/**
 * Custom data value.
 * 
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.03.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property DataObject $owner Owner
 */
class DataValuable extends DataExtension
{
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static $has_many = [
        'DataValues' => DataValue::class,
    ];
    /**
     * Owned relations.
     * 
     * @var string[]
     */
    private static $owns = [
        'DataValues',
    ];
    
    /**
     * Updates the field labels.
     *
     * @param array &$labels Labels to update
     *
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels['DataValues'] = DataValue::singleton()->i18n_plural_name();
    }
    
    /**
     * Updates the CMS fields.
     * 
     * @param FieldList $fields Fields to update
     * 
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        if (!$this->owner->DataValues()->exists()) {
            $fields->removeByName('DataValues');
            return;
        }
    }
    
    /**
     * On before delete.
     * 
     * @return void
     */
    public function onBeforeDelete() : void
    {
        foreach ($this->owner->DataValues() as $dataValue) {
            /* @var $dataValue DataValue */
            $dataValue->delete();
        }
    }
    
    /**
     * Adds a new DataValue to the extended DataObject.
     * 
     * @param string $name    Data name
     * @param string $title   Data title
     * @param string $value   Data value
     * @param string $type    Data type
     * @param bool   $isUniqe Is unique?
     * 
     * @return DataValue
     */
    public function addDataValue(string $name, string $title, string $value, string $type = DataValue::TYPE_STRING, bool $isUniqe = true) : DataValue
    {
        if ($isUniqe) {
            $dataValue = $this->getDataValueByName($name);
            if ($dataValue === null) {
                $dataValue = DataValue::create();
            }
        } else {
            $dataValue = DataValue::create();
        }
        $dataValue->DataName  = $name;
        $dataValue->DataType  = $type;
        $dataValue->DataTitle = $title;
        $dataValue->DataValue = $value;
        $dataValue->write();
        $this->owner->DataValues()->add($dataValue);
        return $dataValue;
    }
    
    /**
     * Removes the related DataValues with the given name.
     * 
     * @param string $name Name
     * 
     * @return DataObject
     */
    public function removeDataValuesByName(string $name) : DataObject
    {
        foreach ($this->getDataValuesByName($name) as $dataValue) {
            /* @var $dataValue DataValue */
            $dataValue->delete();
        }
        return $this->owner;
    }
    
    /**
     * Returns all related DataValues with the given data $name.
     * 
     * @param string $name Data name
     * 
     * @return SS_List
     */
    public function getDataValuesByName(string $name) : SS_List
    {
        return $this->owner->DataValues()->filter('DataName', $name);
    }
    
    /**
     * Returns the first related DataValue with the given data $name.
     * 
     * @param string $name Data name
     * 
     * @return DataValue|null
     */
    public function getDataValueByName(string $name) : DataValue|null
    {
        return $this->getDataValuesByName($name)->first();
    }
    
    /**
     * Returns whether the related DataValue with the given data $name exists.
     * 
     * @param string $name Data name
     * 
     * @return bool
     */
    public function hasDataValue(string $name) : bool
    {
        return $this->getDataValuesByName($name)->exists();
    }
}