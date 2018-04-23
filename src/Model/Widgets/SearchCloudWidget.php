<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\SearchQuery;
use SilverCart\Model\Widgets\Widget;
use SilverStripe\ORM\ArrayList;

/**
 * Provides a search tag cloud as a widget.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchCloudWidget extends Widget {
    
    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'TagsPerCloud'  => 'Int',
        'FontSizeCount' => 'Int',
        'isContentView' => 'Boolean',
    );
    
    /**
     * default values for attributes
     *
     * @var array
     */
    private static $defaults = array(
        'TagsPerCloud'  => 10,
        'FontSizeCount' => 5,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartSearchCloudWidget';

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
                    'TagsPerCloud'  => _t(SearchCloudWidget::class . '.TAGSPERCLOUD', 'Number of the search queries to show'),
                    'FontSizeCount' => _t(SearchCloudWidget::class . '.FONTSIZECOUNT', 'Number of different font sizes'),
                    'isContentView' => _t(ProductSliderWidget::class . '.IS_CONTENT_VIEW', 'Is Content view'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
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
        $searchTags = SearchQuery::get_most_searched($this->TagsPerCloud);
        
        if (!$searchTags) {
            return false;
        }
        
        $searchTags = $searchTags->sort('SearchQuery');
        $searchTagsArrayList = new ArrayList();
        
        /*
         * The following block is a replacement for the call DataObjectSet::groupBy()
         * which does not exist any more
         */
        $searchTagCounts = array();
        foreach ($searchTags as $item) {
                $key = ($item->hasMethod('count')) ? $item->count() : $item->Count;
                $searchTagCounts[$key] = $key;
        }
        $fontSizeRanges     = $this->getFontSizeRanges($searchTagCounts);
        foreach ($searchTags as $searchTag) {
            foreach ($fontSizeRanges as $fontSize => $fontSizeRange) {
                if ($searchTag->Count >= $fontSizeRange['Min'] &&
                    $searchTag->Count <= $fontSizeRange['Max']) {
                    $searchTag->FontSize = $fontSize;
                    $searchTagsArrayList->push($searchTag);
                }
            }
        }
        
        return $searchTagsArrayList;
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
        krsort($existingTagCounts);
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