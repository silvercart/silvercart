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
 * @subpackage Forms_Fields
 */

/**
 * Special upload field which creates a bridge object SilvercartFile to attach 
 * the uploaded file to. The newly created SilvercartFile will be attached to 
 * the handled record.
 *
 * @package Silvercart
 * @subpackage Forms_Fields
 * @copyright pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.03.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartFileUploadField extends UploadField {
    
    /**
     * Class name of the file object
     *
     * @var string
     */
    protected $fileClassName = 'File';
    
    /**
     * Class name of the relation object
     *
     * @var string
     */
    protected $relationClassName = 'SilvercartFile';

    /**
     * Returns the file class name
     * 
     * @return string
     */
    public function getFileClassName() {
        return $this->fileClassName;
    }

    /**
     * Sets the file class name
     * 
     * @param string $fileClassName Class name of the file object
     * 
     * @return void
     */
    public function setFileClassName($fileClassName) {
        $this->fileClassName = $fileClassName;
    }

    /**
     * Returns the relation class name
     * 
     * @return string
     */
    public function getRelationClassName() {
        return $this->relationClassName;
    }

    /**
     * Sets the relation class name
     * 
     * @param string $relationClassName Class name of the relation object
     * 
     * @return void
     */
    public function setRelationClassName($relationClassName) {
        $this->relationClassName = $relationClassName;
    }
    
    /**
     * Adds a JS requirement and returns the field markup.
     * 
     * @param array $properties key value pairs of template variables
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    public function Field($properties = array()) {
        Requirements::javascript(SilvercartTools::getBaseURLSegment() . 'silvercart/admin/javascript/SilvercartFileUploadField.js');
        return parent::Field($properties);
    }

    /**
     * Adds a SilvercartFile and attaches the SilvercartFile onto
     * $this->record.
     * 
     * @param File $file File to attach
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    protected function attachFile($file) {
        $record             = $this->getRecord();
        $name               = $this->getName();
        $relationName       = str_replace('Upload', '', $name);
        $relationClassName  = $this->getRelationClassName();
        $fileClassName      = $this->getFileClassName();
        
        if ($record && $record->exists()) {
            
            $silvercartFile = new $relationClassName();
            $silvercartFile->{$fileClassName . 'ID'} = $file->ID;
            $silvercartFile->write();
            
            if ($record->has_many($relationName) || $record->many_many($relationName)) {
                if (!$record->isInDB()) {
                    $record->write();
                }
                $record->{$relationName}()->add($silvercartFile);
            } elseif ($record->has_one($relationName)) {
                $record->{$relationName . 'ID'} = $silvercartFile->ID;
                $record->write();
            }
        }
    }

}
