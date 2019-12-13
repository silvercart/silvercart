<?php

namespace SilverCart\ORM\Filters;

/**
 * Acts exactly like the default StartsWithFilter but adds null value support to
 * exclude one.
 * 
 * @package SilverCart
 * @subpackage ORM\Filters
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.12.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class StartsWithOrNullFilter extends PartialMatchOrNullFilter
{
    /**
     * Returns the match pattern for the given $value.
     * 
     * @param string $value Value
     * 
     * @return string
     */
    protected function getMatchPattern($value) : string
    {
        return "$value%";
    }
}