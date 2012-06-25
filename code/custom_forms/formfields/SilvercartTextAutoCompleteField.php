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
    
    protected $autoCompleteList = array();
    protected $autoCompleteSource = '';
    protected $autoCompleteSourceDataObject = '';
    protected $autoCompleteSourceAttribute = '';
    protected $autoCompleteValue = '';
    
    protected $canHaveMany = false;
    
    protected $controller = null;
    
    protected $className = 'SilvercartTextAutoCompleteField';

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
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    public function Field() {
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
                $items = explode(',', $list);
                foreach ($items as $item) {
                    $item = strtolower(trim($item));
                    $existingItem = DataObject::get_one($autoCompleteSourceDataObject, sprintf("`%s` = '%s'", $autoCompleteSourceAttribute, $item));
                    if (!$existingItem) {
                        $existingItem = new $autoCompleteSourceDataObject();
                        $existingItem->{$autoCompleteSourceAttribute} = $item;
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
                $autoCompleteList[] = str_replace("'", "\'", $dataObject->{$attribute});
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
                $value = $relation->{$this->getAutoCompleteSourceAttribute()};
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
     * Can be a DataObjectSet or a comma seperated string.
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

}