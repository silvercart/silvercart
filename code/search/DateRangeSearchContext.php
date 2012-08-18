<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package SilverCart
 * @subpackage Search
 */

/**
 * Provides the ability to search between two dates.
 *
 * @package SilverCart
 * @subpackage Search
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 11.03.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class DateRangeSearchContext extends SearchContext {

  /**
   * Replace the default form fields for the 'Created' search
   * field with a single text field which we can use to apply
   * jquery date range widget to.
   *
   * @return FieldList
   *
   * @author Sascha Koehler <skoehler@pixeltricks.de>
   * @since 11.03.2012
   */
    public function getSearchFields() {
        $fields = ($this->fields) ? $this->fields : singleton($this->modelClass)->scaffoldSearchFields();

        if ($fields) {
            $dates = array ();

            foreach ($fields as $f) {
                $type = singleton($this->modelClass)->obj($f->Name())->class;
                if ($type == "Date" || $type == "SS_Datetime") {
                    $dates[] = $f;
                }
            }

            foreach ($dates as $d) {
                $fields->removeByName($d->Name());
                $fields->push(new TextField($d->Name(), $d->Title()));
            }
        }
        return $fields;
    }

    /**
     * Alter the existing SQL query object by adding some filters for the search
     * so that the query finds objects between two dates min and max
     *
     * @param array  $searchParams  The search parameters
     * @param string $sort          The SQL sort statement
     * @param string $limit         The SQL limit statement
     * @param string $existingQuery The existing query
     *
     * @return SQLQuery Query with filters applied for search
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.03.2012
     */
    public function getQuery($searchParams, $sort = false, $limit = false, $existingQuery = null) {
        $query            = parent::getQuery($searchParams, $sort, $limit, $existingQuery);
        $searchParamArray = (is_object($searchParams)) ? $searchParams->getVars() : $searchParams;

        foreach ($searchParamArray as $fieldName => $value) {
            if ($fieldName == 'Created') {
                $filter = $this->getFilter($fieldName);

                if ($filter && get_class($filter) == "DateRangeSearchFilter") {
                    $min_val = null;
                    $max_val = null;

                    $filter->setModel($this->modelClass);

                    if (strpos($value, '-') === false) {
                        $min_val = $value;
                    } else {
                        preg_match('/([^\s]*)(\s-\s(.*))?/i', $value, $matches);

                        $min_val = (isset($matches[1])) ? $matches[1] : null;

                        if (isset($matches[3])) {
                            $max_val = $matches[3];
                        }
                    }

                    if ($min_val && $max_val) {
                        $filter->setMin($min_val);
                        $filter->setMax($max_val);
                        $filter->apply($query);
                    } else if ($min_val) {
                        $filter->setMin($min_val);
                        $filter->apply($query);
                    } else if ($max_val) {
                        $filter->setMax($max_val);
                        $filter->apply($query);
                    }
                }
            }
        }
        return $query;
    }
}

