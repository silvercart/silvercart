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
 * @subpackage Model
 */

/**
 * Extension for every DataObject
 *
 * @package Silvercart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 29.10.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDataObject extends DataExtension {
    
    /**
     * Returns a quick preview to use in a related models admin form
     * 
     * @return string
     */
    public function getAdminQuickPreview() {
        return $this->owner->renderWith($this->owner->ClassName . 'AdminQuickPreview');
    }
    
    /**
     * Returns the record as a array map with non escaped values
     * 
     * @param bool $toDisplayWithinHtml Set this to true to replace html special chars with its entities
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function toRawMap($toDisplayWithinHtml = false) {
        $record = $this->owner->toMap();
        $rawMap = array();
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
     * Clone of DataObject::getCMSFields() with some additional SilverCart
     * related features.
     * <ul>
     *  <li>Restricted fields can be updated by DataExtension (updateRestrictCMSFields).</li>
     *  <li>Translation fields of DataObjects with SilverCart based translation model will be scaffolded.</li>
     * </ul>
     * 
     * @param DataObject $dataObject                     DataObject to get CMS fields for
     * @param string     $neighbourFieldOfLanguageFields Name of the field to insert language fields after or before
     * @param bool       $insertLangugeFieldsAfter       Determines whether to add language fields before or after the given neighbour field
     * 
     * @return FieldList
     */
    public static function getCMSFields(DataObject $dataObject, $neighbourFieldOfLanguageFields = null, $insertLangugeFieldsAfter = true) {
        $params = array(
            'includeRelations'  => $dataObject->isInDB(),
            'tabbed'            => true,
            'ajaxSafe'          => true,
        );
        $restrictFields = array();
        $dataObject->extend('updateRestrictCMSFields', $restrictFields);
        if (!empty($restrictFields)) {
            $params['restrictFields'] = $restrictFields;
        }

        $tabbedFields = self::scaffoldFormFields($dataObject, $params);
        
        if ($dataObject->has_extension($dataObject->class, 'SilvercartDataObjectMultilingualDecorator')) {
            $languageFields = SilvercartLanguageHelper::prepareCMSFields($dataObject->getLanguageClassName());
            foreach ($languageFields as $languageField) {
                if (!is_null($neighbourFieldOfLanguageFields)) {
                    if ($insertLangugeFieldsAfter) {
                        $tabbedFields->insertAfter($languageField, $neighbourFieldOfLanguageFields);
                    } else {
                        $tabbedFields->insertBefore($languageField, $neighbourFieldOfLanguageFields);
                    }
                } else {
                    $tabbedFields->addFieldToTab('Root.Main', $languageField);
                }
            }
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
     * @uses SilvercartFormScaffolder
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public static function scaffoldFormFields(DataObject $dataObject, $_params = null) {
        $params = array_merge(
                array(
                    'tabbed' => false,
                    'includeRelations' => false,
                    'restrictFields' => false,
                    'fieldClasses' => false,
                    'ajaxSafe' => false
                ),
                (array) $_params
        );

        $fs = new SilvercartFormScaffolder($dataObject);
        $fs->tabbed             = $params['tabbed'];
        $fs->includeRelations   = $params['includeRelations'];
        $fs->restrictFields     = $params['restrictFields'];
        $fs->fieldClasses       = $params['fieldClasses'];
        $fs->ajaxSafe           = $params['ajaxSafe'];

        return $fs->getFieldList();
    }

}