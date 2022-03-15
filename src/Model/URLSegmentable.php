<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;
use SilverStripe\ORM\DataObject;

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
     * Returns the matching object with the given URL segment
     * 
     * @param string $urlSegment URL segment
     * @param array  $filter     Additional filter
     * 
     * @return $this|null
     */
    public static function getByURLSegment(string $urlSegment, array $filter = []) : ?DataObject
    {
        $filter = array_merge($filter, [
            'URLSegment' => $urlSegment,
        ]);
        return self::get()->filter($filter)->first();
    }


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
     * @param bool $write Write object after generating the URL segment? (default: true)
     * 
     * @return string
     */
    public function generateURLSegment(bool $write = true) : string
    {
        $index      = 2;
        $urlSegment = $urlSegmentBase = Tools::string2urlSegment($this->Title);
        do {
            $records  = self::get()
                    ->exclude('ID', $this->ID)
                    ->filter('URLSegment', $urlSegment);
            if ($this->hasMethod('updateGenerateURLSegmentRecords')) {
                $this->updateGenerateURLSegmentRecords($records);
            }
            $existing = $records->count();
            if ($existing > 0) {
                $urlSegment = "{$urlSegmentBase}-{$index}";
                $index++;
            }
        } while ($existing > 0);
        self::$generatedURLSegment[$this->ID] = $urlSegment;
        $this->URLSegment = $urlSegment;
        $this->setField('URLSegment', $urlSegment);
        if ($write) {
            $this->write();
        }
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