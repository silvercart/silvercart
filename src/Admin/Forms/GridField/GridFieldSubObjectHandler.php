<?php

namespace SilverCart\Admin\Forms\GridField;

use ReflectionClass;
use SilverCart\Dev\Tools;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataList;
use SilverStripe\View\Requirements;

/**
 * GridField Component to handle sub objects of the GridFields base object.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_Components
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldSubObjectHandler implements GridField_HTMLProvider, GridField_ActionProvider, GridField_URLHandler, GridField_ColumnProvider {

    /**
     * Parent object
     *
     * @var DataObject
     */
    protected $parentObject;

    /**
     * Class name to add actions for
     *
     * @var string
     */
    protected $targetClassName;

    /**
     * Sublist
     *
     * @var DataList
     */
    protected $subList;

    /**
     * URL handlers
     *
     * @var array
     */
    protected $URLHandlers = null;
    
    /**
     * Name of the sub list template
     *
     * @var string
     */
    protected $subListTemplate = 'GridFieldSubObjectHandler_sublist';

    /**
     * Sets the defaults.
     * 
     * @param DataObject $parentObject    The parent object.
     * @param string     $targetClassName The target class to execute action for
     * @param DataList   $subList         The sub list to add objects to.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function __construct($parentObject, $targetClassName, $subList) {
        $this->parentObject    = $parentObject;
        $this->targetClassName = $targetClassName;
        $this->subList         = $subList;
        $this->subListTemplate = static::class . '_sublist';
    }
    
    /**
     * Returns the sub list template name.
     * 
     * @return string
     */
    public function getSubListTemplate() {
        return $this->subListTemplate;
    }

    /**
     * Sets the sub list template name.
     * 
     * @param string $subListTemplate Sub list template
     * 
     * @return void
     */
    public function setSubListTemplate($subListTemplate) {
        $this->subListTemplate = $subListTemplate;
    }
    
    /**
     * Returns the related sub objects.
     * 
     * @param DataObject $record Base record.
     * 
     * @return DataList
     */
    public function getSubObjects($record) {
        $reflection = new ReflectionClass($record->ClassName);
        $relationID = $reflection->getShortName() . 'ID';
        $subObjects = DataObject::get($this->targetClassName)->filter($relationID, $record->ID);
        return $subObjects;
    }
    
    /**
     * Returns the DropdownField to choose sub objects from.
     * 
     * @param DataObject $record Record displayed in this row
     * 
     * @return DropdownField
     */
    public function getDropdownField(GridField $gridField, DataObject $record) : ?DropdownField
    {
        $dropdownField = null;
        $subObjects    = $this->getSubObjects($record);
        $subObjectsMap = $subObjects->map()->toArray();
        $reflection    = new ReflectionClass($this->targetClassName);
        $relationName  = $reflection->getShortName() . 's';
        $parent        = $this->parentObject;
        
        foreach ($parent->{$relationName}() as $relation) {
            if (array_key_exists($relation->ID, $subObjectsMap)) {
                unset($subObjectsMap[$relation->ID]);
            }
        }
        if (count($subObjectsMap) > 0) {
            $dropdownField = DropdownField::create($gridField->getName() . 'SubObjects[' . $record->ID . ']', '', $subObjectsMap);
        }
        return $dropdownField;
    }
    
    /**
     * Returns the columns name for the sub object column.
     * 
     * @return string
     */
    public function getAddSubObjectColumnName() {
        return 'AddSubObject';
    }

    /**
     * Calculate the name of the gridfield relative to the Form
     *
     * @param GridField $base Base GridField
     * 
     * @return string
     */
    protected function getNameFromParent($base) {
        $name = array();

        do {
            array_unshift($name, $base->getName());
            $base = $base->getForm();
        } while ($base && !($base instanceof Form));

        return implode('.', $name);
    }
    
    /***************************************************************************
     * GridField_HTMLProvider
     ***************************************************************************/

    /**
     * Adds the form fields for the batch actions
     * 
     * @param GridField $gridField GridField to get HTML fragments for
     * 
     * @return array
     */
    public function getHTMLFragments($gridField) {
        Requirements::css('silvercart/silvercart:client/admin/css/GridFieldSubObjectHandler.css');
        Requirements::javascript('silvercart/silvercart:client/admin/javascript/GridFieldSubObjectHandler.js');
        
        
        $state = array(
            'grid'       => $this->getNameFromParent($gridField),
            'actionName' => 'removesubobject',
            'args'       => array(),
        );
        $activateState = array(
            'grid'       => $this->getNameFromParent($gridField),
            'actionName' => 'activatesubobject',
            'args'       => array(),
        );
        $deactivateState = array(
            'grid'       => $this->getNameFromParent($gridField),
            'actionName' => 'deactivatesubobject',
            'args'       => array(),
        );
        $defaultState = array(
            'grid'       => $this->getNameFromParent($gridField),
            'actionName' => 'defaultsubobject',
            'args'       => array(),
        );
        $undefaultState = array(
            'grid'       => $this->getNameFromParent($gridField),
            'actionName' => 'undefaultsubobject',
            'args'       => array(),
        );

        $actionID = preg_replace('/[^\w]+/', '_', uniqid('', true));
        Tools::Session()->set($actionID, $state);
        $activateActionID = preg_replace('/[^\w]+/', '_', uniqid('', true));
        Tools::Session()->set($activateActionID, $activateState);
        $deactivateActionID = preg_replace('/[^\w]+/', '_', uniqid('', true));
        Tools::Session()->set($deactivateActionID, $deactivateState);
        $defaultActionID = preg_replace('/[^\w]+/', '_', uniqid('', true));
        Tools::Session()->set($defaultActionID, $defaultState);
        $undefaultActionID = preg_replace('/[^\w]+/', '_', uniqid('', true));
        Tools::Session()->set($undefaultActionID, $undefaultState);
        
        $lists = '';
        $sublist   = $this->subList;
        $recordIDs = $gridField->getList()->map('ID','ID')->toArray();
        foreach ($recordIDs as $recordID) {
            $reflection = new ReflectionClass($gridField->getModelClass());
            $relationID = $reflection->getShortName() . 'ID';
            $list = $sublist->filter($relationID, $recordID);
            if ($list instanceof DataList &&
                $list->exists()) {
                $lists .= $list->customise(array(
                    'Items'          => $list,
                    'ParentRecordID' => $recordID,
                    'TargetURL'      => $gridField->Link(),
                    'ActionID'       => $actionID,
                    'ActivateActionID'   => $activateActionID,
                    'DeactivateActionID' => $deactivateActionID,
                    'DefaultActionID'    => $defaultActionID,
                    'UndefaultActionID'  => $undefaultActionID,
                    'FieldName'          => $gridField->getName(),
                ))->renderWith($this->getSubListTemplate());
            }
        }
        
        return array(
            'before' => '<input type="hidden" value="" name="SubObjectParentID" />' 
                      . '<input type="hidden" value="" name="SubObjectID" />',
            'after'  => '<div class="sub-object-lists" data-target-gridfield="' . $gridField->getName() . '">' . $lists . '</div>',
        );
    }
    
    /***************************************************************************
     * GridField_ActionProvider
     ***************************************************************************/

    /**
     * Returns the actions handled by this component.
     * 
     * @param GridField $gridField GridField to get actions for
     * 
     * @return array 
     */
    public function getActions($gridField) {
        return array(
            'addsubobject'    => 'addSubObject',
            'removesubobject' => 'removeSubObject',
            'activatesubobject' => 'activatesubobject',
            'deactivatesubobject' => 'deactivatesubobject',
            'defaultsubobject' => 'defaultsubobject',
            'undefaultsubobject' => 'undefaultsubobject',
        );
    }

    /**
     * Handles the given action context.
     * 
     * @param GridField $gridField  GridField to handle action for
     * @param string    $actionName Name of the action to handle
     * @param array     $arguments  Arguments
     * @param array     $data       Post data to handle
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if ($actionName == 'addsubobject') {
            $recordID    = $data['SubObjectParentID'];
            $subObjectID = $data[$gridField->getName() . 'SubObjects'][$recordID];
            
            $list = $gridField->getList();
            $parent    = DataObject::get($gridField->getModelClass())->byID($recordID);
            $subObject = DataObject::get($this->targetClassName)->byID($subObjectID);
            
            if ($subObject instanceof $this->targetClassName &&
                $subObject->exists()) {
                $this->subList->add($subObject);
            }
        } elseif ($actionName == 'removesubobject' ||
                  $actionName == 'activatesubobject' ||
                  $actionName == 'deactivatesubobject' ||
                  $actionName == 'defaultsubobject' ||
                  $actionName == 'undefaultsubobject') {
            
            $recordID    = $data['SubObjectParentID'];
            $subObjectID = $data['SubObjectID'];
            
            $list      = $gridField->getList();
            $parent    = DataObject::get($gridField->getModelClass())->byID($recordID);
            $subObject = DataObject::get($this->targetClassName)->byID($subObjectID);
            
            if ($subObject instanceof $this->targetClassName &&
                $subObject->exists()) {
                if ($actionName == 'defaultsubobject' ||
                    $actionName == 'undefaultsubobject') {
                    $this->subList->add($subObject, array('IsDefault' => $actionName == 'defaultsubobject'));
                } elseif ($actionName == 'activatesubobject' ||
                          $actionName == 'deactivatesubobject') {
                    $this->subList->add($subObject, array('IsActive' => $actionName == 'activatesubobject'));
                } elseif ($actionName == 'removesubobject') {
                    $this->subList->remove($subObject);
                }
            }
        }
    }

    /***************************************************************************
     * GridField_URLHandler
     ***************************************************************************/

    /**
     * Returns the URL handlers for the batch actions.
     * 
     * @param GridField $gridField GridField to get URL handlers for
     * 
     * @return array
     */
    public function getURLHandlers($gridField) {
        if (is_null($this->URLHandlers)) {
            $this->URLHandlers  = array();
            $this->URLHandlers['test'] = 'test';
        }
        return $this->URLHandlers;
    }
    
    /***************************************************************************
     * GridField_ColumnProvider
     ***************************************************************************/
    
    /**
     * Modify the list of columns displayed in the table.
     * See {@link GridFieldDataColumns->getDisplayFields()} and {@link GridFieldDataColumns}.
     * 
     * @param GridField $gridField GridField to augment columns for
     * @param array     &$columns  List reference of all column names.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function augmentColumns($gridField, &$columns) {
        $columns = array_merge(
                array(
                    $this->getAddSubObjectColumnName(),
                ),
                $columns
        );
    }

    /**
     * Names of all columns which are affected by this component.
     * 
     * @param GridField $gridField GridField to get handled columns
     * 
     * @return array 
     */
    public function getColumnsHandled($gridField) {
        return array(
            $this->getAddSubObjectColumnName(),
        );
    }

    /**
     * HTML for the column, content of the <td> element.
     * 
     * @param GridField  $gridField  GridField to get column content for
     * @param DataObject $record     Record displayed in this row
     * @param string     $columnName Name of the column to get content for
     * 
     * @return string HTML for the column. Return NULL to skip.
     */
    public function getColumnContent($gridField, $record, $columnName) {
        if ($columnName == $this->getAddSubObjectColumnName()) {
            $content  = '---';
            $dropdown = $this->getDropdownField($gridField, $record);
            $action   = GridField_FormAction::create($gridField, 'AddSubObject' . $record->ID, false, "addsubobject", array('RecordID' => $record->ID))
                            ->addExtraClass('add-sub-object-button')
                            ->setTitle(_t(GridFieldSubObjectHandler::class . '.AddSubObjectColumnTitle', 'Add Sub Object'))
                            ->setAttribute('title', _t(GridFieldSubObjectHandler::class . '.AddSubObjectColumnTitle', 'Add Sub Object'))
                            ->setAttribute('data-icon', 'add')
                            ->setAttribute('data-select-target', $record->ID);
            if (!is_null($dropdown)) {
                $content = $dropdown->Field()
                         . $action->Field()
                         . '<input type="hidden" value="' . $record->ClassName . '" name="SubObjectParentClassName' . $record->ID . '" />';
            }
            return $content;
        }
    }

    /**
     * Attributes for the element containing the content returned by {@link getColumnContent()}.
     * 
     * @param GridField  $gridField  GridField to get column attributes for
     * @param DataObject $record     Record displayed in this row
     * @param string     $columnName Name of the column to get attributes for
     * 
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName) {
        return array('class' => 'col-add-sub-object action col-buttons');
    }

    /**
     * Additional metadata about the column which can be used by other components,
     * e.g. to set a title for a search column header.
     * 
     * @param GridField $gridField  GridField to get column meta data for
     * @param string    $columnName Name of the column to get meta data for
     * 
     * @return array Map of arbitrary metadata identifiers to their values.
     */
    public function getColumnMetadata($gridField, $columnName) {
        $title = _t(GridFieldSubObjectHandler::class . '.AddSubObjectColumnTitle', 'Add Sub Object');
        return array(
            'title' => $title,
        );
    }
}