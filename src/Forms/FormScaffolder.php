<?php

namespace SilverCart\Forms;

use SilverCart\Admin\Controllers\ModelAdmin_ExclusiveRelationInterface;
use SilverCart\Admin\Controllers\ModelAdmin_ReadonlyInterface;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_ExclusiveRelationEditor;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_Readonly;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Model\Translation\TranslationExtension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\GridField\GridField;

/**
 * Extension for every DataObject
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class FormScaffolder extends \SilverStripe\Forms\FormScaffolder {

    /**
     * Gets the form fields as defined through the metadata
     * on {@link $obj} and the custom parameters passed to FormScaffolder.
     * Depending on those parameters, the fields can be used in ajax-context,
     * contain {@link TabSet}s etc.
     * 
     * Uses SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor and 
     * SilverCart\Admin\Forms\GridField\GridFieldConfig_ExclusiveRelationEditor instead of
     * SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor.
     * 
     * @return FieldList
     */
    public function getFieldList() {
        $fields = new FieldList();
        $excludeFromScaffolding = [];
        if ($this->obj->hasMethod('excludeFromScaffolding')) {
            $excludeFromScaffolding = $this->obj->excludeFromScaffolding();
        }

        // tabbed or untabbed
        if ($this->tabbed) {
            $fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
            $mainTab->setTitle(_t(__CLASS__.'.TABMAIN', 'Main'));
        }

        // Add logical fields directly specified in db config
        foreach ($this->obj->config()->get('db') as $fieldName => $fieldType) {
            // Skip restricted fields
            if (in_array($fieldName, $excludeFromScaffolding) || ($this->restrictFields && !in_array($fieldName, $this->restrictFields))) {
                continue;
            }

            // @todo Pass localized title
            if ($this->fieldClasses && isset($this->fieldClasses[$fieldName])) {
                $fieldClass = $this->fieldClasses[$fieldName];
                $fieldObject = new $fieldClass($fieldName);
            } else {
                $fieldObject = $this
                    ->obj
                    ->dbObject($fieldName)
                    ->scaffoldFormField(null, $this->getParamsArray());
            }
            // Allow fields to opt-out of scaffolding
            if (!$fieldObject) {
                continue;
            }
            $fieldObject->setTitle($this->obj->fieldLabel($fieldName));
            if ($this->tabbed) {
                $fields->addFieldToTab("Root.Main", $fieldObject);
            } else {
                $fields->push($fieldObject);
            }
        }

        // add has_one relation fields
        if ($this->obj->hasOne()) {
            foreach ($this->obj->hasOne() as $relationship => $component) {
                if (in_array($relationship, $excludeFromScaffolding) || ($this->restrictFields && !in_array($relationship, $this->restrictFields))) {
                    continue;
                }
                $fieldName = $component === 'SilverStripe\\ORM\\DataObject'
                    ? $relationship // Polymorphic has_one field is composite, so don't refer to ID subfield
                    : "{$relationship}ID";
                if ($this->fieldClasses && isset($this->fieldClasses[$fieldName])) {
                    $fieldClass = $this->fieldClasses[$fieldName];
                    $hasOneField = new $fieldClass($fieldName);
                } else {
                    $hasOneField = $this->obj->dbObject($fieldName)->scaffoldFormField(null, $this->getParamsArray());
                }
                if (empty($hasOneField)) {
                    continue; // Allow fields to opt out of scaffolding
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
            // add has_many relation fields
            if ($this->obj->hasMany()
                    && ($this->includeRelations === true || isset($this->includeRelations['has_many']))) {
                foreach ($this->obj->hasMany() as $relationship => $component) {
                    if (in_array($relationship, $excludeFromScaffolding)) {
                        continue;
                    }
                    if ($this->tabbed) {
                        $fields->findOrMakeTab(
                            "Root.$relationship",
                            $this->obj->fieldLabel($relationship)
                        );
                    }
                    $fieldClass = (isset($this->fieldClasses[$relationship])) ? $this->fieldClasses[$relationship] : GridField::class;
                    if (singleton($component) instanceof ModelAdmin_ReadonlyInterface) {
                        $config = GridFieldConfig_Readonly::create();
                    } elseif (singleton($component) instanceof ModelAdmin_ExclusiveRelationInterface ||
                              $this->obj->has_extension($this->obj->$relationship()->dataClass(), TranslationExtension::class)) {
                        $config = GridFieldConfig_ExclusiveRelationEditor::create();
                    } else {
                        $config = GridFieldConfig_RelationEditor::create();
                    }
                    $fieldClass = (isset($this->fieldClasses[$relationship]))
                        ? $this->fieldClasses[$relationship]
                        : 'SilverStripe\\Forms\\GridField\\GridField';
                    /** @var GridField $grid */
                    $grid = Injector::inst()->create(
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

            if ($this->obj->manyMany()
                    && ($this->includeRelations === true || isset($this->includeRelations['many_many']))) {
                foreach ($this->obj->manyMany() as $relationship => $component) {
                    if (in_array($relationship, $excludeFromScaffolding)) {
                        continue;
                    }
                    if ($this->tabbed) {
                        $fields->findOrMakeTab(
                            "Root.$relationship",
                            $this->obj->fieldLabel($relationship)
                        );
                    }

                    $fieldClass = (isset($this->fieldClasses[$relationship]))
                        ? $this->fieldClasses[$relationship]
                        : 'SilverStripe\\Forms\\GridField\\GridField';

                    /** @var GridField $grid */
                    $grid = Injector::inst()->create(
                            $fieldClass,
                            $relationship,
                            $this->obj->fieldLabel($relationship),
                            $this->obj->$relationship(),
                            GridFieldConfig_RelationEditor::create()
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
    
    /**
	 * Return an array suitable for passing on to {@link DBField->scaffoldFormField()}
	 * without tying this call to a FormScaffolder interface.
     * Adds a reference to the context object.
	 * 
	 * @return array
	 */
    protected function getParamsArray() {
        return array_merge(
                parent::getParamsArray(),
                [
                    'object' => $this->obj
                ]
        );
    }

}