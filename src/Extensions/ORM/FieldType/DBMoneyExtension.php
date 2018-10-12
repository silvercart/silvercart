<?php

namespace SilverCart\Extensions\ORM\FieldType;

use SilverStripe\Core\Extension;

/**
 * Extension for SilverStripe\ORM\FieldType\DBMoney.
 *
 * @package SilverCart
 * @subpackage Extensions_ORM_FieldType
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DBMoneyExtension extends Extension
{
    /**
     * Returns the amount formatted.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2018
     */
    public function NiceAmount()
    {
        if (!$this->owner->exists()) {
            return null;
        }
        $amount    = $this->owner->getAmount();
        $locale    = $this->owner->getLocale();
        $formatter = NumberFormatter::create($locale, NumberFormatter::DECIMAL);
        return $formatter->format($amount);
    }
}