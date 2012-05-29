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
        'TableIndicator' => 'Text',
        'Thumbnail'      => 'HTMLText'
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
     * @return FieldSet the fields for the backend
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage());
        foreach ($languageFields as $languageField) {
            $fields->insertBefore($languageField, 'SortOrder');
        }
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
                'TableIndicator'            => _t('Silvercart.TABLEINDICATOR'),
                'SortOrder'                 => _t('Silvercart.SORTORDER'),
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
            'Thumbnail'      => $this->fieldLabel('Thumbnail'),
            'Title'          => $this->fieldLabel('Title'),
            'TableIndicator' => $this->fieldLabel('TableIndicator'),
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
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
