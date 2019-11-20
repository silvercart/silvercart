<?php

namespace SilverCart\Extensions\ORM\FieldType;

use NumberFormatter;
use SilverStripe\Core\Extension;
use SilverStripe\i18n\i18n;

/**
 * Extension for SilverStripe\ORM\FieldType\DBInt.
 *
 * @package SilverCart
 * @subpackage Extensions\ORM\FieldType
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 18.11.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\ORM\FieldType\DBInt $owner Owner
 */
class DBIntExtension extends Extension
{
    /**
     * Returns the number, with commas and thousands separator dependent on the 
     * current i18n locale added as appropriate, eg:
     * • 9 988 776,65 in France
     * • 9.988.776,65 in Germany
     * • 9,988,776.65 in the United States
     * 
     * @return string
     */
    public function FormattedI18n() : string
    {
        $locale      = i18n::get_locale();
        $formatStyle = NumberFormatter::TYPE_INT32;
        $formatter   = new NumberFormatter($locale, $formatStyle);
        return $formatter->format($this->owner->getValue());
    }
}