<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;

/**
 * 
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 03.11.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @mixin \SilverStripe\ORM\DataObject;
 */
trait URLSegmentable
{
    /**
     * The generated URL segment.
     *
     * @var string[]
     */
    protected static $generatedURLSegment = [];
    
    /**
     * Requires the default records.
     * 
     * @return void
     */
    public function requireDefaultURLSegmentableRecords() : void
    {
        foreach (self::get()->filter('URLSegment', null) as $expansion) {
            /* @var $expansion Expansion */
            $expansion->generateURLSegment();
        }
    }
    
    /**
     * Generates the URL segment.
     * 
     * @return string
     */
    public function generateURLSegment() : string
    {
        $index      = 2;
        $urlSegment = $urlSegmentBase = Tools::string2urlSegment($this->Title);
        do {
            $existing = self::get()
                    ->exclude('ID', $this->ID)
                    ->filter('URLSegment', $urlSegment)
                    ->count();
            if ($existing > 0) {
                $urlSegment = "{$urlSegmentBase}-{$index}";
                $index++;
            }
        } while ($existing > 0);
        self::$generatedURLSegment[$this->ID] = $urlSegment;
        $this->setField('URLSegment', $urlSegment);
        $this->write();
        return $urlSegment;
    }

    /**
     * Returns the URL segment.
     * 
     * @return string
     */
    public function getOrGenerateURLSegment() : string
    {
        $urlSegment = $this->getField('URLSegment');
        if (empty($urlSegment)
         && array_key_exists($this->ID, self::$generatedURLSegment)
        ) {
            $urlSegment = self::$generatedURLSegment[$this->ID];
        }
        if (empty($urlSegment)) {
            $urlSegment = $this->generateURLSegment();
        }
        return $urlSegment;
    }
}