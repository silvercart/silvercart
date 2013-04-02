<?php

/**
 * Copyright 2013 pixeltricks GmbH
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
 * @package Silvercart
 * @subpackage Forms_GridField
 */

/**
 * This class is is responsible for adding objects to another object's has_many 
 * and many_many relation, as defined by the {@link RelationList} passed to the 
 * GridField constructor.
 * Objects can be searched through an input field (partially matching one or 
 * more fields).
 * Selecting from the results will add the object to the relation.
 * Often used alongside {@link GridFieldRemoveButton} for detaching existing 
 * records from a relatinship.
 * For easier setup, have a look at a sample configuration in 
 * {@link GridFieldConfig_RelationEditor}.
 *
 * @package Silvercart
 * @subpackage Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 12.02.2013
 * @license see license file in modules root directory
 */
class SilvercartGridFieldAddExistingAutocompleter extends GridFieldAddExistingAutocompleter {

    /**
     * Returns a json array of a search results that can be used by for example Jquery.ui.autosuggestion
     *
     * @param GridField      $gridField GridField to execute search for
     * @param SS_HTTPRequest $request   Request to extract search data from
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.02.2013
     */
    public function doSearch($gridField, $request) {
        $dataClass = $gridField->getList()->dataClass();
        $allList = $this->searchList ? $this->searchList : DataList::create($dataClass);

        $searchFields = ($this->getSearchFields()) ? $this->getSearchFields() : $this->scaffoldSearchFields($dataClass);
        if (!$searchFields) {
            throw new LogicException(
                    sprintf('GridFieldAddExistingAutocompleter: No searchable fields could be found for class "%s"', $dataClass));
        }

        $stmts          = array();
        $joinClassNames = array();
        foreach ($searchFields as $index => $searchField) {
            if (strpos($searchField, '.') !== false) {
                $originalSearchableField    = $searchField;
                $parts                      = explode('.', $searchField);
                $relationName               = $parts[0];
                $searchField                = $parts[1];
                $joinClassName              = null;
                $relationClassName          = Object::get_static($dataClass, 'has_many');
                if (is_array($relationClassName)) {
                    $relationClassName = $relationClassName[$relationName];
                }
                if (!is_null($relationClassName)) {
                    foreach (singleton($relationClassName)->getClassAncestry() as $ancestor) {
                        if (DataObject::has_own_table($ancestor)) {
                            $joinClassName = $ancestor;
                            break;
                        }
                    }
                }
                if (is_null($joinClassName)) {
                    throw new LogicException(
                            sprintf('GridFieldAddExistingAutocompleter: Searchable field "%s" could not be found for class "%s"', $originalSearchableField, $dataClass)
                    );
                } else {
                    $joinClassNames[$relationName]  = $joinClassName;
                    $searchFields[$index]           = $relationClassName . '.' . $searchField;
                    $searchField                    = $relationClassName . '"."' . $searchField;
                }
                $has_one = Object::get_static($relationClassName, 'has_one');
                foreach ($has_one as $hasOneRelationName => $hasOneRelationClassName) {
                    if ($hasOneRelationClassName == $dataClass) {
                        $targetRelationName = $hasOneRelationName;
                        continue;
                    }
                }
            } else {
                $searchField = $dataClass . '"."' . $searchField;
            }
            $stmts[] = sprintf('"%s" LIKE \'%s%%\'', $searchField, Convert::raw2sql($request->getVar('gridfield_relationsearch')));
        }
        foreach ($joinClassNames as $relationName => $joinClassName) {
            $allList->leftJoin(
                    $joinClassName, sprintf(
                            '"%s"."ID" = "%s"."%sID"', $dataClass, $joinClassName, $targetRelationName
                    )
            );
        }
        $results = $allList
                ->where(implode(' OR ', $stmts))
                ->subtract($gridField->getList())
                ->sort($searchFields[0], 'ASC')
                ->limit($this->getResultsLimit());

        $json = array();
        foreach ($results as $result) {
            $json[$result->ID] = SSViewer::fromString($this->resultsFormat)->process($result);
        }
        return Convert::array2json($json);
    }

    /**
     * Detect searchable fields and searchable relations
     * Only has_many relations may be searched.
     * Falls back to Title or Name if no earchableFields are defined.
     *
     * @param string $dataClass The class name to get fields for
     * 
     * @return array
     * 
     * @return array|null
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.02.2013
     */
    public function scaffoldSearchFields($dataClass) {
        $obj                = singleton($dataClass);
        $searchableFields   = null;
        if ($obj->searchableFields()) {
            foreach ($obj->searchableFields() as $name => $specOrName) {
                //searchableFields() may return a multidimensional array
                $searchableFieldKey = (is_int($name)) ? $specOrName : $name;
                if (strpos($searchableFieldKey, ".") !== false) {
                    $parts = explode('.', $searchableFieldKey);
                    $relationName = $parts[0];
                    $has_many = Object::get_static($dataClass, 'has_many');
                    if (is_array($has_many) && array_key_exists($relationName, $has_many)) {
                        $searchableFields[] = $searchableFieldKey;
                    }
                } else {
                    $searchableFields[] = $searchableFieldKey;
                }
            }
        }
        if (is_null($searchableFields)) {
            if ($obj->hasDatabaseField('Title')) {
                $searchableFields = array('Title');
            } elseif ($obj->hasDatabaseField('Name')) {
                $searchableFields = array('Name');
            }
        }
        return $searchableFields;
    }

}