<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverCart\Dev\Tools;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\View\Requirements;
use ReflectionClass;

/**
 * Component to add an action to display a records quick access view on mouseover.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_Components
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldQuickAccessController implements GridField_HTMLProvider, GridField_ColumnProvider {
    
    /**
     * Name of the handled column
     *
     * @var string
     */
    protected $columnName = 'QuickAccess';
    
    /***************************************************************************
     * GridField_HTMLProvider
     ***************************************************************************/
    
    /**
     * Returns a map where the keys are fragment names and the values are pieces of HTML to add to these fragments.
     *
     * Here are 4 built-in fragments: 'header', 'footer', 'before', and 'after', but components may also specify
     * fragments of their own.
     * 
     * To specify a new fragment, specify a new fragment by including the text "$DefineFragment(fragmentname)" in the
     * HTML that you return.  Fragment names should only contain alphanumerics, -, and _.
     *
     * If you attempt to return HTML for a fragment that doesn't exist, an exception will be thrown when the GridField
     * is rendered.
     * 
     * @param GridField $gridField GridField to get HTML for
     *
     * @return Array
     */
    public function getHTMLFragments($gridField) {
        Requirements::css('silvercart/silvercart:client/admin/css/GridFieldQuickAccessController.css');
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
     * @since 21.03.2013
     */
    public function augmentColumns($gridField, &$columns) {
        $columns = array_merge(
                array(
                    $this->columnName,
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
            $this->columnName,
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
        if ($columnName == $this->columnName) {
            $quickAccessData    = '';
            $quickAccessFields  = $record->getQuickAccessFields();
            if ($quickAccessFields instanceof FieldList) {
                $reflection = new ReflectionClass(static::class);
                $quickAccessData = $quickAccessFields->renderWith($reflection->getNamespaceName() . '\GridFieldQuickAccessFields');
            } else {
                $quickAccessData = $quickAccessFields;
            }
            return Tools::string2html('<span class="icon icon-24 icon-magnifier cursor-context-menu TriggerQuickAccess">' . $quickAccessData . '</span>');
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
        return array('class' => 'col-silvercart-quick-access');
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
                '<span class="icon icon-24 white icon-magnifier" title="%s"> </span>',
                _t(GridFieldQuickAccessController::class . '.QUICKACCESSLABEL', 'Quick access')
        );
        return array(
            'title' => $title,
        );
    }

}