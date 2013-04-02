<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.03.2013
 * @license see license file in modules root directory
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
