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
 * Extension of FileIFrameField to disable selecting files from file store
 *
 * @package Silvercart
 * @subpackage FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 21.06.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartFileIFrameField extends FileIFrameField {
    
    /**
     * Alternative title to use
     *
     * @var string
     */
    protected $forceTitle = '';

    /**
     * Flag that controls whether or not new files can be selected by the user 
     * out of the file store on the server
     * 
     * @var boolean
     */
    protected $canSelectFromFileStore = true;

    /** 
     * Sets whether or not files can be be selected by the user 
     * out of the file store on the server
     * 
     * @param boolean $can Can choose or not?
     * 
     * @return void
     */
    public function setCanSelectFromFileStore($can) {
        $this->canSelectFromFileStore = $can;
    }
    
    /**
     * Sets the forced title to use for the upload form
     *
     * @param string $forceTitle forced title to use for the upload form
     * 
     * @return void
     */
    public function setForceTitle($forceTitle) {
        $this->forceTitle = $forceTitle;
    }
    
    /**
     * Returns the forced title to use for the upload form
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2012
     */
    public function ForceTitle() {
        return $this->forceTitle;
    }

    /**
     * Returns the edit file form to display in iframe
     * 
     * @return Form
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2012
     */
    public function EditFileForm() {
        $uploadFile = _t('FileIFrameField.FROMCOMPUTER', 'From your Computer');
        $selectFile = _t('FileIFrameField.FROMFILESTORE', 'From the File Store');

        $title = $this->ForceTitle();
        if (empty($title)) {
            if ($this->AttachedFile() && $this->AttachedFile()->ID) {
                $title = sprintf(_t('FileIFrameField.REPLACE', 'Replace %s'), $this->FileTypeName());
            } else {
                $title = sprintf(_t('FileIFrameField.ATTACH', 'Attach %s'), $this->FileTypeName());
            }
        }

        $fileSources = array();

        if (singleton($this->dataClass())->canCreate()) {
            if ($this->canUploadNewFile) {
                $fileSources["new//$uploadFile"] = new FileField('Upload', '');
            }
        }
        if ($this->canSelectFromFileStore) {
            $fileSources["existing//$selectFile"] = new TreeDropdownField('ExistingFile', '', 'File');
        }
        
        if (!empty($fileSources)) {
            $fields = new FieldSet (
                    new HeaderField('EditFileHeader', $title),
                    new SelectionGroup('FileSource', $fileSources)
            );

            // locale needs to be passed through from the iframe source
            if (isset($_GET['locale'])) {
                $fields->push(new HiddenField('locale', '', $_GET['locale']));
            }

            $form = new Form(
                    $this,
                    'EditFileForm',
                    $fields,
                    new FieldSet(
                            new FormAction('save', $title)
                    )
            );
        } else {
            $form = new Form(
                    $this,
                    'EditFileForm'
            );
        }
        return $form;
    }

}
