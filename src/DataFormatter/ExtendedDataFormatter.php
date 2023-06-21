<?php

namespace SilverCart\DataFormatter;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\RestfulServer\DataFormatter\JSONDataFormatter as DefaultJSONDataFormatter;

if (!class_exists(DefaultJSONDataFormatter::class)) {
    return;
}

/**
 * Adds support for casted fields.
 * 
 * @package SilverCart
 * @subpackage DataFormatter
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.06.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait ExtendedDataFormatter
{
    /**
     * Returns all fields on the object which should be shown in the output. Can 
     * be customised through {@link self::setCustomFields()}.
     * Adds support for casted fields.
     *
     * @param DataObject $obj Data Object
     * 
     * @return array
     */
    protected function getFieldsForObj($obj) : array
    {
        $dbFields = parent::getFieldsForObj($obj);
        if (!is_array($this->customFields)) {
            $castedFields = (array) $obj->config()->casting;
            foreach ($castedFields as $castedFieldName => $castedFieldType) {
                $castedField = $obj->obj($castedFieldName);
                if ($castedField instanceof DBField) {
                    continue;
                }
                unset($castedFields[$castedFieldName]);
            }
            $dbFields = array_merge($dbFields, $castedFields);
        }
        if (is_array($this->removeFields)) {
            $dbFields = array_diff_key(
                $dbFields ?? [],
                array_combine($this->removeFields ?? [], $this->removeFields ?? [])
            );
        }
        return $dbFields;
    }
}