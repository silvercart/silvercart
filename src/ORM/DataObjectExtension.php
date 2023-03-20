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
use SilverCart\View\IteratorSupport;
use SilverStripe\Admin\CMSMenu;
use SilverStripe\CMS\Model\RedirectorPage;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config as SilverStripeConfig;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\MoneyField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\FieldType\DBMoney;
use SilverStripe\Security\Member;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ViewableData;
use function _t;
use function singleton;

/**
 * Extension for every DataObject.
 *
 * @package SilverCart
 * @subpackage ORM
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property DataObject $owner Owner
 */
class DataObjectExtension extends DataExtension
{
    use IteratorSupport;
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
     * Dropdown source.
     * 
     * @var array[]
     */
    protected $dropdownSource = [];
    
    /**
     * Returns the table name respecting the current Versioned stage.
     * 
     * @return string
     */
    public function getStageTableName() : string
    {
        $tableName = SilverStripeConfig::inst()->get(get_class($this->owner), 'table_name');
        if ($this->owner->hasExtension(Versioned::class)) {
            $tableName = $this->owner->stageTable($tableName, Versioned::get_stage());
        }
        return (string) $tableName;
    }
    
    /**
     * Handles UseAsRootForMainNavigation property (can only be set for a single 
     * page).
     * 
     * @param string $fromStage        Stage to publish from
     * @param string $toStage          Stage to publish to
     * @param bool   $createNewVersion Create new version or not?
     * 
     * @return void
     */
    public function onBeforeVersionedPublish(string $fromStage, string $toStage, bool $createNewVersion) : void
    {
        if ($toStage == 'Live') {
            if ($this->owner instanceof Page
             && $this->owner->UseAsRootForMainNavigation
            ) {
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
     * @return DBHTMLText
     */
    public function getAdminQuickPreview() : DBHTMLText
    {
        return $this->owner->renderWith("{$this->owner->ClassName}AdminQuickPreview");
    }
    
    /**
     * Returns the record as a array map with non escaped values
     * 
     * @param bool $toDisplayWithinHtml Set this to true to replace html special chars with its entities
     * 
     * @return array
     */
    public function toRawMap(bool $toDisplayWithinHtml = false) : array
    {
        $record = $this->owner->toMap();
        $rawMap = [];
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
     */
    public function ClassNameCSS() : string
    {
        return str_replace(['/', '\\'], '-', $this->owner->ClassName);
    }
    
    /**
     * Checks whether the given field is changed.
     * 
     * @param string $fieldName Field name to check change for
     * 
     * @return bool
     */
    public function fieldValueIsChanged(string $fieldName) : bool
    {
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
     * @return bool
     */
    public function moneyFieldIsChanged(string $fieldName) : bool
    {
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
     * @return bool
     */
    public function hasOneRelationIsChanged(string $relationName) : bool
    {
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
    public static function getCMSFields(DataObject $dataObject, string $neighbourFieldOfTranslationFields = null, bool $insertLangugeFieldsAfter = true, bool $tabbed = true) : FieldList
    {
        $params = [
            'includeRelations' => $dataObject->isInDB(),
            'tabbed'           => $tabbed,
            'ajaxSafe'         => true,
        ];
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
     */
    public static function scaffoldFormFields(DataObject $dataObject, array $_params = null) : FieldList
    {
        $params = array_merge(
                [
                    'tabbed'           => false,
                    'includeRelations' => false,
                    'restrictFields'   => false,
                    'fieldClasses'     => false,
                    'ajaxSafe'         => false
                ],
                (array) $_params
        );

        $fs                   = FormScaffolder::create($dataObject);
        $fs->tabbed           = $params['tabbed'];
        $fs->includeRelations = $params['includeRelations'];
        $fs->restrictFields   = $params['restrictFields'];
        $fs->fieldClasses     = $params['fieldClasses'];
        $fs->ajaxSafe         = $params['ajaxSafe'];

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
     */
    public function scaffoldFieldLabels() : array
    {
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
        $key = get_class($this->owner);
        if (array_key_exists($key, $this->dropdownSource)) {
            return $this->dropdownSource[$key];
        }
        $targetObject               = $this->owner;
        $this->dropdownSource[$key] = ['' => ''];
        $this->fieldMapDropdownSourceAddDBFields();
        if ($includeRelations) {
            $hasOneRelations = $targetObject->hasOne();
            foreach ($hasOneRelations as $relationName => $className) {
                $singleton = singleton($className);
                $this->fieldMapDropdownSourceAddRelations($singleton, $relationName);
            }
            $this->fieldMapDropdownSourceAddSubRelations();
            $hasManyRelations = $targetObject->hasMany();
            foreach ($hasManyRelations as $relationName => $className) {
                $singleton = singleton($className);
                $this->fieldMapDropdownSourceAddRelations($singleton, $relationName);
            }
            $manyManyRelations = $targetObject->manyMany();
            foreach ($manyManyRelations as $relationName => $className) {
                if (is_array($className)) {
                    $className = $className['through'];
                }
                if (strpos($className, '.') !== false) {
                    $className = substr($className, 0, strpos($className, '.'));
                }
                $singleton = singleton($className);
                $this->fieldMapDropdownSourceAddRelations($singleton, $relationName);
            }
        }
        if (method_exists($this, 'get_dropdown_source_callbacks')) {
            $callbacks = self::get_dropdown_source_callbacks();
            if (array_key_exists($targetObject->ClassName, $callbacks)) {
                $this->dropdownSource[$key] = array_merge(
                        $this->dropdownSource[$key],
                        $callbacks[$targetObject->ClassName]
                );
            }
        }
        $this->owner->extend('updateFieldMapDropdownSource', $this->dropdownSource[$key]);
        unset($this->dropdownSource[$key]['PriceGross']);
        unset($this->dropdownSource[$key]['PriceNet']);
        unset($this->dropdownSource[$key]['MSRPrice']);
        unset($this->dropdownSource[$key]['PurchasePrice']);
        asort($this->dropdownSource[$key]);
        return $this->dropdownSource[$key];
    }
    
    /**
     * Adds the db fields to the field map dropdown fields.
     * 
     * @return DataObject
     */
    public function fieldMapDropdownSourceAddDBFields(string $labelPrefix = '') : DataObject
    {
        $key          = get_class($this->owner);
        $targetObject = $this->owner;
        $db           = array_merge(
                ['Created' => 'DBDatetime'],
                $targetObject->config()->db
        );
        if ($targetObject->hasExtension(TranslatableDataObjectExtension::class)) {
            $languageTargetObject = singleton("{$targetObject->ClassName}Translation");
            $db                   = array_merge(
                    $languageTargetObject->config()->db,
                    $db
            );
        }
        $labelAmount   = _t(MoneyField::class . '.FIELDLABELAMOUNT', 'Amount');
        $labelCurrency = _t(MoneyField::class . '.FIELDLABELCURRENCY', 'Currency');
        foreach ($db as $fieldName => $fieldType) {
            $arrayKey = $fieldName;
            if ($targetObject->dbObject($fieldName) instanceof DBMoney) {
                $this->dropdownSource[$key]["{$arrayKey}Amount"]   = "{$labelPrefix}{$targetObject->fieldLabel($fieldName)} {$labelAmount}";
                $this->dropdownSource[$key]["{$arrayKey}Currency"] = "{$labelPrefix}{$targetObject->fieldLabel($fieldName)} {$labelCurrency}";
                $this->dropdownSource[$key][$arrayKey]             = "{$labelPrefix}{$targetObject->fieldLabel($fieldName)}";
            } else {
                $this->dropdownSource[$key][$arrayKey] = "{$labelPrefix}{$targetObject->fieldLabel($fieldName)}";
            }
        }
        return $this->owner;
    }
    
    /**
     * Adds the relations to the field map dropdown fields.
     * 
     * @param DataObject      $singleton    Relation singleton
     * @param string          $relationName Relation name
     * @param string          $labelPrefix  Label prefix
     * @param DataObject|null $relation     Relation target object
     * 
     * @return DataObject
     */
    public function fieldMapDropdownSourceAddRelations(DataObject $singleton, string $relationName, string $labelPrefix = '', ?DataObject $relation = null) : DataObject
    {
        $key            = get_class($this->owner);
        $targetObject   = $relation === null ? $this->owner : $relation;
        $relationFields = (array) $targetObject->config()->general_relation_fields;
        $ancestry       = ClassInfo::ancestry($singleton);
        foreach ($ancestry as $className) {
            if (in_array($className, [DataObject::class, ViewableData::class])) {
                continue;
            }
            if (array_key_exists($className, (array) $targetObject->config()->object_relation_fields)) {
                $relationFields = array_merge($relationFields, $targetObject->config()->object_relation_fields[$className]);
            }
            if ($className === $singleton->ClassName) {
                break;
            }
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
                $this->dropdownSource[$key]["{$relationName}.{$relationField}"] = "{$labelPrefix}{$targetObject->fieldLabel($i18nKey)} ({$singleton->fieldLabel($relationField)})";
            }
        }
        return $this->owner;
    }
    
    /**
     * Adds the sub relations to the field map dropdown fields.
     * 
     * @param string          $labelPrefix        Label prefix
     * @param string          $parentRelationName Parent relation name
     * @param DataObject|null $relation           Relation target object
     * 
     * @return DataObject
     */
    public function fieldMapDropdownSourceAddSubRelations(string $labelPrefix = '', string $parentRelationName = null, ?DataObject $relation = null) : DataObject
    {
        $targetObject = $relation === null ? $this->owner : $relation;
        if (!array_key_exists($targetObject->ClassName, (array) $targetObject->config()->sub_relations)) {
            return $this->owner;
        }
        foreach ($targetObject->config()->sub_relations[$targetObject->ClassName] as $subRelation) {
            try {
                $hasOneRelation = $targetObject->getComponent($subRelation);
                if ($hasOneRelation instanceof DataObject) {
                    $fullRelationName = $parentRelationName === null ? $subRelation : "{$parentRelationName}.{$subRelation}";
                    $this->fieldMapDropdownSourceAddRelationDBFields($hasOneRelation, $fullRelationName, "{$labelPrefix}{$hasOneRelation->i18n_singular_name()}: ");
                    $hasOneRelations = $hasOneRelation->hasOne();
                    foreach ($hasOneRelations as $relationName => $className) {
                        $subSingleton = singleton($className);
                        $this->fieldMapDropdownSourceAddRelations($subSingleton, "{$subRelation}.{$relationName}", "{$labelPrefix}{$hasOneRelation->i18n_singular_name()} > {$subSingleton->i18n_singular_name()}: ", $hasOneRelation);
                    }
                    $this->fieldMapDropdownSourceAddSubRelations("{$labelPrefix}{$hasOneRelation->i18n_singular_name()} > ", $subRelation, $hasOneRelation);
                }
            } catch(InvalidArgumentException $e) {
                continue;
            }
        }
        return $this->owner;
    }
    
    /**
     * Adds the DB fields of the given $relation.
     * 
     * @param DataObject $relation     Relation
     * @param string     $relationName Relation name
     * @param string     $labelPrefix  Label prefix
     * 
     * @return DataObject
     */
    public function fieldMapDropdownSourceAddRelationDBFields(DataObject $relation, string $relationName, string $labelPrefix = '') : DataObject
    {
        $key           = get_class($this->owner);
        $relationDB    = $relation->getFieldMapDropdownSource(false);
        $labelAmount   = _t(MoneyField::class . '.FIELDLABELAMOUNT', 'Amount');
        $labelCurrency = _t(MoneyField::class . '.FIELDLABELCURRENCY', 'Currency');
        foreach ($relationDB as $fieldName => $fieldType) {
            $arrayKey = "{$relationName}.{$fieldName}";
            if ($relation->dbObject($fieldName) instanceof DBMoney) {
                $this->dropdownSource[$key]["{$arrayKey}Amount"]   = "{$labelPrefix}{$relation->fieldLabel($fieldName)} {$labelAmount}";
                $this->dropdownSource[$key]["{$arrayKey}Currency"] = "{$labelPrefix}{$relation->fieldLabel($fieldName)} {$labelCurrency}";
                $this->dropdownSource[$key][$arrayKey]             = "{$labelPrefix}{$relation->fieldLabel($fieldName)}";
            } else {
                $this->dropdownSource[$key][$arrayKey] = "{$labelPrefix}{$relation->fieldLabel($fieldName)}";
            }
        }
        return $this->owner;
    }
    
    /**
     * Returns the admin link.
     * 
     * @return string
     */
    public function AdminLink() : string
    {
        $link = '';
        if ($this->owner->canEdit()) {
            $menuItems = CMSMenu::get_viewable_menu_items();
            foreach ($menuItems as $menuItem) {
                if (strpos($menuItem->controller, 'Product') === false) {
                    continue;
                }
                if (in_array($this->owner->ClassName, (array) SilverStripeConfig::inst()->get($menuItem->controller, 'managed_models'))) {
                    $class = str_replace(['/', '\\'], '-', $this->owner->ClassName);
                    $link  = Director::makeRelative("{$menuItem->url}/{$class}/EditForm/field/{$class}/item/{$this->owner->ID}/edit");
                }
            }
        }
        return $link;
    }
}