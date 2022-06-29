<?php

namespace SilverCart\ORM;

use InvalidArgumentException;
use SilverCart\Dev\Tools;
use SilverCart\Forms\FormScaffolder;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderPosition;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\MoneyField;
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
class DataObjectExtension extends DataExtension
{
    /**
     * General import field names for has one relations.
     * 
     * @var string[]
     */
    private static $general_relation_fields = [
        'ID',
        'Name',
        'Title',
    ];
    /**
     * Import field names for object specific has one relations.
     * 
     * @var string[]
     */
    private static $object_relation_fields = [
        Member::class => [
            'CustomerNumber',
        ],
        Order::class => [
            'OrderNumber',
        ],
        Product::class => [
            'ProductNumberShop',
        ],
    ];
    /**
     * Import field names for has one relations of an object specific has one relation.
     * 
     * @var string[]
     */
    private static $sub_relations = [
        OrderPosition::class => [
            'Order',
        ],
        Order::class => [
            'Member',
        ],
    ];
    
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

    
    /**
     * Returns the field map dropdown source.
     * 
     * @param bool $includeRelations include relations?
     * 
     * @return array
     */
    public function getFieldMapDropdownSource(bool $includeRelations = true) : array
    {
        $targetObject   = $this->owner;
        $dropdownSource = ['' => ''];
        $this->fieldMapDropdownSourceAddDBFields($dropdownSource);
        if ($includeRelations) {
            $hasOneRelations = $targetObject->hasOne();
            foreach ($hasOneRelations as $relationName => $className) {
                $singleton = singleton($className);
                $this->fieldMapDropdownSourceAddRelations($dropdownSource, $singleton, $relationName);
            }
            $this->fieldMapDropdownSourceAddSubRelations($dropdownSource);
            // Todo: Add support for has-many
            // Todo: Add support for many-many
        }
        if (method_exists($this, 'get_dropdown_source_callbacks')) {
            $callbacks = self::get_dropdown_source_callbacks();
            if (array_key_exists($targetObject->ClassName, $callbacks)) {
                $dropdownSource = array_merge(
                        $dropdownSource,
                        $callbacks[$targetObject->ClassName]
                );
            }
        }
        unset($dropdownSource['PriceGross']);
        unset($dropdownSource['PriceNet']);
        unset($dropdownSource['MSRPrice']);
        unset($dropdownSource['PurchasePrice']);
        asort($dropdownSource);
        return $dropdownSource;
    }
    
    /**
     * Adds the db fields to the field map dropdown fields.
     * 
     * @param array  $dropdownSource Dropdown source to extend
     * @param string $relationName   Relation name
     * @param string $labelPrefix    Label prefix
     * 
     * @return DataObject
     */
    public function fieldMapDropdownSourceAddDBFields(array &$dropdownSource, string $relationName = null, string $labelPrefix = '') : DataObject
    {
        $targetObject = $this->owner;
        $db = array_merge(
                ['Created' => 'DBDatetime'],
                $targetObject->config()->db
        );
        if ($targetObject->hasExtension(TranslatableDataObjectExtension::class)) {
            $languageTargetObject = singleton("{$targetObject->ClassName}Translation");
            $db = array_merge(
                    $languageTargetObject->config()->db,
                    $db
            );
        }
        $labelAmount   = _t(MoneyField::class . '.FIELDLABELAMOUNT', 'Amount');
        $labelCurrency = _t(MoneyField::class . '.FIELDLABELCURRENCY', 'Currency');
        foreach ($db as $fieldName => $fieldType) {
            $arrayKey = $relationName === null ? $fieldName : "{$relationName}.{$fieldName}";
            if ($targetObject->dbObject($fieldName) instanceof DBMoney) {
                $dropdownSource["{$arrayKey}Amount"]   = "{$labelPrefix}{$targetObject->fieldLabel($fieldName)} {$labelAmount}";
                $dropdownSource["{$arrayKey}Currency"] = "{$labelPrefix}{$targetObject->fieldLabel($fieldName)} {$labelCurrency}";
                $dropdownSource[$arrayKey]             = "{$labelPrefix}{$targetObject->fieldLabel($fieldName)}";
            } else {
                $dropdownSource[$arrayKey] = "{$labelPrefix}{$targetObject->fieldLabel($fieldName)}";
            }
        }
        return $this->owner;
    }
    
    /**
     * Adds the relations to the field map dropdown fields.
     * 
     * @param array      $dropdownSource Dropdown source to extend
     * @param DataObject $singleton      Relation singleton
     * @param string     $relationName   Relation name
     * @param string     $labelPrefix    Label prefix
     * 
     * @return DataObject
     */
    public function fieldMapDropdownSourceAddRelations(array &$dropdownSource, DataObject $singleton, string $relationName, string $labelPrefix = '') : DataObject
    {
        $targetObject   = $this->owner;
        $relationFields = (array) $targetObject->config()->general_relation_fields;
        if (array_key_exists($singleton->ClassName, (array) $targetObject->config()->object_relation_fields)) {
            $relationFields = array_merge($relationFields, $targetObject->config()->object_relation_fields[$singleton->ClassName]);
        }
        foreach ($relationFields as $relationField) {
            if ($singleton->hasField($relationField)
             || ($singleton->hasExtension(TranslatableDataObjectExtension::class)
              && singleton($singleton->getTranslationClassName())->hasField($relationField))
            ) {
                $i18nKey = $relationName;
                if (strpos($i18nKey, '.') !== false) {
                    $parts = explode('.', $i18nKey);
                    $i18nKey = array_pop($parts);
                }
                $dropdownSource["{$relationName}.{$relationField}"] = "{$labelPrefix}{$targetObject->fieldLabel($i18nKey)} ({$singleton->fieldLabel($relationField)})";
            }
        }
        return $this->owner;
    }
    
    /**
     * Adds the sub relations to the field map dropdown fields.
     * 
     * @param array  $dropdownSource     Dropdown source to extend
     * @param string $labelPrefix        Label prefix
     * @param string $parentRelationName Parent relation name
     * 
     * @return DataObject
     */
    public function fieldMapDropdownSourceAddSubRelations(array &$dropdownSource, string $labelPrefix = '', string $parentRelationName = null) : DataObject
    {
        $targetObject = $this->owner;
        if (!array_key_exists($targetObject->ClassName, (array) $targetObject->config()->sub_relations)) {
            return $this->owner;
        }
        foreach ($targetObject->config()->sub_relations[$targetObject->ClassName] as $subRelation) {
            try {
                $hasOneRelation = $targetObject->getComponent($subRelation);
                if ($hasOneRelation instanceof DataObject) {
                    $fullRelationName = $parentRelationName === null ? $subRelation : "{$parentRelationName}.{$subRelation}";
                    $hasOneRelation->fieldMapDropdownSourceAddDBFields($dropdownSource, $fullRelationName, "{$labelPrefix}{$hasOneRelation->i18n_singular_name()}: ");
                    $hasOneRelations = $hasOneRelation->hasOne();
                    foreach ($hasOneRelations as $relationName => $className) {
                        $subSingleton = singleton($className);
                        $hasOneRelation->fieldMapDropdownSourceAddRelations($dropdownSource, $subSingleton, "{$subRelation}.{$relationName}", "{$labelPrefix}{$hasOneRelation->i18n_singular_name()} > {$subSingleton->i18n_singular_name()}: ");
                    }
                    $hasOneRelation->fieldMapDropdownSourceAddSubRelations($dropdownSource, "{$labelPrefix}{$hasOneRelation->i18n_singular_name()} > ", $subRelation);
                }
            } catch(InvalidArgumentException $e) {
                continue;
            }
        }
        return $this->owner;
    }
}