<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * This class fixes the belongs_many_many resolution of the cores default
 * ManyManyComplexTableField.
 * 
 * @package Silvercart
 * @subpackage FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 29.03.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartManyManyComplexTableField extends HasManyComplexTableField {

    /**
     * Many to many related parent class
     * 
     * @var string 
     */
    private $manyManyParentClass;

    /**
     * Class to use to handle the fields items
     *
     * @var string
     */
    public $itemClass = 'ManyManyComplexTableField_Item';
    
    /**
     * Fixes the belongs_many_many resolution of the cores default
     * ManyManyComplexTableField.
     *
     * @param DataObject $controller       Controller
     * @param string     $name             Name of the field / relation
     * @param string     $sourceClass      Name of the Source class
     * @param array      $fieldList        Fieldlist
     * @param string     $detailFormFields Method to call to get CMS fields for
     * @param string     $sourceFilter     Source filter
     * @param string     $sourceSort       Sort
     * @param string     $sourceJoin       Join
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2012
     */
    public function __construct($controller, $name, $sourceClass, $fieldList = null, $detailFormFields = null, $sourceFilter = "", $sourceSort = "", $sourceJoin = "") {
        parent::__construct($controller, $name, $sourceClass, $fieldList, $detailFormFields, $sourceFilter, $sourceSort, $sourceJoin);

        $classes = array_reverse(ClassInfo::ancestry($this->controllerClass()));
        foreach ($classes as $class) {
            $singleton = singleton($class);
            $manyManyRelations = $singleton->uninherited('many_many', true);
            if (isset($manyManyRelations) && array_key_exists($this->name, $manyManyRelations)) {
                $this->manyManyParentClass = $class;
                $manyManyTable = $class . '_' . $this->name;
                break;
            }
            $belongsManyManyRelations = $singleton->uninherited( 'belongs_many_many', true );
            if (isset( $belongsManyManyRelations ) && array_key_exists( $this->name, $belongsManyManyRelations ) ) {
                $this->manyManyParentClass  = $class;
                $manyManySingleton          = singleton($belongsManyManyRelations[$this->name]);
                $manyManyRelations          = $manyManySingleton->uninherited('many_many', true);
                $flippedManyManyRelations   = array_flip($manyManyRelations);
                foreach ($classes as $class) {
                    if (isset($manyManyRelations) && array_key_exists($class, $flippedManyManyRelations)) {
                        $manyManyTable = $belongsManyManyRelations[$this->name] . '_' . $flippedManyManyRelations[$class];
                        break 2;
                    }
                }
            }
        }
        $tableClasses = ClassInfo::dataClassesFor($this->sourceClass);
        $source = array_shift($tableClasses);
        $sourceField = $this->sourceClass;
        if ($this->manyManyParentClass == $sourceField) {
            $sourceField = 'Child';
        }
        $parentID = $this->controller->ID;

        $this->sourceJoin .= " LEFT JOIN \"$manyManyTable\" ON (\"$source\".\"ID\" = \"$manyManyTable\".\"{$sourceField}ID\" AND \"{$this->manyManyParentClass}ID\" = '$parentID')";

        $this->joinField = 'Checked';
    }

    /**
     * Returns the Query to use
     *
     * @return SQLQuery
     */
    public function getQuery() {
        $query = parent::getQuery();
        $query->select[]    = "CASE WHEN \"{$this->manyManyParentClass}ID\" IS NULL THEN '0' ELSE '1' END AS \"Checked\"";
        $query->groupby[]   = "\"{$this->manyManyParentClass}ID\"";

        return $query;
    }

    /**
     * Returns the name of the relations ID field by the given parent and child
     * class
     *
     * @param string $parentClass Parent class
     * @param string $childClass  Child class
     * 
     * @return string
     */
    public function getParentIdName($parentClass, $childClass) {
        return $this->getParentIdNameRelation($parentClass, $childClass, 'many_many');
    }
    
    /**
     * Some extra HTML
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.05.2012
     */
    public function ExtraData() {
        $extraData = parent::ExtraData();
        $extraData .= sprintf(
                '<a href="javascript:;" class="mark-all" rel="%s">%s / %s</a>',
                $this->id(),
                _t('Silvercart.MARK_ALL', 'mark all'),
                _t('Silvercart.UNMARK_ALL', 'mark all')
        );
        return $extraData;
    }
}