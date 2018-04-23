<?php

namespace SilverCart\Admin\Forms\GridField;

use ReflectionClass;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;

/**
 * Similar to {@link GridFieldConfig}, but adds some static helper methods.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldBatchController implements GridField_HTMLProvider, GridField_ActionProvider, GridField_URLHandler, GridField_ColumnProvider {

    /**
     * Batch actions for the given class name
     *
     * @var string
     */
    protected $targetBatchActions = null;
    
    /**
     * Batch action objects for the given class name
     *
     * @var string
     */
    protected $targetBatchActionObjects = null;

    /**
     * Fragment to write the batch actions to
     *
     * @var string
     */
    protected $targetFragment;

    /**
     * Class name to add actions for
     *
     * @var string
     */
    protected $targetClassName;

    /**
     * URL handlers
     *
     * @var array
     */
    protected $URLHandlers = null;

    /**
     * Mapping of all batch actions in relation to a DataObject
     *
     * @var array
     */
    public static $batchActions = array();

    /**
     * Sets the defaults.
     * 
     * @param string $targetClassName The target class to execute action for
     * @param string $targetFragment  The HTML fragment to write the button into
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function __construct($targetClassName, $targetFragment = "before") {
        $this->targetClassName  = $targetClassName;
        $this->targetFragment   = $targetFragment;
    }
    
    /**
     * Returns the fragment to write the batch actions to
     * 
     * @return string
     */
    public function getTargetFragment() {
        return $this->targetFragment;
    }

    /**
     * Sets the fragment to write the batch actions to
     * 
     * @param string $targetFragment Fragment to write the batch actions to
     * 
     * @return void
     */
    public function setTargetFragment($targetFragment) {
        $this->targetFragment = $targetFragment;
    }
    
    /**
     * Returns the class name to add actions for
     * 
     * @return string
     */
    public function getTargetClassName() {
        return $this->targetClassName;
    }

    /**
     * Sets the class name to add actions for
     * 
     * @param string $targetClassName Class name to add actions for
     * 
     * @return void
     */
    public function setTargetClassName($targetClassName) {
        $this->targetClassName = $targetClassName;
    }
    
    /**
     * Returns the batch actions for the given class name
     * 
     * @return array
     */
    public function getTargetBatchActions() {
        if (is_null($this->targetBatchActions)) {
            $this->targetBatchActions = self::getBatchActionsFor($this->getTargetClassName());
        }
        return $this->targetBatchActions;
    }
    
    /**
     * Returns the batch action objects for the given class name.
     * 
     * @return array
     */
    public function getTargetBatchActionObjects() {
        if (is_null($this->targetBatchActionObjects)) {
            $this->targetBatchActionObjects = array();
            $targetBatchActions = $this->getTargetBatchActions();
            foreach ($targetBatchActions as $targetBatchAction) {
                $this->targetBatchActionObjects[$targetBatchAction] = new $targetBatchAction();
            }
        }
        return $this->targetBatchActionObjects;
    }
    
    /**
     * Returns dropdown name
     * 
     * @return string
     */
    public function getDropdownName() {
        return 'GridFieldBatchControllerDropdown_' . $this->getTargetClassName();
    }
    
    /**
     * Returns checkbox name
     * 
     * @param bool $withBrackets Add brackets or not?
     * 
     * @return string
     */
    public function getCheckboxName($withBrackets = true) {
        $brackets = '';
        if ($withBrackets) {
            $brackets = '[]';
        }
        return 'GridFieldBatchControllerCheckbox_' . $this->getTargetClassName() . $brackets;
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
        Requirements::css('silvercart/silvercart:client/admin/css/GridFieldBatchController.css');
        Requirements::javascript('silvercart/silvercart:client/admin/javascript/GridFieldBatchController.js');
        $source = array(
            '' => 'Bitte wählen',
        );
        $targetBatchActionObjects = $this->getTargetBatchActionObjects();
        foreach ($targetBatchActionObjects as $targetBatchAction => $targetBatchActionObject) {
            $reflection = new ReflectionClass($targetBatchAction);
            $source[$reflection->getShortName()] = $targetBatchActionObject->getTitle();
            $targetBatchActionObject->RequireJavascript();
        }
        $dropdown = new DropdownField($this->getDropdownName(), $this->getDropdownName(), $source);
        $dropdown->addExtraClass('grid-batch-action-selector');
        
        $button = new GridField_FormAction(
                $gridField, 'execute_batch_action', _t(static::class . '.EXECUTE', 'Execute'), 'handleBatchAction', null
        );
        $button->setAttribute('data-icon', 'navigation');
        $button->addExtraClass('gridfield-button-batch');
        
        $forTemplate = new ArrayData(array());
        $forTemplate->Dropdown = $dropdown;
        $forTemplate->Button   = $button;
        
        $template = SSViewer::get_templates_by_class($this, '', __CLASS__);
        return array(
            $this->targetFragment => $forTemplate->renderWith($template),
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
            'handleBatchAction'     => 'handleBatchAction',
            'handleBatchCallback'   => 'handleBatchCallback',
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
        if ($actionName == 'handlebatchaction') {
            $targetBatchAction = 'SilverCart\\Admin\\Forms\\GridField\\' . $data[$this->getDropdownName()];
            if (class_exists($targetBatchAction)) {
                $object     = new $targetBatchAction();
                $recordIDs  = $data[$this->getCheckboxName(false)];
                return $object->handle($gridField, $recordIDs, $data);
            }
        }
    }
    
    /**
     * Handles a batch call back action.
     * 
     * @param string      $targetClassName Class to handle callback for
     * @param HTTPRequest $request         Request to handle
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public static function handleBatchCallback($targetClassName, HTTPRequest $request) {
        $result             = '';
        $targetBatchAction  = $request->postVar('scBatchAction');
        if (self::hasBatchActionFor($targetClassName, $targetBatchAction) &&
            class_exists($targetBatchAction)) {
            $object = new $targetBatchAction();
            $result = $object->getCallbackFormFields();
        }
        return $result;
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
            $batchActions       = $this->getTargetBatchActions();
            foreach ($batchActions as $batchAction) {
                $this->URLHandlers[$batchAction] = $batchAction;
            }
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
                    $this->getCheckboxName(),
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
            $this->getCheckboxName(),
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
        if ($columnName == $this->getCheckboxName()) {
            return '<input type="checkbox" name="' . $this->getCheckboxName() . '" value="' . $record->ID . '" />';
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
        return array('class' => 'col-batch-action-selector action');
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
        $title = sprintf(
                '<span class="icon icon-24 white icon-checked col-batch-action-header check-all" title="%s"> </span> | <span class="icon icon-24 white icon-not-checked col-batch-action-header uncheck-all" title="%s"> </span>',
                _t(static::class . '.MARK_ALL', 'Mark all'),
                _t(static::class . '.UNMARK_ALL', 'Unmark all')
        );
        return array(
            'title' => $title,
        );
    }
    
    /***************************************************************************
     * static section
     ***************************************************************************/

    /**
     * Adds a single Batch Action for the given Class Name.
     * 
     * @param string $className       Class name of the DataObject to add batch action to.
     * @param string $actionClassName Class name of the batch action to add.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.03.2013
     */
    public static function addBatchActionFor($className, $actionClassName) {
        if (!array_key_exists($className, self::$batchActions)) {
            self::$batchActions[$className] = array();
        }
        if (!in_array($actionClassName, self::$batchActions[$className])) {
            self::$batchActions[$className][] = $actionClassName;
        }
    }

    /**
     * Checks whether the DataObject with the given class name has batch actions.
     * 
     * @param string $className Class name to check
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.03.2013
     */
    public static function hasBatchActionsFor($className) {
        $hasBatchActions = false;
        if (array_key_exists($className, self::$batchActions) &&
                count(self::$batchActions[$className]) > 0) {
            $hasBatchActions = true;
        }
        return $hasBatchActions;
    }

    /**
     * Checks whether the DataObject with the given class name has  the given 
     * batch action.
     * 
     * @param string $className       Class name to check
     * @param string $batchActionName Batch action name to check
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public static function hasBatchActionFor($className, $batchActionName) {
        $hasBatchAction = false;
        if (array_key_exists($className, self::$batchActions) &&
            count(self::$batchActions[$className]) > 0 &&
            in_array($batchActionName, self::$batchActions[$className])) {
            $hasBatchAction = true;
        }
        return $hasBatchAction;
    }
    
    /**
     * Returns the batch actions for the given class name
     * 
     * @param string $className Class name to get batch actions for
     * 
     * @return array
     */
    public static function getBatchActionsFor($className) {
        $batchAction = array();
        if (self::hasBatchActionsFor($className)) {
            $batchAction = self::$batchActions[$className];
        }
        return $batchAction;
    }

}