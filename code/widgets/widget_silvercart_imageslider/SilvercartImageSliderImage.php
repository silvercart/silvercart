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
 * @subpackage Widgets
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
class SilvercartImageSliderImage extends DataObject {
    
    /**
     * DB properties
     *
     * @var array
     */
    public static $db = array(
        'ProductNumberToReference'  => 'Varchar(128)',
    );
    
    /**
     * Casted properties
     *
     * @var array
     */
    public static $casting = array(
        'Title'             => 'VarChar',
        'Content'           => 'HTMLText',
        'AltText'           => 'Varchar',
        'TableIndicator'    => 'Text',
        'Thumbnail'         => 'HTMLText',
    );
    
    /**
     * Has-one relationships.
     * 
     * @var array
     */
    public static $has_one = array(
        'Image'     => 'Image',
        'SiteTree'  => 'SiteTree'
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartImageSliderImageLanguages' => 'SilvercartImageSliderImageLanguage'
    );
    
    /**
     * Belongs-many-many relationships.
     * 
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartImageSliderWidgets' => 'SilvercartImageSliderWidget'
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
     * getter for the content, looks for set translation
     * 
     * @return string The content from the translation object or an empty string
     */
    public function getContent() {
        return $this->getLanguageFieldValue('Content');
    }
    
    /**
     * getter for the AltText, looks for set translation
     * 
     * @return string
     */
    public function getAltText() {
        $altText = $this->getLanguageFieldValue('AltText');
        if (empty($altText)) {
            $altText = $this->Title;
        }
        return $altText;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $siteTreeField = new TreeDropdownField(
            'SiteTreeID',
            $this->fieldLabel('Linkpage'),
            'SiteTree',
            'ID',
            'Title',
            false
        );
        //Inject the fields that come from the language object
        //They are added to the content tab for the users comfort.
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'SortOrder');
        }
        $fields->addFieldToTab('Root.Main', $siteTreeField, 'Title');
        $fields->addFieldToTab('Root.Main', new TextField('ProductNumberToReference', $this->fieldLabel('ProductNumberToReference')), 'SiteTreeID');
        $fields->removeByName('SilvercartImageSliderWidgets');
        $fields->dataFieldByName('ProductNumberToReference')->setRightTitle($this->fieldLabel('ProductNumberToReferenceInfo'));
        
        return $fields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'SilvercartImageSliderImageLanguages'   => _t('SilvercartImageSliderImageLanguage.PLURALNAME'),
                    'Image'                                 => _t("Image.SINGULARNAME"),
                    'Linkpage'                              => _t('SilvercartImageSliderImage.LINKPAGE'),
                    'ProductNumberToReference'              => _t('SilvercartImageSliderImage.ProductNumberToReference'),
                    'ProductNumberToReferenceInfo'          => _t('SilvercartImageSliderImage.ProductNumberToReferenceInfo'),
                    'SortOrder'                             => _t('SilvercartWidget.SORT_ORDER_LABEL'),
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 20.01.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Thumbnail'      => $this->fieldLabel('Thumbnail'),
            'Title'          => $this->fieldLabel('Title'),
            'TableIndicator' => ''
        );


        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Returns the linked SiteTree object.
     *
     * @return mixed SiteTree|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2013
     */
    public function LinkedSite() {
        $linkedSite = false;
        if (!empty($this->ProductNumberToReference)) {
            $product = DataObject::get_one('SilvercartProduct', sprintf('"SilvercartProduct"."ProductNumberShop" = \'%s\'', $this->ProductNumberToReference));
            if ($product instanceof SilvercartProduct) {
                $linkedSite = $product;
            }
        }
        if ($linkedSite == false &&
            $this->SiteTreeID > 0) {
            $linkedSite = $this->SiteTree();
        }
        
        return $linkedSite;
    }
    
    /**
     * Returns the URL to a thumbnail if an image is assigned.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function getThumbnail() {
        $thumbnail = '';

        if ($this->ImageID > 0) {
            $image     = $this->Image()->SetRatioSize(50, 50);
            
            if ($image) {
                $thumbnail = $image->getTag();
            }
        }

        return $thumbnail;
    }
}