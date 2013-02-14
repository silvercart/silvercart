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
 * @subpackage Forms
 */

/**
 * Extension for every DataObject
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 13.02.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartFormScaffolder extends FormScaffolder {

    /**
     * Gets the form fields as defined through the metadata
     * on {@link $obj} and the custom parameters passed to FormScaffolder.
     * Depending on those parameters, the fields can be used in ajax-context,
     * contain {@link TabSet}s etc.
     * 
     * Uses SilvercartGridFieldConfig_RelationEditor and 
     * SilvercartGridFieldConfig_LanguageRelationEditor instead of
     * GridFieldConfig_RelationEditor.
     * 
     * @return FieldList
     */
    public function getFieldList() {
        $fields = new FieldList();

        // tabbed or untabbed
        if ($this->tabbed) {
            $fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
            $mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));
        }

        // add database fields
        foreach ($this->obj->db() as $fieldName => $fieldType) {
            if ($this->restrictFields && !in_array($fieldName, $this->restrictFields)) {
                continue;
            }

            // @todo Pass localized title
            if ($this->fieldClasses && isset($this->fieldClasses[$fieldName])) {
                $fieldClass = $this->fieldClasses[$fieldName];
                $fieldObject = new $fieldClass($fieldName);
            } else {
                $fieldObject = $this->obj->dbObject($fieldName)->scaffoldFormField(null, $this->getParamsArray());
            }
            $fieldObject->setTitle($this->obj->fieldLabel($fieldName));
            if ($this->tabbed) {
                $fields->addFieldToTab("Root.Main", $fieldObject);
            } else {
                $fields->push($fieldObject);
            }
        }

        // add has_one relation fields
        if ($this->obj->has_one()) {
            foreach ($this->obj->has_one() as $relationship => $component) {
                if ($this->restrictFields && !in_array($relationship, $this->restrictFields)) {
                    continue;
                }
                $fieldName = "{$relationship}ID";
                if ($this->fieldClasses && isset($this->fieldClasses[$fieldName])) {
                    $fieldClass = $this->fieldClasses[$fieldName];
                    $hasOneField = new $fieldClass($fieldName);
                } else {
                    $hasOneField = $this->obj->dbObject($fieldName)->scaffoldFormField(null, $this->getParamsArray());
                }
                $hasOneField->setTitle($this->obj->fieldLabel($relationship));
                if ($this->tabbed) {
                    $fields->addFieldToTab("Root.Main", $hasOneField);
                } else {
                    $fields->push($hasOneField);
                }
            }
        }

        // only add relational fields if an ID is present
        if ($this->obj->ID) {
            $excludeFromScaffolding = array();
            if (method_exists($this->obj, 'excludeFromScaffolding')) {
                $excludeFromScaffolding = $this->obj->excludeFromScaffolding();
            }
            // add has_many relation fields
            if ($this->obj->has_many() && ($this->includeRelations === true || isset($this->includeRelations['has_many']))) {
                foreach ($this->obj->has_many() as $relationship => $component) {
                    if (in_array($relationship, $excludeFromScaffolding)) {
                        continue;
                    }
                    if ($this->tabbed) {
                        $relationTab = $fields->findOrMakeTab(
                                "Root.$relationship", $this->obj->fieldLabel($relationship)
                        );
                    }
                    $fieldClass = (isset($this->fieldClasses[$relationship])) ? $this->fieldClasses[$relationship] : 'GridField';
                    if ($this->obj->has_extension($this->obj->$relationship()->dataClass(), 'SilvercartLanguageDecorator')) {
                        $config = SilvercartGridFieldConfig_LanguageRelationEditor::create();
                    } else {
                        $config = SilvercartGridFieldConfig_RelationEditor::create();
                    }
                    $grid = Object::create(
                            $fieldClass,
                            $relationship,
                            $this->obj->fieldLabel($relationship),
                            $this->obj->$relationship(),
                            $config
                    );
                    if ($this->tabbed) {
                        $fields->addFieldToTab("Root.$relationship", $grid);
                    } else {
                        $fields->push($grid);
                    }
                }
            }

            if ($this->obj->many_many() && ($this->includeRelations === true || isset($this->includeRelations['many_many']))) {
                foreach ($this->obj->many_many() as $relationship => $component) {
                    if (in_array($relationship, $excludeFromScaffolding)) {
                        continue;
                    }
                    if ($this->tabbed) {
                        $relationTab = $fields->findOrMakeTab(
                                "Root.$relationship", $this->obj->fieldLabel($relationship)
                        );
                    }

                    $fieldClass = (isset($this->fieldClasses[$relationship])) ? $this->fieldClasses[$relationship] : 'GridField';
                    $grid = Object::create($fieldClass, $relationship, $this->obj->fieldLabel($relationship), $this->obj->$relationship(), SilvercartGridFieldConfig_RelationEditor::create()
                    );
                    if ($this->tabbed) {
                        $fields->addFieldToTab("Root.$relationship", $grid);
                    } else {
                        $fields->push($grid);
                    }
                }
            }
        }

        return $fields;
    }

}