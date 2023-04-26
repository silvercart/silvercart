<?php

namespace SilverCart\ORM\Search;

use Exception;
use InvalidArgumentException;
use SilverCart\ORM\Filters\DateRangeSearchFilter;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\SelectField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\Filters\SearchFilter;
use SilverStripe\ORM\Search\SearchContext as SilverStripeSearchContext;
use SilverStripe\View\ArrayData;
use function singleton;

/**
 * Provides the ability to search between two dates.
 *
 * @package SilverCart
 * @subpackage ORM\Search
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @author Ionut Lipciuc
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchContext extends SilverStripeSearchContext
{
    /**
     * Returns scaffolded search fields for UI.
     *
     * @return FieldList
     */
    public function getSearchFields() : FieldList
    {
        $fields = ($this->fields) ?: singleton($this->modelClass)->scaffoldSearchFields();
        if ($fields) {
            $dates = [];
            foreach ($fields as $field) {
                $type = get_class(singleton($this->modelClass)->obj($field->getName()));
                if ($type == DBDate::class || $type == DBDatetime::class) {
                    $dates[] = $field;
                }
            }
            foreach ($dates as $d) {
                $fields->removeByName($d->getName());
                $fields->push(new TextField($d->getName(), $d->Title()));
            }
        }
        return $fields;
    }

    /**
     * Apply the base table fields.
     * 
     * @return array
     */
    protected function applyBaseTableFields() : array
    {
        $classes   = ClassInfo::dataClassesFor($this->modelClass);
        $baseTable = DataObject::getSchema()->baseDataTable($this->modelClass);
        $fields    = ["{$baseTable}.*"];
        if ($this->modelClass != $classes[0]) {
            $fields[] = "{$classes[0]}.*";
        }
        $fields[] = "{$classes[0]}.ClassName AS RecordClassName";
        return $fields;
    }

    /**
     * Returns a SQL object representing the search context for the given
     * list of query parameters.
     *
     * @param array             $searchParams  Map of search criteria, mostly taken from $_REQUEST.
     *                                         If a filter is applied to a relationship in dot notation,
     *                                         the parameter name should have the dots replaced with double underscores,
     *                                         for example "Comments__Name" instead of the filter name "Comments.Name".
     * @param array|bool|string $sort          Database column to sort on.
     *                                         Falls back to {@link DataObject::$default_sort} if not provided.
     * @param array|bool|string $limit         Limit
     * @param DataList          $existingQuery Existing query
     * 
     * @return DataList
     * 
     * @throws Exception
     */
    public function getQuery($searchParams, $sort = false, $limit = false, $existingQuery = null) : DataList
    {
        if ($this->connective != "AND") {
            throw new Exception("SearchContext connective '{$this->connective}' not supported after ORM-rewrite.");
        }
        $this->setSearchParams($searchParams);
        $query = $this->prepareQuery($sort, $limit, $existingQuery);
        return $this->search($query);
    }

    /**
     * Perform a search on the passed DataList based on $this->searchParams.
     * 
     * @return DataList
     */
    private function search(DataList $query) : DataList
    {
        /** @var DataObject $modelObj */
        $modelObj = Injector::inst()->create($this->modelClass);
        $searchableFields = $modelObj->searchableFields();
        foreach ($this->searchParams as $searchField => $searchPhrase) {
            $searchField = str_replace('__', '.', $searchField ?? '');
            if ($searchField !== '' && $searchField === $modelObj->getGeneralSearchFieldName()) {
                $query = $this->generalFieldSearch($query, $searchableFields, $searchPhrase);
            } else {
                $query = $this->individualFieldSearch($query, $searchableFields, $searchField, $searchPhrase);
            }
            if ($searchField == 'Created') {
                $filter = $this->getFilter($searchField);
                if ($filter && get_class($filter) == DateRangeSearchFilter::class) {
                    $min_val = null;
                    $max_val = null;
                    $filter->setModel($this->modelClass);
                    if (strpos($searchPhrase, '-') === false) {
                        $min_val = $searchPhrase;
                    } else {
                        preg_match('/([^\s]*)(\s-\s(.*))?/i', $searchPhrase, $matches);
                        $min_val = (isset($matches[1])) ? $matches[1] : null;
                        if (isset($matches[3])) {
                            $max_val = $matches[3];
                        }
                    }
                    if ($min_val && $max_val) {
                        $filter->setMin($min_val);
                        $filter->setMax($max_val);
                        $query = $query->alterDataQuery(array($filter, 'apply'));
                    } else if ($min_val) {
                        $filter->setMin($min_val);
                        $query = $query->alterDataQuery(array($filter, 'apply'));
                    } else if ($max_val) {
                        $filter->setMax($max_val);
                        $query = $query->alterDataQuery(array($filter, 'apply'));
                    }
                }
            }
        }
        return $query;
    }

    /**
     * Prepare the query to begin searching
     *
     * @param array|bool|string $sort  Database column to sort on.
     * @param array|bool|string $limit Limit
     * 
     * @return DataList
     */
    private function prepareQuery($sort, $limit, $existingQuery) : DataList
    {
        if ($existingQuery) {
            if (!($existingQuery instanceof DataList)) {
                throw new InvalidArgumentException("existingQuery must be DataList");
            }
            if ($existingQuery->dataClass() != $this->modelClass) {
                throw new InvalidArgumentException("existingQuery's dataClass is {$existingQuery->dataClass()}"
                    . ", {$this->modelClass} expected.");
            }
            $query = $existingQuery;
        } else {
            $query = DataList::create($this->modelClass);
        }
        if (is_array($limit)) {
            $query = $query->limit(
                $limit['limit'] ?? null,
                $limit['start'] ?? null
            );
        } else {
            $query = $query->limit($limit);
        }
        return $query->sort($sort);
    }

    /**
     * Takes a search phrase or search term and searches for it across all searchable fields.
     *
     * @param  array|string  $searchPhrase     Search phrase
     * @param  DataQuery     $subGroup         Sub group
     * @param  array         $searchableFields Searchable fields
     * 
     * @return void
     */
    private function generalSearchAcrossFields(array|string $searchPhrase, DataQuery $subGroup, array $searchableFields) : void
    {
        $formFields = $this->getSearchFields();
        foreach ($searchableFields as $field => $spec) {
            $formFieldName = str_replace('.', '__', $field);
            $filter        = $this->getGeneralSearchFilter($this->modelClass, $field);
            // Only apply filter if the field is allowed to be general and is backed by a form field.
            // Otherwise we could be dealing with, for example, a DataObject which implements scaffoldSearchField
            // to provide some unexpected field name, where the below would result in a DatabaseException.
            if ((!isset($spec['general'])
              || $spec['general'])
             && ($formFields->fieldByName($formFieldName)
              || $formFields->dataFieldByName($formFieldName))
             && $filter !== null
            ) {
                $filter->setModel($this->modelClass);
                $filter->setValue($searchPhrase);
                if (is_array($spec)) {
                    $this->applyFilter($filter, $subGroup, $spec);
                }
            }
        }
    }

    /**
     * Use the global general search for searching across multiple fields.
     *
     * @param string|array  $searchPhrase
     * 
     * @return DataList
     *
     * @throws \Exception
     */
    private function generalFieldSearch(DataList $query, array $searchableFields, $searchPhrase) : DataList
    {
        return $query->alterDataQuery(function (DataQuery $dataQuery) use ($searchableFields, $searchPhrase) {
            // If necessary, split search phrase into terms, then search across fields.
            if (Config::inst()->get($this->modelClass, 'general_search_split_terms')) {
                if (is_array($searchPhrase)) {
                    // Allow matches from ANY query in the array (i.e. return $obj where query1 matches OR query2 matches)
                    $dataQuery = $dataQuery->disjunctiveGroup();
                    foreach ($searchPhrase as $phrase) {
                        // where ((field1 LIKE %lorem% OR field2 LIKE %lorem%) AND (field1 LIKE %ipsum% OR field2 LIKE %ipsum%))
                        $generalSubGroup = $dataQuery->conjunctiveGroup();
                        foreach (explode(' ', $phrase) as $searchTerm) {
                            $this->generalSearchAcrossFields($searchTerm, $generalSubGroup->disjunctiveGroup(), $searchableFields);
                        }
                    }
                } else {
                    // where ((field1 LIKE %lorem% OR field2 LIKE %lorem%) AND (field1 LIKE %ipsum% OR field2 LIKE %ipsum%))
                    $generalSubGroup = $dataQuery->conjunctiveGroup();
                    foreach (explode(' ', $searchPhrase) as $searchTerm) {
                        $this->generalSearchAcrossFields($searchTerm, $generalSubGroup->disjunctiveGroup(), $searchableFields);
                    }
                }
            } else {
                // where (field1 LIKE %lorem ipsum% OR field2 LIKE %lorem ipsum%)
                $this->generalSearchAcrossFields($searchPhrase, $dataQuery->disjunctiveGroup(), $searchableFields);
            }
        });
    }

    /**
     * Get the search filter for the given fieldname when searched from the general search field.
     * 
     * @return SearchFilter|null
     */
    private function getGeneralSearchFilter(string $modelClass, string $fieldName) : SearchFilter|null
    {
        if ($filterClass = Config::inst()->get($modelClass, 'general_search_field_filter')) {
            return Injector::inst()->create($filterClass, $fieldName);
        }
        return $this->getFilter($fieldName);
    }

    /**
     * 
     * Search against a single field
     *
     * @param DataList     $query            Query
     * @param array        $searchableFields Searchable fields
     * @param string       $searchField      Search field
     * @param string|array $searchPhrase     Search phrase
     * 
     * @return DataList
     *
     * @throws \Exception
     */
    private function individualFieldSearch(DataList $query, array $searchableFields, string $searchField, $searchPhrase) : DataList
    {
        $filter = $this->getFilter($searchField);
        if (!$filter) {
            return $query;
        }
        $filter->setModel($this->modelClass);
        $filter->setValue($searchPhrase);
        $searchableFieldSpec = $searchableFields[$searchField] ?? [];
        return $query->alterDataQuery(function ($dataQuery) use ($filter, $searchableFieldSpec) {
            if (is_array($searchableFieldSpec)
             && is_array($dataQuery)
            ){
                $this->applyFilter($filter, $dataQuery, $searchableFieldSpec);
            }
        });
    }

    /**
     * Apply a SearchFilter to a DataQuery for a given field's specifications
     * 
     * @param SearchFilter $filter              Search filter
     * @param DataQuery    $dataQuery           Data query
     * @param array        $searchableFieldSpec Searchable field spec 
     * 
     * @return void
     */
    private function applyFilter(SearchFilter $filter, DataQuery $dataQuery, array $searchableFieldSpec) : void
    {
        if ($filter->isEmpty()) {
            return;
        }
        if (isset($searchableFieldSpec['match_any'])) {
            $searchFields = $searchableFieldSpec['match_any'];
            $filterClass  = get_class($filter);
            $value        = $filter->getValue();
            $modifiers    = $filter->getModifiers();
            $subGroup     = $dataQuery->disjunctiveGroup();
            foreach ($searchFields as $matchField) {
                /** @var SearchFilter $filter */
                $filter = Injector::inst()->create($filterClass, $matchField, $value, $modifiers);
                $filter->apply($subGroup);
            }
        } else {
            $filter->apply($dataQuery);
        }
    }

    /**
     * Returns a result set from the given search parameters.
     *
     * @param array             $searchParams Search params
     * @param array|bool|string $sort         Sort
     * @param array|bool|string $limit        Limit
     * 
     * @return DataList
     * 
     * @todo rearrange start and limit params to reflect DataObject
     * @throws Exception
     */
    public function getResults($searchParams, $sort = false, $limit = false): DataList
    {
        $searchParams = array_filter((array)$searchParams, [$this, 'clearEmptySearchFields']);
        // getQuery actually returns a DataList
        return $this->getQuery($searchParams, $sort, $limit);
    }

    /**
     * Callback map function to filter fields with empty values from
     * being included in the search expression.
     *
     * @param mixed $value Value
     * 
     * @return bool
     */
    public function clearEmptySearchFields($value): bool
    {
        return ($value != '');
    }

    /**
     * Accessor for the filter attached to a named field.
     *
     * @param string $name Name
     * 
     * @return SearchFilter
     */
    public function getFilter($name): SearchFilter|null
    {
        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        } else {
            return null;
        }
    }

    /**
     * Get the map of filters in the current search context.
     *
     * @return SearchFilter[]
     */
    public function getFilters() : array
    {
        return $this->filters;
    }

    /**
     * Overwrite the current search context filter map.
     *
     * @param array $filters Filters
     * 
     * @return void
     */
    public function setFilters($filters) : void
    {
        $this->filters = $filters;
    }

    /**
     * Adds a instance of {@link SearchFilter}.
     *
     * @param SearchFilter $filter Filter
     * 
     * @return void
     */
    public function addFilter($filter) : void
    {
        $this->filters[$filter->getFullName()] = $filter;
    }

    /**
     * Removes a filter by name.
     *
     * @param string $name Name
     * 
     * @return void
     */
    public function removeFilterByName($name) : void
    {
        unset($this->filters[$name]);
    }

    /**
     * Get the list of searchable fields in the current search context.
     *
     * @return FieldList
     */
    public function getFields() : FieldList
    {
        return $this->fields;
    }

    /**
     * Apply a list of searchable fields to the current search context.
     *
     * @param FieldList $fields Fields
     * 
     * @return void
     */
    public function setFields($fields) : void
    {
        $this->fields = $fields;
    }

    /**
     * Adds a new {@link FormField} instance.
     *
     * @param FormField $field Field
     * 
     * @return void
     */
    public function addField($field) : void
    {
        $this->fields->push($field);
    }

    /**
     * Removes an existing formfield instance by its name.
     *
     * @param string $fieldName Field name
     * 
     * @return void
     */
    public function removeFieldByName($fieldName) : void
    {
        $this->fields->removeByName($fieldName);
    }

    /**
     * Set search param values
     *
     * @param array|HTTPRequest $searchParams Search params
     * 
     * @return $this
     */
    public function setSearchParams($searchParams) : static
    {
        // hack to work with $searchParams when it's an Object
        if ($searchParams instanceof HTTPRequest) {
            $this->searchParams = $searchParams->getVars();
        } else {
            $this->searchParams = $searchParams;
        }
        return $this;
    }

    /**
     * Returns the search params.
     * 
     * @return array
     */
    public function getSearchParams() : array
    {
        return $this->searchParams;
    }

    /**
     * Gets a list of what fields were searched and the values provided
     * for each field. Returns an ArrayList of ArrayData, suitable for
     * rendering on a template.
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
            } else {
                // For checkboxes, it suffices to simply include the field in the list, since it's binary
                if ($field instanceof CheckboxField) {
                    $searchValue = null;
                }
            }
            $list->push(ArrayData::create([
                'Field' => $field->Title(),
                'Value' => $searchValue,
            ]));
        }
        return $list;
    }
}