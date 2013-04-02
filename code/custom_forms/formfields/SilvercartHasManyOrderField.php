<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage FormFields
 */

/**
 * A formfield that displays two multiselect boxes. The left one contains a
 * pool of available items from the given DataObject ($sourceClass) that can be
 * transfered to the right field which contains the selected items. Those are
 * related to the given DataObject via a relation ($relationName).
 * The selected items can be removed and ordered.
 * 
 * You have to follow a naming convention for this field to work. If the
 * DataObject is called "MyDataObject" the relation to it's fields has to be
 * named "MyDataObjectFields"; the relation object's name must be
 * "MyDataObjectField".
 * 
 * For the actions (move up, move down, attribute, remove, etc) to work you
 * have to register this classes' recordController:
 * 
 * Register your record_controller in your ModelAdmin:
 * 
 * public static $managed_models = array(
 *     'MyDataObject' => array(
 *         'record_controller' => 'MyAdmin_RecordController'
 *     )
 * );
 * 
 * Extend the RecordController class with your ModelAdmin_RecordController
 * class that handles the DataObject in the storeadmin:
 * 
 * class MyAdmin_RecordController extends SilvercartHasManyOrderField_RecordController {
 *     ...
 * }
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 06.07.2011
 * @license see license file in modules root directory
 * @deprecated since version 2.0 class is incompatible; functionallity no longer needed
 */
class SilvercartHasManyOrderField extends DropdownField {
    
    /**
     * Contains the data object to operate on
     * 
     * @var DataObject
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    protected $dataObj;
    
    /**
     * Contains the name of the relation.
     * 
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 13.08.2011
     */
    protected $relationName;
    
    /**
     * Creates a new SilvercartHasManyOrder field.
     * 
     * @param DataObject   $sourceClass  The source class object
     * @param string       $relationName The name of the relation
     * @param string       $name         The field name
     * @param string       $title        The field title
     * @param array        $source       An map of the dropdown items
     * @param string|array $value        You can pass an array of values or a single value like a drop down to be selected
     * @param int          $size         Optional size of the select element
     * @param boolean      $multiple     Indicates wether multiple entries can be selected
     * @param form         $form         The parent form
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function __construct($sourceClass, $relationName, $name, $title = '', $source = array(), $value = '', $size = null, $multiple = false, $form = null) {
        parent::__construct($name, $title, $source, $value, $form);
        
        $this->dataObj      = $sourceClass;
        $this->relationName = $relationName;
    }
    
    /**
     * Returns the select field.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public function FieldHolder() {
        $source             = $this->getSource();
        $availableItemIdx   = 0;
        $selectedItemIdx    = 0;
        $output             = '';
        $relationName       = $this->relationName;
        $availableItems     = array();
        $selectedItems      = array();
        $templateVars       = array(
            'ID'                => $this->id(),
            'extraClass'        => $this->extraClass(),
            'available_items'   => array(),
            'selected_items'    => array(),
            'relationName'      => $relationName,
            'AbsUrl'            => Director::absoluteBaseURL()
        );
        
        if (!$this->dataObj) {
            return $output;
        }
        
        // --------------------------------------------------------------------
        // Fill available field list
        // --------------------------------------------------------------------
        if (is_array($source)) {
            foreach ($source as $item) {
                $availableItems['item_'.$availableItemIdx] = new ArrayData(
                    array(
                        'value'             => $item[0],
                        'label'             => $item[1]
                    )
                );
                $availableItemIdx++;
            }
        }
        
        // --------------------------------------------------------------------
        // Fill selected field list
        // --------------------------------------------------------------------
        if ($this->dataObj) {
            $selectedRelationFields = $this->dataObj->$relationName();
            $selectedRelationFields->sort('Sort', 'ASC');

            foreach ($selectedRelationFields as $selectedRelationField) {
                if ($selectedRelationField->ClassName == 'SilvercartWidget') {
                    continue;
                }
                $selectedItems['item_'.$selectedItemIdx] = new ArrayData(
                    array(
                        'value' => $selectedRelationField->ID,
                        'label' => $selectedRelationField->Title()
                    )
                );
                $selectedItemIdx++;
            }

            $templateVars['available_items'] = new ArrayList($availableItems);
            $templateVars['selected_items']  = new ArrayList($selectedItems);
            $output                          = $this->customise($templateVars)->renderWith('SilvercartHasManyOrderField');
        }
        
        return $output;
    }
}