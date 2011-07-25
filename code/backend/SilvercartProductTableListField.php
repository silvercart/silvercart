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
 * @subpackage Backend
 */

/**
 * A modified TableListField for the SilverCart product export manager.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 25.07.2011
 * @copyright 2011 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductTableListField extends TableListField {
    
    /**
     * We don't want the HTML tags in some fields to be replaced by
     * Silverstripe's automatic mechanism.
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.07.2011
     */
    public $csvFieldFormatting = array(
        "Title"             => '$Title',
        "ShortDescription"  => '$ShortDescription',
        "LongDescription"   => '$LongDescription',
        "MetaTitle"         => '$MetaTitle',
        "MetaDescription"   => '$MetaDescription',
    );
    
    /**
     * We have to replace some field contents here to gain real CSV
     * compatibility.
     *
     * @return void
     *
     * @param 
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.07.2011
     */
    function generateExportFileData(&$numColumns, &$numRows) {
        $separator = $this->csvSeparator;
        $csvColumns = ($this->fieldListCsv) ? $this->fieldListCsv : $this->fieldList;
        $fileData = '';
        $columnData = array();
        $fieldItems = new DataObjectSet();

        if($this->csvHasHeader) {
            $fileData .= "\"" . implode("\"{$separator}\"", array_values($csvColumns)) . "\"";
            $fileData .= "\n";
        }

        if(isset($this->customSourceItems)) {
            $items = $this->customSourceItems;
        } else {
            $dataQuery = $this->getCsvQuery();
            $items = $dataQuery->execute();
        }

        // temporary override to adjust TableListField_Item behaviour
        $this->setFieldFormatting(array());
        $this->fieldList = $csvColumns;

        if($items) {
            foreach($items as $item) {
                if(is_array($item)) {
                    $className = isset($item['RecordClassName']) ? $item['RecordClassName'] : $item['ClassName'];
                    $item = new $className($item);
                }
                $fieldItem = new $this->itemClass($item, $this);

                $fields = $fieldItem->Fields(false);
                $columnData = array();
                if($fields) foreach($fields as $field) {
                    $value = $field->Value;

                    // TODO This should be replaced with casting
                    if(array_key_exists($field->Name, $this->csvFieldFormatting)) {
                        $format = str_replace('$value', "__VAL__", $this->csvFieldFormatting[$field->Name]);
                        $format = preg_replace('/\$([A-Za-z0-9-_]+)/','$item->$1', $format);
                        $format = str_replace('__VAL__', '$value', $format);
                        eval('$value = "' . $format . '";');
                    }

                    $value = str_replace(
                        array(
                            "\n",
                        ),
                        array(
                            "<br />",
                        ),
                        $value
                    );

                    $value = str_replace(array("\r", "\n"), "\n", $value);
                    
                    $tmpColumnData = '"' . str_replace('"', '""', $value) . '"';
                    $columnData[] = $tmpColumnData;
                }
                $fileData .= implode($separator, $columnData);
                $fileData .= "\n";

                $item->destroy();
                unset($item);
                unset($fieldItem);
            }

            $numColumns = count($columnData);
            $numRows = $fieldItems->count();
            return $fileData;
        } else {
            return null;
        }
    }
}
