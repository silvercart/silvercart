<?php

namespace SilverCart\DataFormatter;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\RestfulServer\DataFormatter\XMLDataFormatter as DefaultXMLDataFormatter;

if (!class_exists(DefaultXMLDataFormatter::class)) {
    return;
}

/**
 * Formats a DataObject's member fields into an XML string.
 * Adds support for casted fields.
 * 
 * @package SilverCart
 * @subpackage DataFormatter
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.06.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class XMLDataFormatter extends DefaultXMLDataFormatter
{
    use ExtendedDataFormatter;
    /**
     * Priority of this DataFormatter.
     * 
     * @var int
     */
    private static int $priority = 51;
}