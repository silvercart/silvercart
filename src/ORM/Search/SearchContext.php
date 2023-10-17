<?php

namespace SilverCart\ORM\Search;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\SelectField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\Search\SearchContext as SilverStripeSearchContext;
use SilverStripe\View\ArrayData;
use function singleton;

/**
 * Extended SearchContext.
 *
 * @package SilverCart
 * @subpackage ORM\Search
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchContext extends SilverStripeSearchContext
{
    /**
     * Workaroung to add proper summary support for MultiDropdownFields.
     * 
     * @return ArrayList
     */
    public function getSummary() : ArrayList
    {
        $list = ArrayList::create();
        foreach ($this->searchParams as $searchField => $searchValue) {
            if (empty($searchValue)) {
                continue;
            }
            $filter = $this->getFilter($searchField);
            if (!$filter) {
                continue;
            }
            $field = $this->fields->fieldByName($filter->getFullName());
            if (!$field) {
                continue;
            }
            // For dropdowns, checkboxes, etc, get the value that was presented to the user
            // e.g. not an ID
            if ($field->hasMethod('getSummary')) {
                $searchValue = $field->getSummary($searchValue);
            } elseif ($field instanceof SelectField) {
                $source = $field->getSource();
                if (isset($source[$searchValue])) {
                    $searchValue = $source[$searchValue];
                }
                // For checkboxes, it suffices to simply include the field in the list, since it's binary
            } elseif ($field instanceof CheckboxField) {
                $searchValue = null;
            }
            $list->push(ArrayData::create([
                'Field' => $field->Title(),
                'Value' => $searchValue,
            ]));
        }
        return $list;
    }
}