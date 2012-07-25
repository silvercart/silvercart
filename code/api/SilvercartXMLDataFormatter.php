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
 * @subpackage API
 */

/**
 * Extended XML data formatter
 *
 * @package Silvercart
 * @subpackage API
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 13.07.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartXMLDataFormatter extends XMLDataFormatter {
    
    /**
     * Relation depth to show detailed XML data
     *
     * @var int
     */
    protected $relationDetailDepth = 0;
    
    /**
     * Returns the relation depth
     *
     * @return int
     */
    public function getRelationDepth() {
        return $this->relationDepth;
    }

    /**
     * Sets the relation depth
     *
     * @param int $relationDepth Relation depth
     * 
     * @return void
     */
    public function setRelationDepth($relationDepth) {
        $this->relationDepth = $relationDepth;
    }
    
    /**
     * Returns the relation detail depth
     *
     * @return int
     */
    public function getRelationDetailDepth() {
        return $this->relationDetailDepth;
    }
    
    /**
     * Sets the relation detail depth
     *
     * @param int $relationDetailDepth Relation detail depth
     * 
     * @return void
     */
    public function setRelationDetailDepth($relationDetailDepth) {
        $this->relationDetailDepth = $relationDetailDepth;
    }
    
    /**
     * Builds the XML data
     *
     * @param DataObject $obj    Object to build XML data for
     * @param array      $fields Fields to build XML data for
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function convertDataObjectWithoutHeader(DataObject $obj, $fields = null) {
        $className  = $obj->class;
        $id         = $obj->ID;
        $objHref    = Director::absoluteURL(self::$api_base . $obj->class . "/" . $obj->ID);
        $xml        = "<$className href=\"$objHref.xml\">\n";
        foreach ($this->getFieldsForObj($obj) as $fieldName => $fieldType) {
            // Field filtering
            if ($fields && !in_array($fieldName, $fields)) {
                continue;
            }

            $fieldValue = $obj->$fieldName;
            if (!mb_check_encoding($fieldValue,'utf-8')) {
                $fieldValue = "(data is badly encoded)";
            }

            if (is_object($fieldValue) &&
                is_subclass_of($fieldValue, 'Object') &&
                $fieldValue->hasMethod('toXML')) {
                $xml .= $fieldValue->toXML();
            } else {
                $xml .= "<$fieldName>" . Convert::raw2xml($fieldValue) . "</$fieldName>\n";
            }
        }

        if ($this->getRelationDepth() > 0) {
            foreach ($obj->has_one() as $relName => $relClass) {
                if ($this->skipRelation($relName, $relClass, $fields)) {
                    continue;
                }
                $fieldName  = $relName . 'ID';
                $href       = '';
                if ($obj->$fieldName) {
                    $relObj = null;
                    if ($this->getRelationDetailDepth() > 0) {
                        $relObj = DataObject::get_by_id($relClass, $obj->$fieldName);
                    }
                    if ($relObj) {
                        $relationDepth = $this->getRelationDepth();
                        $this->setRelationDepth($relationDepth - 1);
                        
                        $originalCustomAddFields = $this->getCustomAddFields();
                        $customAddFields = Object::get_static($relObj->ClassName, 'custom_add_export_fields');
                        $this->setCustomAddFields((array) $customAddFields);
                        $xml .= $this->convertDataObjectWithoutHeader($relObj);
                        $this->setCustomAddFields($originalCustomAddFields);
                        
                        $this->setRelationDepth($relationDepth);
                    } else {
                        $href = Director::absoluteURL(self::$api_base . "$relClass/" . $obj->$fieldName);
                    }
                } else {
                    $href = Director::absoluteURL(self::$api_base . "$className/$id/$relName");
                }
                if (!empty($href)) {
                    $xml .= "<$relName linktype=\"has_one\" href=\"$href.xml\" id=\"" . $obj->$fieldName . "\"></$relName>\n";
                }
            }
            foreach ($obj->has_many() as $relName => $relClass) {
                if ($this->skipRelation($relName, $relClass, $fields)) {
                    continue;
                }
                $xml .= $this->addMany($relName, $relClass, $objHref, $obj);
            }
            foreach ($obj->many_many() as $relName => $relClass) {
                if ($this->skipRelation($relName, $relClass, $fields)) {
                    continue;
                }
                $xml .= $this->addMany($relName, $relClass, $objHref, $obj, 'many_many');
            }
        }

        $xml .= "</$className>";

        return $xml;
    }
    
    /**
     * Adds the xml part for a has_many or many_many relation
     *
     * @param string     $relName  Relation name
     * @param string     $relClass Relation class name
     * @param string     $objHref  Link to the object
     * @param DataObject $obj      DataObject to get relation data for
     * @param string     $relType  Relation type (has_many/many_many)
     * 
     * @return string 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function addMany($relName, $relClass, $objHref, $obj, $relType = 'has_many') {
        $xmlPart    = "<$relName linktype=\"$relType\" href=\"$objHref/$relName.xml\">\n";
        $items      = $obj->$relName();
        if ($items) {
            foreach ($items as $item) {
                if ($this->getRelationDetailDepth() > 0) {
                    $relationDepth = $this->getRelationDepth();
                    $this->setRelationDepth($relationDepth - 1);
                    $xmlPart .= $this->convertDataObjectWithoutHeader($item);
                    $this->setRelationDepth($relationDepth);
                } else {
                    $href       = Director::absoluteURL(self::$api_base . "$relClass/$item->ID");
                    $xmlPart    .= "<$relClass href=\"$href.xml\" id=\"{$item->ID}\"></$relClass>\n";
                }
            }
        }
        $xmlPart .= "</$relName>\n";
        return $xmlPart;
    }
    
    /**
     * Checks whether to skip the XML creation for a relation oor not
     *
     * @param string $relName  Relation name
     * @param string $relClass Relation class name
     * @param array  $fields   Field list for the XML data
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function skipRelation($relName, $relClass, $fields) {
        $skipRelation = false;
        if (!singleton($relClass)->stat('api_access')) {
            $skipRelation = true;
        }
        // Field filtering
        if ($fields &&
            !in_array($relName, $fields)) {
            $skipRelation = true;
        }
        if ($this->customRelations &&
            !in_array($relName, $this->customRelations)) {
            $skipRelation = true;
        }
        return $skipRelation;
    }
}