<?php
/**
 * Copyright 2014 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage API
 */

/**
 * Adds a new API base
 *
 * @package Silvercart
 * @subpackage API
 * @author Sebastian Diel <sdiel@pixeltricks.de>,
 *         Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.06.2014
 * @copyright 2014 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartRestfulXMLDataFormatter extends SilvercartXMLDataFormatter {

    /**
     * The API base
     *
     * @var string
     */
    public static $api_base = 'api/silvercart/';

    /**
     * Builds the XML data.
     *
     * SilvercartPaymentMethod relations get treated specially here.
     *
     * @param DataObject $obj       Object to build XML data for
     * @param array      $fields    Fields to build XML data for
     * @param array      $relations Relations to support
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.06.2014
     */
    public function convertDataObjectWithoutHeader(DataObject $obj, $fields = null, $relations = null) {
        $className  = $obj->class;
        $id         = $obj->ID;
        $objHref    = Director::absoluteURL(static::$api_base . $obj->class . "/" . $obj->ID);

        if (substr($obj->ClassName, 0, 17) == 'SilvercartPayment') {
            $relClassName = 'SilvercartPaymentMethod';
        } else {
            $relClassName = $obj->ClassName;
        }

        $xml        = "<$relClassName href=\"$objHref.xml\">\n";

        $this->getDataObjectFieldPermissions($obj);
        $fields = array_intersect((array) $this->getCustomAddFields(), (array) $this->getCustomFields());

        foreach ($this->getFieldsForObj($obj) as $fieldName => $fieldType) {
            // Field filtering
            if (SilvercartRestfulServer::isBlackListField($obj->class, $fieldName)) {
                continue;
            }
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
                        $customAddFields = Config::inst()->get($relObj->ClassName, 'custom_add_export_fields');
                        $this->setCustomAddFields((array) $customAddFields);
                        $xml .= $this->convertDataObjectWithoutHeader($relObj, $fields);
                        $this->setCustomAddFields($originalCustomAddFields);

                        $this->setRelationDepth($relationDepth);
                    } else {
                        $href = Director::absoluteURL(static::$api_base . "$relClass/" . $obj->$fieldName);
                    }
                } else {
                    $href = Director::absoluteURL(static::$api_base . "$className/$id/$relName");
                }
                if (!empty($href)) {
                    $xml .= "<$relName linktype=\"has_one\" href=\"$href.xml\" id=\"" . $obj->$fieldName . "\"></$relName>\n";
                }
            }
            foreach ($obj->has_many() as $relName => $relClass) {
                if ($this->skipRelation($relName, $relClass, $fields)) {
                    continue;
                }
                $xml .= $this->addMany($relName, $relClass, $objHref, $obj, 'has_many', $fields);
            }
            foreach ($obj->many_many() as $relName => $relClass) {
                if ($this->skipRelation($relName, $relClass, $fields)) {
                    continue;
                }
                $xml .= $this->addMany($relName, $relClass, $objHref, $obj, 'many_many', $fields);
            }
        }

        $xml .= "</$relClassName>\n";

        return $xml;
    }
}