<?php

namespace SilverCart\Extensions\ORM\FieldType;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBHTMLText;

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
    
    /**
     * Returns the money amount with currency in a nice format with the decimals
     * displayed as superscript.
     * Format:
     * {$predecimals}{$separator}<sup>{$decimals}</sup> {$currency}
     * 
     * @return DBHTMLText
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.05.2019
     */
    public function NiceSup() : DBHTMLText
    {
        $nice        = $this->owner->Nice();
        $predecimals = mb_substr($nice, 0, -5);
        $separator   = mb_substr($nice, -5, 1);
        $decimals    = mb_substr($nice, -4, 2);
        $currency    = mb_substr($nice, -1);
        $niceSup     = "{$predecimals}{$separator}<sup>{$decimals}</sup> {$currency}";
        return DBHTMLText::create()
                ->setValue($niceSup);
    }
}