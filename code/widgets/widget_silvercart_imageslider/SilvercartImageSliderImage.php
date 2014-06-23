<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 21.03.2011
 * @license see license file in modules root directory
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
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding() {
        $excludeFromScaffolding = array(
            'SilvercartImageSliderWidgets',
            'SortOrder'
        );
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
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
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'ExtraCssClasses', false);
        
        $siteTreeField = new TreeDropdownField(
            'SiteTreeID',
            $this->fieldLabel('Linkpage'),
            'SiteTree',
            'ID',
            'Title',
            false
        );
        $fields->addFieldToTab('Root.Main', $siteTreeField, 'Title');
        $fields->addFieldToTab('Root.Main', new TextField('ProductNumberToReference', $this->fieldLabel('ProductNumberToReference')), 'SiteTreeID');
        $fields->removeByName('SilvercartImageSliderWidgets');
        $fields->dataFieldByName('ProductNumberToReference')->setDescription($this->fieldLabel('ProductNumberToReferenceInfo'));
        
        return $fields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.03.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'SilvercartImageSliderImageLanguages'       => _t('SilvercartImageSliderImageLanguage.PLURALNAME'),
                'Image'                                     => _t('Image.SINGULARNAME'),
                'Linkpage'                                  => _t('SilvercartImageSliderImage.LINKPAGE'),
                'ProductNumberToReference'              => _t('SilvercartImageSliderImage.ProductNumberToReference'),
                'ProductNumberToReferenceInfo'          => _t('SilvercartImageSliderImage.ProductNumberToReferenceInfo'),
                'SortOrder'                             => _t('SilvercartWidget.SORT_ORDER_LABEL'),
                'Thumbnail'                                 => _t('SilvercartImage.THUMBNAIL'),
                'Title'                                     => _t('SilvercartImage.TITLE'),
                'SilvercartImageSliderImageLanguages.Title' => _t('SilvercartImage.TITLE'),
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
     * @since 20.01.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Thumbnail'      => $this->fieldLabel('Thumbnail'),
            'Title'          => $this->fieldLabel('Title'),
        );


        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Searchable fields definition
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.03.2014
     */
    public function searchableFields() {
        $searchableFields = array(
            'SilvercartImageSliderImageLanguages.Title' => array(
                'title'  => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            )
        );
            
        return $searchableFields;
    }
    
    /**
     * Returns the linked SiteTree object.
     *
     * @return mixed SiteTree|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.06.2014
     */
    public function LinkedSite() {
        $linkedSite = false;
        if (!empty($this->ProductNumberToReference)) {
            $product = SilvercartProduct::get()->filter('ProductNumberShop', $this->ProductNumberToReference)->first();
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
     * @return Image_Cached|false thumbnail ratio 50:50
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.05.2012
     */
    public function getThumbnail() {
        $result = false;

        if ($this->Image()->isInDB()) {
            $result = $this->Image()->SetRatioSize(50, 50);
        }
        return $result;
    }
}