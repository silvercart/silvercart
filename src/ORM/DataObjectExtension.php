<?php

namespace SilverCart\ORM;

use SilverCart\Dev\Tools;
use SilverCart\Forms\FormScaffolder;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

/**
 * Extension for every DataObject.
 *
 * @package SilverCart
 * @subpackage ORM
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DataObjectExtension extends DataExtension {
    
    /**
     * Handles UseAsRootForMainNavigation property (can only be set for a single 
     * page).
     * 
     * @param string  $fromStage        Stage to publish from
     * @param string  $toStage          Stage to publish to
     * @param boolean $createNewVersion Create new version or not?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    public function onBeforeVersionedPublish($fromStage, $toStage, $createNewVersion) {
        if ($toStage == 'Live') {
            if ($this->owner instanceof Page &&
                $this->owner->UseAsRootForMainNavigation) {
                $pageTable = Tools::get_table_name(Page::class);
                DB::query('UPDATE ' . $pageTable . '_Live SET UseAsRootForMainNavigation = 0 WHERE ID != ' . $this->owner->ID);
            }
        }
    }
    
    /**
     * Checks whether the current visited page is a child of the context
     * RedirectionPage.
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.10.2014
     */
    public function IsRedirectedChild() : bool
    {
        $isRedirectedChild = false;
        if ($this->owner instanceof RedirectorPage
         && Controller::curr()->hasMethod('data')
        ) {
            if ($this->owner->LinkToID == Controller::curr()->data()->ID) {
                $isRedirectedChild = true;
            } else {
                $ancestors = Controller::curr()->data()->getAncestors();
                foreach ($ancestors as $ancestor) {
                    if ($this->owner->LinkToID == $ancestor->ID) {
                        $isRedirectedChild = true;
                        break;
                    }
                }
            }
        }
        return $isRedirectedChild;
    }
    
    /**
     * Returns a quick preview to use in a related models admin form
     * 
     * @return string
     */
    public function getAdminQuickPreview() {
        return $this->owner->renderWith($this->owner->ClassName . 'AdminQuickPreview');
    }
    
    /**
     * Returns the record as a array map with non escaped values
     * 
     * @param bool $toDisplayWithinHtml Set this to true to replace html special chars with its entities
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function toRawMap($toDisplayWithinHtml = false) {
        $record = $this->owner->toMap();
        $rawMap = array();
        foreach ($record as $field => $value) {
            if ($toDisplayWithinHtml) {
                $value = htmlspecialchars($value);
            }
            $rawValue = stripslashes($value);
            $rawMap[$field] = $rawValue;
        }
        return $rawMap;
    }
    
    /**
     * Returns the ClassName to use as a CSS class.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function ClassNameCSS() {
        return str_replace(['/', '\\'], '-', $this->owner->ClassName);
    }
    
    /**
     * Checks whether the given field is changed.
     * 
     * @param string $fieldName Field name to check change for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     */
    public function fieldValueIsChanged($fieldName) {
        $isChanged = false;
        if ($this->owner->isChanged($fieldName)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $original = $changed[$fieldName]['before'];
            $new      = $changed[$fieldName]['after'];
            if ($new != $original) {
                $isChanged = true;
            }
        }
        return $isChanged;
    }
    
    /**
     * Checks whether the money field with the given fieldname is changed.
     * 
     * @param string $fieldName Field name to check change for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     */
    public function moneyFieldIsChanged($fieldName) {
        $isChanged  = false;
        $amountName = $fieldName . 'Amount';
        if ($this->owner->isChanged($fieldName)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $original = $changed[$fieldName]['before'];
            $new      = $changed[$fieldName]['after'];
            $originalAmount = $this->owner->{$amountName};
            $newAmount      = 0;
            if (!is_null($original)) {
                $originalAmount = $original->getAmount();
            }
            if (!is_null($new)) {
                $newAmount = $new->getAmount();
            }
            if ($newAmount != $originalAmount) {
                $isChanged = true;
            }
        } elseif ($this->owner->isChanged($amountName)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $originalAmount = $changed[$amountName]['before'];
            $newAmount      = $changed[$amountName]['after'];
            if ($newAmount != $originalAmount) {
                $isChanged = true;
            }
        }
        return $isChanged;
    }
    
    /**
     * Checks whether the given has one relation is changed.
     * 
     * @param string $relationName Relation name to check change for
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.10.2014
     */
    public function hasOneRelationIsChanged($relationName) {
        $isChanged  = false;
        $relationID = $relationName . 'ID';
        if ($this->owner->isChanged($relationID)) {
            $changed  = $this->owner->getChangedFields(false, 1);
            $original = (int)$changed[$relationID]['before'];
            $new      = (int)$changed[$relationID]['after'];
            if ($new != $original) {
                $isChanged = true;
            }
        }
        return $isChanged;
    }
    
    /**
     * Clone of DataObject::getCMSFields() with some additional SilverCart
     * related features.
     * <ul>
     *  <li>Restricted fields can be updated by DataExtension (updateRestrictCMSFields).</li>
     *  <li>Translation fields of DataObjects with SilverCart based translation model will be scaffolded.</li>
     * </ul>
     * 
     * @param DataObject $dataObject                        DataObject to get CMS fields for
     * @param string     $neighbourFieldOfTranslationFields Name of the field to insert language fields after or before
     * @param bool       $insertLangugeFieldsAfter          Determines whether to add language fields before or after the given neighbour field
     * @param bool       $tabbed                            Determines whether get tabbed fields or not
     * 
     * @return FieldList
     */
    public static function getCMSFields(DataObject $dataObject, $neighbourFieldOfTranslationFields = null, $insertLangugeFieldsAfter = true, $tabbed = true) {
        $params = array(
            'includeRelations'  => $dataObject->isInDB(),
            'tabbed'            => $tabbed,
            'ajaxSafe'          => true,
        );
        $restrictFields = array();
        $dataObject->extend('updateRestrictCMSFields', $restrictFields);
        if (!empty($restrictFields)) {
            $params['restrictFields'] = $restrictFields;
        }

        $tabbedFields = self::scaffoldFormFields($dataObject, $params);
        
        if ($dataObject->has_extension(get_class($dataObject), TranslatableDataObjectExtension::class)) {
            $languageFields = TranslationTools::prepare_cms_fields($dataObject->getTranslationClassName());
            foreach ($languageFields as $languageField) {
                if (!is_null($neighbourFieldOfTranslationFields)) {
                    if ($insertLangugeFieldsAfter) {
                        $tabbedFields->insertAfter($languageField, $neighbourFieldOfTranslationFields);
                        
                        /*
                         * Change the name of the field the insert the next field
                         * Otherwise the sort order would be inverted
                         */
                        $neighbourFieldOfTranslationFields = $languageField->getName();
                    } else {
                        $tabbedFields->insertBefore($languageField, $neighbourFieldOfTranslationFields);
                    }
                } else {
                    $tabbedFields->addFieldToTab('Root.Main', $languageField);
                }
            }
        }
        if ($dataObject->hasMethod('LinkTracking')
         && !$dataObject->LinkTracking()->exists()
        ) {
            $tabbedFields->removeByName('LinkTracking');
        }
        if ($dataObject->hasMethod('FileTracking')
         && !$dataObject->FileTracking()->exists()
        ) {
            $tabbedFields->removeByName('FileTracking');
        }

        $dataObject->extend('updateCMSFields', $tabbedFields);

        return $tabbedFields;
    }

    /**
     * Scaffold a simple edit form for all properties on this dataobject,
     * based on default {@link FormField} mapping in {@link DBField::scaffoldFormField()}.
     * Field labels/titles will be auto generated from {@link DataObject::fieldLabels()}.
     * 
     * @param DataObject $dataObject DataObject to scaffold form fields for
     * @param array      $_params    Associative array passing through properties to {@link FormScaffolder}.
     * 
     * @return FieldList
     * 
     * @uses FormScaffolder
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public static function scaffoldFormFields(DataObject $dataObject, $_params = null) {
        $params = array_merge(
                array(
                    'tabbed' => false,
                    'includeRelations' => false,
                    'restrictFields' => false,
                    'fieldClasses' => false,
                    'ajaxSafe' => false
                ),
                (array) $_params
        );

        $fs = new FormScaffolder($dataObject);
        $fs->tabbed             = $params['tabbed'];
        $fs->includeRelations   = $params['includeRelations'];
        $fs->restrictFields     = $params['restrictFields'];
        $fs->fieldClasses       = $params['fieldClasses'];
        $fs->ajaxSafe           = $params['ajaxSafe'];

        return $fs->getFieldList();
    }
    
    /**
     * Scaffolds the field labels by using a simple pattern.
     * <code>
     * $labels = [
     *     '<db-field-name>'                   => _t('<DataObject-full-qualified-class-name>.<db-field-name>',                   '<db-field-name>'),
     *     '<has-one-relation-name>'           => _t('<DataObject-full-qualified-class-name>.<has-one-relation-name>',           '<has-one-relation-name>'),
     *     '<has-many-relation-name>'          => _t('<DataObject-full-qualified-class-name>.<has-many-relation-name>',          '<has-many-relation-name>'),
     *     '<many-many-relation-name>'         => _t('<DataObject-full-qualified-class-name>.<many-many-relation-name>',         '<many-many-relation-name>'),
     *     '<belongs-many-many-relation-name>' => _t('<DataObject-full-qualified-class-name>.<belongs-many-many-relation-name>', '<belongs-many-many-relation-name>')
     * ];
     * </code>
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.05.2018
     */
    public function scaffoldFieldLabels() {
        $fieldsToGetLabelsFor = array_merge(
            array_keys($this->owner->config()->get('db')),
            array_keys($this->owner->config()->get('has_one')),
            array_keys($this->owner->config()->get('has_many')),
            array_keys($this->owner->config()->get('many_many')),
            array_keys($this->owner->config()->get('belongs_many_many'))
        );
        $fieldLabels = [];
        foreach ($fieldsToGetLabelsFor as $fieldName) {
            $fieldLabels[$fieldName] = _t(get_class($this->owner) . '.' . $fieldName, $fieldName);
        }
        return $fieldLabels;
    }

}