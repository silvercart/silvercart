<?php

namespace SilverCart\Extensions\Broarm\CookieConsent\Model;

use Broarm\CookieConsent\Model\CookieGroup;
use SilverStripe\ORM\DataExtension;

/**
 * Extension for CookieGroup.
 * 
 * @package SilverCart
 * @subpackage Extensions\CookieConsent\Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 01.12.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \Broarm\CookieConsent\Model\CookieGroup $owner Owner
 */
class CookieGroupExtension extends DataExtension
{
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'Sort' => 'Int',
    ];
    /**
     * Default sort.
     *
     * @var array
     */
    private static $default_sort = [
        'Sort ASC',
    ];
    
    /**
     * On before write.
     * 
     * @return void
     */
    public function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if ((int) $this->owner->Sort === 0) {
            $this->owner->Sort = CookieGroup::get()->max('Sort') + 1;
        }
    }
}