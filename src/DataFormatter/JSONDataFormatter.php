<?php

namespace SilverCart\DataFormatter;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\RestfulServer\DataFormatter\JSONDataFormatter as DefaultJSONDataFormatter;

if (!class_exists(DefaultJSONDataFormatter::class)) {
    return;
}

/**
 * Formats a DataObject's member fields into a JSON string.
 * Adds support for casted fields.
 * 
 * @package SilverCart
 * @subpackage DataFormatter
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.06.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class JSONDataFormatter extends DefaultJSONDataFormatter
{
    use ExtendedDataFormatter;
    /**
     * Priority of this DataFormatter.
     * 
     * @var int
     */
    private static int $priority = 51;
}