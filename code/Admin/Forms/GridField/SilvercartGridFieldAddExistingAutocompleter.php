<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @since 08.04.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartGridFieldAddExistingAutocompleter extends GridFieldAddExistingAutocompleter {

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
     * @since 15.04.2014
     */
    public function scaffoldSearchFields($dataClass) {
        $fields            = parent::scaffoldSearchFields($dataClass);
        $has_many          = Config::inst()->get($dataClass, 'has_many');
        $many_many         = Config::inst()->get($dataClass, 'many_many');
        $belongs_many_many = Config::inst()->get($dataClass, 'belongs_many_many');

        foreach ($fields as $key => $value) {
            if (strpos($value, '.') !== false) {
                $parts        = explode('.', $value, 2);
                $relationName = $parts[0];
                $searchField  = $parts[1];
                if (is_array($has_many) && array_key_exists($relationName, $has_many)) {
                    $fields[$key] = $has_many[$relationName] . '.' . $searchField;
                } elseif (is_array($many_many) && array_key_exists($relationName, $many_many)) {
                    unset($fields[$key]);
                } elseif (is_array($belongs_many_many) && array_key_exists($relationName, $belongs_many_many)) {
                    unset($fields[$key]);
                }
            }
        }
        
        return $fields;
    }

    /**
     * Returns the placeholder text to display in search field.
     * 
     * @param String $dataClass The class of the object being searched for
     * 
     * @return String
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.04.2014
     */
    public function getPlaceholderText($dataClass) {
        $searchFields = ($this->getSearchFields()) ? $this->getSearchFields() : $this->scaffoldSearchFields($dataClass);

        if ($this->placeholderText) {
            return $this->placeholderText;
        } else {
            $labels = array();
            if ($searchFields) {
                foreach ($searchFields as $searchField) {
                    $parts = explode(':', $searchField);
                    $label = singleton($dataClass)->fieldLabel($parts[0]);
                    if ($label) {
                        $labels[] = $label;
                    }
                }
            }
            if ($labels) {
                return _t(
                        'GridField.PlaceHolderWithLabels', 'Find {type} by {name}', array('type' => singleton($dataClass)->plural_name(), 'name' => implode(', ', $labels))
                );
            } else {
                return _t(
                        'GridField.PlaceHolder', 'Find {type}', array('type' => singleton($dataClass)->plural_name())
                );
            }
        }
    }

}