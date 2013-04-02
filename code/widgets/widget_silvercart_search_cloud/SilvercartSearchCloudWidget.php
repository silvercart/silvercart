<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 * 
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * Provides a search form as a widget.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.06.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartSearchCloudWidget extends SilvercartWidget {
    
    /**
     * attributes
     *
     * @var array
     */
    public static $db = array(
        'TagsPerCloud'  => 'Int',
        'FontSizeCount' => 'Int',
        'isContentView' => 'Boolean',
    );
    
    /**
     * default values for attributes
     *
     * @var array
     */
    public static $defaults = array(
        'TagsPerCloud'  => 10,
        'FontSizeCount' => 5,
    );

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'TagsPerCloud'  => _t('SilvercartSearchCloudWidget.TAGSPERCLOUD'),
                    'FontSizeCount' => _t('SilvercartSearchCloudWidget.FONTSIZECOUNT'),
                    'isContentView' => _t('SilvercartProductSliderWidget.IS_CONTENT_VIEW'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the input fields for this widget.
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        
        return $fields;
    }
    
    /**
     * Returns the most searched queries as a DataList
     *
     * @return DataList 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 05.06.2012
     */
    public function TagsForCloud() {
        $searchTags = SilvercartSearchQuery::get_most_searched($this->TagsPerCloud);
        
        if (!$searchTags) {
            return false;
        }
        
        /*
         * The following block is a replacement for the call DataObjectSet::groupBy()
         * which does not exist any more
         */
        $searchTagCounts = array();
        foreach ($searchTags as $item) {
                $key = ($item->hasMethod('Count')) ? $item->count() : $item->Count;

                if (!isset($searchTagCounts[$key])) {
                        $searchTagCounts[$key] = new ArrayList();
                }
                $searchTagCounts[$key]->push($item);
        }
        
        
        foreach ($searchTagCounts as $index => $searchTagCount) {
            $searchTagCounts[$index] = $index;
        }
        $fontSizeRanges     = $this->getFontSizeRanges($searchTagCounts);
        foreach ($searchTags as $searchTag) {
            foreach ($fontSizeRanges as $fontSize => $fontSizeRange) {
                if ($searchTag->Count >= $fontSizeRange['Min'] &&
                    $searchTag->Count <= $fontSizeRange['Max']) {
                    $searchTag->FontSize = $fontSize;
                }
            }
        }
        
        $searchTags->sort('SearchQuery');
        
        return $searchTags;
    }
    
    
    /**
     * Returns the font size ranges to use in the tag cloud.
     * A range 1 -> 7-12 means that a tag which is use between 7 and 12 times
     * will get the font size 1 (which is defined via css class).
     *
     * @param array $existingTagCounts A list of all existing tag counts
     * 
     * @return array
     */
    protected function getFontSizeRanges($existingTagCounts) {
        $fontSizeRanges = array();
        if (count($existingTagCounts) > $this->FontSizeCount) {
            $maximum = array_shift($existingTagCounts);
            $rangeSize = ceil($maximum / $this->FontSizeCount);
            $min = 1;
            $max = $rangeSize;
            for ($x = 0; $x < $this->FontSizeCount; $x++) {
                $fontSizeRanges[] = array(
                    'Min' => $min,
                    'Max' => $max,
                );
                $min = $max + 1;
                $max = $max + $rangeSize;
            }
        } elseif (count($existingTagCounts) == $this->FontSizeCount ||
                    count($existingTagCounts) < $this->FontSizeCount) {
            $existingTagCounts = array_reverse($existingTagCounts);
            foreach ($existingTagCounts as $tagCount) {
                $fontSizeRanges[] = array(
                    'Min' => $tagCount,
                    'Max' => $tagCount,
                );
            }
        }
        return $fontSizeRanges;
    }
}

/**
 * Provides a search form as a widget.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.06.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartSearchCloudWidget_Controller extends SilvercartWidget_Controller {
    
    
}
