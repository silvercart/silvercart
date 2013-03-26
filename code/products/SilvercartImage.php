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
 * DataObject to handle images added to a product or sth. else.
 * Provides additional (meta-)information about the image.
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 21.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartImage extends DataObject {

    /**
     * Has one relations
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartProduct'         => 'SilvercartProduct',
        'SilvercartPaymentMethod'   => 'SilvercartPaymentMethod',
        'Image'                     => 'Image',
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartImageLanguages' => 'SilvercartImageLanguage'
    );
    
    /**
     * Casted properties
     *
     * @var array
     */
    public static $casting = array(
        'Title'          => 'VarChar',
        'Content'        => 'HTMLText',
        'Description'    => 'HTMLText',
    );
    
    /**
     * Constructor. Overwrites some basic attributes.
     *
     * @param array $record      Record to fill Object with
     * @param bool  $isSingleton Is this a singleton?
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.06.2012
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        if ($this->Image()->exists()) {
            $this->Image()->Title = $this->Title;
        }
    }
    
    /**
     * getter for the Title, looks for set translation
     * 
     * @return string The Title from the translation object or an empty string
     */
    public function getTitle() {
        $title = $this->getLanguageFieldValue('Title');
        if ($this->SilvercartProduct()->ID &&
            empty($title)) {
            $title = $this->SilvercartProduct()->Title;
        }
        return $title;
    }
    
    /**
     * getter for the content, looks for set translation
     * 
     * @return string The content from the translation object or an empty string
     */
    public function getContent() {
        return $this->getLanguageFieldValue('Content');
    }
    
    /**
     * getter for the description, looks for set translation
     * 
     * @return string The description from the translation object or an empty string
     */
    public function getDescription() {
        return $this->getLanguageFieldValue('Description');
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.05.2012
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
     * @since 22.05.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        $fields->removeByName('SilvercartProductID');
        $fields->removeByName('SilvercartPaymentMethodID');
        
        $controller = Controller::curr();
        if ($controller instanceof SilvercartProductAdmin ||
            $controller instanceof SilvercartPaymentMethodAdmin) {
            $fields->removeByName('Content');
            $fields->removeByName('Description');
        }
        
        return $fields;
    }
    
    /**
     * wrapper that changes add behavior for better user experience
     * images may directly be added without pressing the save/add button
     *
     * @param array $params configuration parameters
     *
     * @return FieldList $fields field set for cms 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.07.2012
     * @deprecated should be removed before release
     */
    public function getCMSFieldsForContext($params = null) {
        /* @var $request SS_HTTPRequest */
        $request = Controller::curr()->getRequest();
        if (!$this->isInDB() &&
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
     * @since 08.02.2013
     * @deprecated should be removed before release
     */
    public function getCMSFieldsForProduct($params = null) {
        $fields = $this->getCMSFieldsForContext(
                        array_merge(
                                array(
                                    'restrictFields' => array(
                                        'SortOrder',
                                    ),
                                ),
                                (array) $params
                        )
        );
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }

        $fields->removeByName('Content');
        $fields->removeByName('Description');

        return $fields;
    }

    /**
     * Returns the CMS fields for the payment method context
     *
     * @param array $params Scaffolding params
     * 
     * @return FieldList
     * 
     * @deprecated should be removed before release
     */
    public function getCMSFieldsForPayment($params = null) {
        $fields = $this->getCMSFieldsForContext(
                        array_merge(
                                array(
                                    'restrictFields' => array(
                                        'SortOrder',
                                    ),
                                ),
                                (array) $params
                        )
        );
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->addFieldToTab('Root.Main', $languageField);
        }

        $fields->removeByName('Content');
        $fields->removeByName('Description');

        return $fields;
    }

    /**
     * Returns the CMS fields for the widget context
     *
     * @param array $params Scaffolding params
     * 
     * @return FieldList
     * @deprecated should be removed before release
     */
    public function getCMSFieldsForWidget($params = null) {
        $fields = $this->getCMSFieldsForContext(
            array_merge(
                array(
                    'restrictFields' => array(
                        'SortOrder',
                    ),
                ),
                (array) $params
            )
        );
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'SortOrder');
        }

        $fields->removeByName('SilvercartProductID');
        $fields->removeByName('SilvercartPaymentMethodID');
        $fields->removeByName('SortOrder');
        return $fields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.03.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'SilvercartImageLanguages'  => _t('SilvercartImageLanguage.PLURALNAME'),
                'SilvercartPaymentMethod'   => _t('SilvercartPaymentMethod.SINGULARNAME'),
                'SilvercartProduct'         => _t('SilvercartProduct.SINGULARNAME'),
                'Thumbnail'                 => _t('SilvercartImage.THUMBNAIL'),
                'Title'                     => _t('SilvercartImage.TITLE'),
                'Content'                   => _t('SilvercartImage.CONTENT'),
                'Description'               => _t('SilvercartImage.DESCRIPTION'),
                'SortOrder'                 => _t('Silvercart.SORTORDER'),
                'Image'                     => _t('Image.SINGULARNAME'),
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
     * @since 22.05.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Image.ImageThumbnail' => $this->fieldLabel('Thumbnail'),
            'Title'                => $this->fieldLabel('Title')
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Searchable fields definition
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 31.05.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'SilvercartImageLanguages.Title' => array(
                'title'  => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            )
        );
            
        return $searchableFields;
    }
    
    /**
     * Returns a HTML snippet for the related Files icon.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function getFileIcon() {
        return '<img src="' . $this->Image()->Icon() . '" alt="' . $this->Image()->FileType . '" title="' . $this->Image()->Title . '" />';
    }
    
    /**
     * Returns the products link
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function getProductLink() {
        $link = "";
        if ($this->SilvercartProductID) {
            $link = $this->SilvercartProduct()->Link();
        }
        return $link;
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
        if ($this->ImageID == 0 &&
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
        $image = $this->Image();

        if ($image &&
            $image->ID > 0) {
            $image->delete();
        }
    }
    
    /**
     * On before write hook.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if ($this->SilvercartProduct()->exists() &&
            empty($this->Title)) {
            $this->Title = $this->SilvercartProduct()->Title;
        } elseif ($this->Image()->exists() &&
            empty($this->Title)) {
            $this->Title = $this->Image()->Title;
        }
    }
    
}
