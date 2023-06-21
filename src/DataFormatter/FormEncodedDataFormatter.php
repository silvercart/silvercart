<?php

namespace SilverCart\DataFormatter;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\RestfulServer\DataFormatter\FormEncodedDataFormatter as DefaultFormEncodedDataFormatter;

if (!class_exists(DefaultFormEncodedDataFormatter::class)) {
    return;
}

/**
 * Accepts form encoded strings and converts them
 * to a valid PHP array via {@link parse_str()}.
 *
 * Example when using cURL on commandline:
 * <code>
 * curl -d "Name=This is a new record" http://host/api/v1/(DataObject)
 * curl -X PUT -d "Name=This is an updated record" http://host/api/v1/(DataObject)/1
 * </code>
 * Adds support for casted fields.
 * 
 * @package SilverCart
 * @subpackage DataFormatter
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.06.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class FormEncodedDataFormatter extends DefaultFormEncodedDataFormatter
{
    use ExtendedDataFormatter;
    /**
     * Priority of this DataFormatter.
     * 
     * @var int
     */
    private static int $priority = 51;
}