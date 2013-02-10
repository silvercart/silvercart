<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @subpackage Products
 */

/**
 * DataObject to handle files added to a product or sth. else.
 * Provides additional (meta-)information about the file.
 * It's used to add PDF datasheets or other files.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 21.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartFile extends DataObject {

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartProduct'         => 'SilvercartProduct',
        'File'                      => 'File',
        'SilvercartDownloadPage'    => 'SilvercartDownloadPage',
    );
    
    /**
     * Castings
     *
     * @var array
     */
    public static $casting = array(
        'Title'             => 'VarChar',
        'Description'       => 'HTMLText',
        'FileIcon'          => 'HTMLText',
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartFileLanguages' => 'SilvercartFileLanguage'
    );
    
    /**
     * getter for the Title, looks for set translation
     * 
     * @return string The Title from the translation object or an empty string
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
    /**
     * getter for the Description, looks for set translation
     * 
     * @return string The description from the translation object or an empty string
     */
    public function getDescription() {
        return $this->getLanguageFieldValue('Description');
    }

    /**
     * Returns a HTML snippet for the related Files icon.
     *
     * @return string
     */
    public function getFileIcon() {
        return '<img src="' . $this->File()->Icon() . '" alt="' . $this->File()->FileType . '" title="' . $this->File()->Title . '" />';
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.03.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Title'                     => _t('SilvercartFile.TITLE'),
                'FileIcon'                  => _t('SilvercartFile.FILEICON'),
                'SilvercartFileLanguages'   => _t('SilvercartFileLanguage.PLURALNAME'),
                'SilvercartProduct'         => _t('SilvercartProduct.SINGULARNAME'),
                'File'                      => _t('File.SINGULARNAME'),
                'SilvercartDownloadPage'    => _t('SilvercartDownloadPage.SINGULARNAME'),
                'Description'               => _t('SilvercartFile.DESCRIPTION'),
                
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'FileIcon'       => $this->fieldLabel('FileIcon'),
            'Title'          => $this->fieldLabel('Title'),
        );


        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }
        return $fields;
    }
    
    /**
     * saves the value of the field LongDescription correctly into HTMLText
     * 
     * @param string $value the field value
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.01.2012
     */
    public function saveDescription($value) {
        $languageObj = $this->getLanguage();
        $languageObj->Description = $value;
        $languageObj->write();
    }
    
    /**
     * wrapper that changes image upload workflow for better user experience
     * images may directly be added without pressing the save/add button of the
     * context object
     *
     * @param array $params configuration parameters
     *
     * @return FieldList $fields field set for cms 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.07.2012
     */
    public function getCMSFieldsForContext($params = null) {
        /* @var $request SS_HTTPRequest */
        $request = Controller::curr()->getRequest();
        if ($this->ID == 0 &&
            $request->param('Action') == 'add') {
            $this->write();
            $editURL = str_replace('/add', '/item/' . $this->ID . '/edit', $request->getURL());
            Controller::curr()->redirect($editURL);
        }
        $fields = parent::getCMSFields($params);
        return $fields;
    }

    /**
     * Returns the CMS fields for the product context
     *
     * @param array $params Scaffolding params
     * 
     * @return FieldList $fields field set for cms
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.07.2012
     */
    public function getCMSFieldsForProduct($params = null) {
        $fields = $this->getCMSFieldsForContext($params);
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }
        return $fields;
    }
    
    
    
    /**
     * customizes the backends fields for file upload on a SilvercartDownloadPage
     * 
     * @param array $params configuration array
     *
     * @return FieldList the fields for the backend
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFieldsForDownloadPage($params = null) {
        
        $fields = $this->getCMSFieldsForContext($params);
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }
        return $fields;
    }
    
    /**
     * Was the object just accidently written?
     * object without attribute or file appended
     *
     * @return bool $result
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 14.07.2012
     */
    public function isEmptyObject() {
        $result = false;
        if ($this->FileID == 0 &&
            $this->isEmptyMultilingualAttributes()) {
            $result = true;
        }
        return $result;
    }
    
    /**
     * hook
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.07.2012
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();
        if ($this->File()) {
            $this->File()->delete();
        }
    }
}
