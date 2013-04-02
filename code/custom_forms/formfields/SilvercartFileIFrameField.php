<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 */

/**
 * Extension of FileIFrameField to disable selecting files from file store
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 21.06.2012
 * @license see license file in modules root directory
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
