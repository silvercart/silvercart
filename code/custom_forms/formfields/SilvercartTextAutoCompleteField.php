<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of the SilverStripe modul FormFieldTools.
 *
 * FormFieldTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FormFieldTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with FormFieldTools. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @package Silvercart
 * @subpackage FormFields
 */

/**
 * TextAutoCompleteField is an autocomplete form field for a DataObjects
 * has_one relation
 *
 * @package Silvercart
 * @subpackage FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartTextAutoCompleteField extends TextField {
    
    /**
     * List of values to use for the auto completion
     *
     * @var array
     */
    protected $autoCompleteList = array();
    
    /**
     * Source field for the auto completion (dot notated)
     *
     * @var string
     */
    protected $autoCompleteSource = '';
    
    /**
     * Source object name for the auto completion
     *
     * @var string
     */
    protected $autoCompleteSourceDataObject = '';
    
    /**
     * Source field name for the auto completion
     *
     * @var string
     */
    protected $autoCompleteSourceAttribute = '';
    
    /**
     * Value of the auto complete field
     *
     * @var string
     */
    protected $autoCompleteValue = '';
    
    /**
     * Indicator to check whether the field can have many related values
     *
     * @var string
     */
    protected $canHaveMany = false;
    
    /**
     * Controller for this field
     *
     * @var DataObject
     */
    protected $controller = null;
    
    /**
     * Class name
     *
     * @var string
     */
    protected $className = 'SilvercartTextAutoCompleteField';
    
    /**
     * Delimiter to seperate field values of one entry
     *
     * @var string 
     */
    protected $fieldDelimiter = '--';
    
    /**
     * Delimiter to seperate entries
     *
     * @var string 
     */
    protected $entryDelimiter = ';';

    /**
     * Returns an input field, class="text" and type="text" with an optional maxlength
     * 
     * @param DataObject $controller         Controller of the field
     * @param string     $name               name of the field
     * @param string     $title              title of the field
     * @param array      $autoCompleteSource list of autocomplete values
     * @param string     $value              value of the field
     * @param int        $maxLength          maximum length
     * @param Form       $form               related form
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.10.2011
     */
    public function __construct($controller, $name, $title = null, $autoCompleteSource = '', $value = "", $maxLength = null, $form = null) {
        $this->setController($controller);
        $this->setAutoCompleteSource($autoCompleteSource);
        $this->setAutoCompleteValue($value);
        parent::__construct($name, $title, $value, $maxLength, $form);
    }
    
    /**
     * Returns the field tag.
     * 
     * @param array $properties not in use, just declared to be compatible with parent
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function Field($properties = array()) {
        $value = $this->getAutoCompleteValue();
        if (empty ($value)) {
            $this->generateAutoCompleteValue();
            $value = $this->getAutoCompleteValue();
        }
        $this->setValue($value);
        return parent::Field();
    }

    /**
     * Returns a "Field Holder" for this field - used by templates.
     * Forms are constructed from by concatenating a number of these field holders.  The default
     * field holder is a label and form field inside a paragraph tag.
     * 
     * Composite fields can override FieldHolder to create whatever visual effects you like.  It's
     * a good idea to put the actual HTML for field holders into templates.  The default field holder
     * is the DefaultFieldHolder template.  This lets you override the HTML for specific sites, if it's
     * necessary.
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function FieldHolder() {
        return parent::FieldHolder() . $this->FieldHolderScript();
    }
    
    /**
     * Returns the small fieldholder for field groups
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.06.2012
     */
    public function SmallFieldHolder() {
        return '<div class="silvercarttextautocomplete">' . parent::SmallFieldHolder() . '</div>' . $this->FieldHolderScript();
    }
    
    /**
     * Executes the common field holder routine and returns the custom
     * javascript code
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.06.2012
     */
    public function FieldHolderScript() {
        $baseUrl = SilvercartTools::getBaseURLSegment();
        Requirements::css($baseUrl . FRAMEWORK_DIR . '/thirdparty/jquery-ui-themes/smoothness/jquery-ui-1.8rc3.custom.css');
        Requirements::javascript($baseUrl . 'silvercart/script/jquery-ui/jquery.ui.autocomplete.js');
        Requirements::javascript($baseUrl . 'silvercart/script/SilvercartTextAutoCompleteField.js');
        $autoCompleteSource = $this->getAutoCompleteSource();
        if (empty ($autoCompleteSource)) {
            $this->generateAutoCompleteSource();
        }
        $this->generateAutoCompleteList();
        $autoCompleteList = array();
        foreach ($this->getAutoCompleteList() as $autoCompleteEntry) {
            $autoCompleteList[] = "'" . $autoCompleteEntry . "'";
        }
        $customScript = '<script type="text/javascript">';
        $customScript .= $this->className . '.AutoCompleteList["' . $this->Name() . '"] = [';
        $customScript .= implode(',', $autoCompleteList);
        $customScript .= '];';
        $customScript .= $this->className . '.EntryDelimiter["' . $this->Name() . '"] = "' . $this->getEntryDelimiter() . '";';
        $customScript .= '</script>';
        return $customScript;
    }
    
    /**
     * Saves the submited data into the managed DataObject.
     *
     * @param DataObject $record DataObject to save into
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function saveInto(DataObject $record) {
        $fieldName = $this->name;

        if (!$record->hasDatabaseField($fieldName)) {
            user_error(
                sprintf(
                    "SilvercartTextAutoCompleteField::saveInto() Field '%s' not found on %s.%s",
                    $fieldName,
                    $record->class,
                    $record->ID
                ),
                E_USER_ERROR
            );
        }

        $items = array();
        $list = $this->value;
        $autoCompleteSourceDataObject = $this->getAutoCompleteSourceDataObject();
        $autoCompleteSourceAttribute = $this->getAutoCompleteSourceAttribute();
        $relatedID = 0;
        if ($list) {
            if ($list != 'undefined') {
                $items = explode($this->getEntryDelimiter(), $list);
                foreach ($items as $item) {
                    $item = trim($item);
                    if (is_array($autoCompleteSourceAttribute)) {
                        $filters        = array();
                        $splittedItems  = explode($this->getFieldDelimiter(), $item);
                        foreach ($autoCompleteSourceAttribute as $key => $fieldName) {
                            $filters[] = sprintf(
                                    "\"%s\" = '%s'",
                                    $fieldName,
                                    $splittedItems[$key]
                            );
                        }
                        $filter = implode(' AND ', $filters);
                    } else {
                        $filter = sprintf(
                                "\"%s\" = '%s'",
                                $autoCompleteSourceAttribute,
                                $item
                        );
                    }
                    $existingItem = DataObject::get_one($autoCompleteSourceDataObject, $filter);
                    if (!$existingItem) {
                        $existingItem = new $autoCompleteSourceDataObject();
                        if (is_array($autoCompleteSourceAttribute)) {
                            $splittedItems  = explode($this->getFieldDelimiter(), $item);
                            foreach ($autoCompleteSourceAttribute as $key => $fieldName) {
                                $existingItem->{$fieldName} = $splittedItems[$key];
                            }
                        } else {
                            $existingItem->{$autoCompleteSourceAttribute} = $item;
                        }
                        $existingItem->write();
                    }
                    $relatedID = $existingItem->ID;
                    break;
                }
            }
        }

        $record->{$fieldName} = $relatedID;
        $record->write();
    }

    ////////////////////////////////////////////////////////////////////////////
    // GENERATING METHODS
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Generates the autocomplete list by the given DataObject source
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function generateAutoCompleteList() {
        $dataObjectSet = DataObject::get($this->getAutoCompleteSourceDataObject());
        $autoCompleteList = array();
        $attribute = $this->getAutoCompleteSourceAttribute();
        if ($dataObjectSet) {
            foreach ($dataObjectSet as $dataObject) {
                if (is_array($attribute)) {
                    $listEntries = array();
                    foreach ($attribute as $key => $fieldName) {
                        $listEntries[] = $this->prepareValue($dataObject->{$fieldName});
                    }
                    $listEntry = implode($this->getFieldDelimiter(), $listEntries);
                } else {
                    $listEntry = $this->prepareValue($dataObject->{$attribute});
                }
                $autoCompleteList[] = $listEntry;
            }
        }
        $this->setAutoCompleteList($autoCompleteList);
    }
    
    /**
     * Generates the autocomplete source by the given controllers relations and
     * fieldname
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    protected function generateAutoCompleteSource() {
        $sourceDataObject = $this->getAutoCompleteSourceDataObject();
        if (empty($sourceDataObject) ||
            is_null($sourceDataObject)) {
            $fieldname = $this->name;
            if (strpos($fieldname, 'ID') == strlen($fieldname) - 2) {
                $autoCompleteSource = substr($fieldname, 0, strlen($fieldname) - 2);
                $this->setAutoCompleteSource($autoCompleteSource);
            }
        }
    }
    
    /**
     * Generates the autocomplete value by the given controllers relations and
     * fieldname
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    protected function generateAutoCompleteValue() {
        $fieldName  = $this->name;
        $controller = $this->getController();
        $value      = '';
        if ($controller->ID) {
            $relation   = DataObject::get_by_id($this->getAutoCompleteSourceDataObject(), $controller->{$fieldName});
            if ($relation) {
                $attribute = $this->getAutoCompleteSourceAttribute();
                if (is_array($attribute)) {
                    $values = array();
                    foreach ($attribute as $key => $fieldName) {
                        $values[] = $this->prepareValue($relation->{$fieldName});
                    }
                    $value = implode($this->getFieldDelimiter(), $values);
                } else {
                    $value = $this->prepareValue($relation->{$attribute});
                }
            }
        }
        $this->setAutoCompleteValue($value);
    }


    ////////////////////////////////////////////////////////////////////////////
    // GETTER AND SETTER SECTION
    ////////////////////////////////////////////////////////////////////////////
    
    /**
     * Sets $this->canHaveMany to true.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function canHaveMany() {
        $this->canHaveMany = true;
    }
    
    /**
     * Sets $this->canHaveMany to false.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function cantHaveMany() {
        $this->canHaveMany = false;
    }
    
    /**
     * Returns the autocomplete list
     *
     * @return array
     */
    public function getAutoCompleteList() {
        return $this->autoCompleteList;
    }
    
    /**
     * Returns the autocomplete source
     *
     * @return string
     */
    public function getAutoCompleteSource() {
        return $this->autoCompleteSource;
    }
    
    /**
     * Returns the autocomplete source DataObject
     *
     * @return string
     */
    public function getAutoCompleteSourceDataObject() {
        return $this->autoCompleteSourceDataObject;
    }
    
    /**
     * Returns the autocomplete source DataObjects attribute
     *
     * @return string
     */
    public function getAutoCompleteSourceAttribute() {
        return $this->autoCompleteSourceAttribute;
    }
    
    /**
     * Returns the value of the autocomplete field
     *
     * @return mixed
     */
    public function getAutoCompleteValue() {
        return $this->autoCompleteValue;
    }
    
    /**
     * Returns the controller
     *
     * @return DataObject
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Sets the autocomplete list
     *
     * @param array $autoCompleteList autocomplete list
     * 
     * @return void
     */
    public function setAutoCompleteList($autoCompleteList) {
        $this->autoCompleteList = $autoCompleteList;
    }

    /**
     * Sets the autocomplete source
     *
     * @param string $autoCompleteSource autocomplete source
     * 
     * @return void
     */
    public function setAutoCompleteSource($autoCompleteSource) {
        if (strpos($autoCompleteSource, '.') !== false) {
            list(
                $dataObject,
                $attribute
            ) = explode('.', $autoCompleteSource);
        } else {
            $dataObject = $autoCompleteSource;
            $attribute = 'Title';
        }
        $this->autoCompleteSource = $autoCompleteSource;
        $this->setAutoCompleteSourceAttribute($attribute);
        $this->setAutoCompleteSourceDataObject($dataObject);
    }

    /**
     * Sets the autocomplete source DataObject
     *
     * @param string $autoCompleteSourceDataObject autocomplete source DataObject
     * 
     * @return void
     */
    public function setAutoCompleteSourceDataObject($autoCompleteSourceDataObject) {
        $this->autoCompleteSourceDataObject = $autoCompleteSourceDataObject;
    }

    /**
     * Sets the autocomplete source DataObjects attribute
     *
     * @param string $autoCompleteSourceAttribute autocomplete source DataObjects attribute
     * 
     * @return void
     */
    public function setAutoCompleteSourceAttribute($autoCompleteSourceAttribute) {
        $this->autoCompleteSourceAttribute = $autoCompleteSourceAttribute;
    }

    /**
     * Sets the value for the autocomplete field.
     * Can be a ArrayList or a comma seperated string.
     *
     * @param mixed $autoCompleteValue value for the autocomplete field
     * 
     * @return void
     */
    public function setAutoCompleteValue($autoCompleteValue) {
        $this->autoCompleteValue = $autoCompleteValue;
    }

    /**
     * sets the controller
     *
     * @param DataObject $controller Controller
     * 
     * @return void
     */
    public function setController($controller) {
        $this->controller = $controller;
    }

    /**
     * Sets the field delimiter
     *
     * @param string $fieldDelimiter Delimiter to seperate field values of one entry
     * 
     * @return void
     */
    public function setFieldDelimiter($fieldDelimiter) {
        $this->fieldDelimiter = $fieldDelimiter;
    }
    
    /**
     * Returns the field delimiter
     * 
     * @return string
     */
    public function getFieldDelimiter() {
        return  ' ' . $this->fieldDelimiter . ' ';
    }

    /**
     * Sets the entry delimiter
     *
     * @param string $entryDelimiter Delimiter to seperate entries
     * 
     * @return void
     */
    public function setEntryDelimiter($entryDelimiter) {
        $this->entryDelimiter = $entryDelimiter;
    }
    
    /**
     * Returns the field delimiter
     * 
     * @return string
     */
    public function getEntryDelimiter() {
        return $this->entryDelimiter . ' ';
    }
    
    /**
     * Prepares a value to display in text field
     *
     * @param string $value Value to prepare
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.07.2012
     */
    public function prepareValue($value) {
        $preparedValue = str_replace("'",                           '\\\'',                             $value);
        $preparedValue = str_replace($this->getEntryDelimiter(),    "\\" . $this->getEntryDelimiter(),  $preparedValue);
        $preparedValue = str_replace($this->getFieldDelimiter(),    "\\" . $this->getFieldDelimiter(),  $preparedValue);
        return $preparedValue;
    }

}