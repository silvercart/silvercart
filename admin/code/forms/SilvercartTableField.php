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
 * Field to show some DataObjects in a simple table.
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 22.03.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTableField extends FormField {

    /**
     * Items to use in template
     *
     * @var ArrayList
     */
    protected $items = null;
    
    /**
     * DataList to use for table
     *
     * @var SS_List
     */
    protected $dataList = null;
    
    /**
     * List of fields to use
     *
     * @var Array
     */
    protected $fieldList = null;

    /**
     * Constructor
     * 
     * @param string  $name      Name of the field
     * @param string  $title     Title of the field
     * @param SS_List $dataList  DataList to use
     * @param array   $fieldList List of fields to use
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.03.2013
     */
    public function __construct($name, $title, SS_List $dataList, $fieldList = null) {
        parent::__construct($name, $title);
        $this->setTemplate('SilvercartTableField');
        $this->setDataList($dataList);
        $this->setFieldList(($fieldList) ? $fieldList : singleton($dataList->dataClass())->summaryFields());
    }
    
    /**
     * Returns the DataList to use
     * 
     * @return SS_List
     */
    public function getDataList() {
        return $this->dataList;
    }

    /**
     * Sets the DataList to use
     * 
     * @param SS_List $dataList DataList to use
     * 
     * @return void
     */
    public function setDataList($dataList) {
        $this->dataList = $dataList;
    }
    
    /**
     * Returns the list of fields to use
     * 
     * @return array
     */
    public function getFieldList() {
        return $this->fieldList;
    }

    /**
     * Sets the list of fields to use
     * 
     * @param array $fieldList List of fields to use
     * 
     * @return void
     */
    public function setFieldList($fieldList) {
        $this->fieldList = $fieldList;
    }
    
    /**
     * Returns the items to use in template
     * 
     * @return ArrayList
     */
    public function getItems() {
        if (is_null($this->items)) {
            $this->initItems();
        }
        return $this->items;
    }

    /**
     * Sets the items to use in template
     * 
     * @param ArrayList $items Items to use in template
     * 
     * @return void
     */
    public function setItems($items) {
        $this->items = $items;
    }
    
    /**
     * Initializes the items to use in template
     * 
     * @return ArrayList
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.03.2013
     */
    public function initItems() {
        $this->items = new ArrayList();
        foreach ($this->getDataList() as $item) {
            $arrayItem = array(
                'Columns' => new ArrayList(),
            );
            foreach ($this->getFieldList() as $field => $fieldLabel) {
                if (strpos($field, '.') !== false) {
                    $parts  = explode('.', $field);
                    $value  = $item;
                    $index  = 1;
                    foreach ($parts as $part) {
                        if ($index == count($parts)) {
                            $value = $value->{$part};
                        } else {
                            $value = $value->{$part}();
                        }
                        $index++;
                    }
                } else {
                    $value = $item->{$field};
                }
                $arrayItem['Columns']->add(
                        new ArrayData(
                                array(
                                    'Value' => $value,
                                    'Title' => $fieldLabel,
                                    'Type'  => $field,
                                )
                        )
                );
            }
            $this->items->add(new ArrayData($arrayItem));
        }
        
        return $this->items;
    }

}