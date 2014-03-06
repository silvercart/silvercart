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
     * Casted properties
     *
     * @var array
     */
    public static $casting = array(
        'Title'          => 'VarChar',
        'Thumbnail'      => 'HTMLText'
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.01.2012
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
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>, Roland Lehmann
     * @since 10.02.2013
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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2011
     */
    public function LinkedSite() {
        if ($this->SiteTreeID > 0) {
            return $this->SiteTree();
        }
        
        return false;
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