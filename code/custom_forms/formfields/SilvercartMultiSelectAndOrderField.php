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
 * pool of available items that can be transfered to the right field which
 * contains the selected items.
 * The selected items can be removed and ordered.
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 06.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartMultiSelectAndOrderField extends DropdownField {
    
    /**
     * Contains the exporter object
     * 
     * @var int
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    protected $exporterObj;
    
    /**
	 * Creates a new SilvercartMultiSelectAndOrder field.
	 * 
	 * @param string       $name   The field name
	 * @param string       $title  The field title
	 * @param array        $source An map of the dropdown items
	 * @param string|array $value  You can pass an array of values or a single value like a drop down to be selected
	 * @param int          $size   Optional size of the select element
	 * @param form         $form   The parent form
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
	 */
    function __construct($exporterId, $name, $title = '', $source = array(), $value = '', $size = null, $multiple = false, $form = null) {
        parent::__construct($name, $title, $source, $value, $form);
        
        if (!empty($exporterId)) {
            $this->exporterObj = DataObject::get_by_id(
                'SilvercartProductExporter',
                $exporterId
            );
        }
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
        $availableItems     = array();
        $selectedItems      = array();
        $templateVars       = array(
            'ID'                => $this->id(),
            'extraClass'        => $this->extraClass(),
            'available_items'   => array(),
            'selected_items'    => array()
        );
        
        if (!$this->exporterObj) {
            return $output;
        }
        
        // --------------------------------------------------------------------
        // Fill available field list
        // --------------------------------------------------------------------
        foreach ($source as $key => $value) {
            if (!$this->exporterObj->SilvercartProductExporterFields()->find('name', $value)) {
                $availableItems['item_'.$availableItemIdx] = new ArrayData(
                    array(
                        'value'             => $value,
                        'label'             => $value
                    )
                );
                $availableItemIdx++;
            }
        }
        
        // --------------------------------------------------------------------
        // Fill selected field list
        // --------------------------------------------------------------------
        foreach ($this->exporterObj->SilvercartProductExporterFields() as $exporterField) {
            $selectedItems['item_'.$selectedItemIdx] = new ArrayData(
                array(
                    'value' => $exporterField->name,
                    'label' => $exporterField->name
                )
            );
            $selectedItemIdx++;
        }
        
        $templateVars['available_items'] = new DataObjectSet($availableItems);
        $templateVars['selected_items']  = new DataObjectSet($selectedItems);
        $output                          = $this->customise($templateVars)->renderWith('SilvercartMultiSelectAndOrderField');

        return $output;
    }
}
