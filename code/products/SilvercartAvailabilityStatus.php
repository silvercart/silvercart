<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Products
 */

/**
 * abstract for an availibility status
 *
 * @package Silvercart
 * @subpackage Products
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 18.03.2011
 * @license see license file in modules root directory
 */
class SilvercartAvailabilityStatus extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    public static $db = array(
        'Code'                => 'VarChar',
        'badgeColor'          => "Enum('default,success,warning,important,info,inverse','default')",
        'SetForPositiveStock' => 'Boolean(0)',
        'SetForNegativeStock' => 'Boolean(0)',
    );
    
    /**
     * field casting
     *
     * @var array
     */
    public static $casting = array(
        'Title'          => 'Text',
        'AdditionalText' => 'Text',
        'SetForPositiveStockNice' => 'Text',
        'SetForNegativeStockNice' => 'Text',
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartAvailabilityStatusLanguages' => 'SilvercartAvailabilityStatusLanguage'
    );

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 11.06.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'badgeColor'                            => _t('SilvercartOrderStatus.BADGECOLOR'),
                'Code'                                  => _t('SilvercartOrderStatus.CODE'),
                'Title'                                 => _t('SilvercartAvailabilityStatus.SINGULARNAME'),
                'AdditionalText'                        => _t('SilvercartAvailabilityStatus.ADDITIONALTEXT'),
                'SetForPositiveStock'                   => _t('SilvercartAvailabilityStatus.SetForPositiveStock'),
                'SetForPositiveStockDesc'               => _t('SilvercartAvailabilityStatus.SetForPositiveStockDesc'),
                'SetForPositiveStockShort'              => _t('SilvercartAvailabilityStatus.SetForPositiveStockShort'),
                'SetForNegativeStock'                   => _t('SilvercartAvailabilityStatus.SetForNegativeStock'),
                'SetForNegativeStockDesc'               => _t('SilvercartAvailabilityStatus.SetForNegativeStockDesc'),
                'SetForNegativeStockShort'              => _t('SilvercartAvailabilityStatus.SetForNegativeStockShort'),
                'SilvercartAvailabilityStatusLanguages' => _t('SilvercartAvailabilityStatusLanguage.SINGULARNAME')
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2014
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'Code', false);

        $badgeColorSource = array(
            'default'   => '<span style="padding: 4px 8px; color: #fff; background-color:#999999">' . $this->Title . '</span>',
            'success'   => '<span style="padding: 4px 8px; color: #fff; background-color:#468847">' . $this->Title . '</span>',
            'warning'   => '<span style="padding: 4px 8px; color: #fff; background-color:#f89406">' . $this->Title . '</span>',
            'important' => '<span style="padding: 4px 8px; color: #fff; background-color:#b94a48">' . $this->Title . '</span>',
            'info'      => '<span style="padding: 4px 8px; color: #fff; background-color:#3a87ad">' . $this->Title . '</span>',
            'inverse'   => '<span style="padding: 4px 8px; color: #fff; background-color:#333333">' . $this->Title . '</span>',
        );
        
        $fields->removeByName('badgeColor');
        $fields->addFieldToTab(
                'Root.Main',
                new OptionsetField('badgeColor', $this->fieldLabel('badgeColor'), $badgeColorSource)
        );

        return $fields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2014
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title' => $this->fieldLabel('Title'),
            'Code'  => $this->fieldLabel('Code'),
            'SetForNegativeStockNice' => $this->fieldLabel('SetForNegativeStockShort'),
            'SetForPositiveStockNice' => $this->fieldLabel('SetForPositiveStockShort'),
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Defines the form fields for the search in ModelAdmin
     * 
     * @return array seach fields definition
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2012
     */
    public function searchableFields() {
        $searchableFields = array(
            'SilvercartAvailabilityStatusLanguages.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => 'PartialMatchFilter'
            ),
            'Code' => array(
                'title' => $this->fieldLabel('Code'),
                'filter' => 'PartialMatchFilter'
            ),
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }
    
    /**
     * getter for the pseudo attribute title
     *
     * @return string the title in the corresponding frontend language 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.01.2012
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }

    /**
     * getter for the pseudo attribute AdditionalText
     *
     * @return string the title in the corresponding frontend language
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.12.2012
     */
    public function getAdditionalText() {
        return $this->getLanguageFieldValue('AdditionalText');
    }

    /**
     * Returns "Yes" (i18n) or an empty string.
     *
     * @return string
     */
    public function getSetForPositiveStockNice() {
        return $this->SetForPositiveStock ? _t('Silvercart.YES') : '';
    }

    /**
     * Returns "Yes" (i18n) or an empty string.
     *
     * @return string
     */
    public function getSetForNegativeStockNice() {
        return $this->SetForNegativeStock ? _t('Silvercart.YES') : '';
    }
    
    /**
     * Returns the availability status to use when a product gets a negative
     * stock.
     * 
     * @return SilvercartAvailabilityStatus
     */
    public static function get_negative_status() {
        return SilvercartAvailabilityStatus::get()->filter('SetForNegativeStock', '1')->first();
    }
    
    /**
     * Returns the availability status to use when a product gets a positive
     * stock.
     * 
     * @return SilvercartAvailabilityStatus
     */
    public static function get_positive_status() {
        return SilvercartAvailabilityStatus::get()->filter('SetForPositiveStock', '1')->first();
    }
    
    /**
     * Sets for other status SetForNegativeStock and SetForPositiveStock to 
     * false when set to $this.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.06.2014
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if ($this->SetForNegativeStock) {
            $statusList = SilvercartAvailabilityStatus::get()
            ->filter(array(
                        'SetForNegativeStock' => 1,
            ))->exclude(array(
                        'SilvercartAvailabilityStatus.ID' => $this->ID
            ));
            if ($statusList) {
                foreach ($statusList as $status) {
                    $status->SetForNegativeStock = false;
                    $status->write();
                }
            }
        }
        if ($this->SetForPositiveStock) {
            $statusList = SilvercartAvailabilityStatus::get()
            ->filter(array(
                        'SetForPositiveStock' => 1,
            ))->exclude(array(
                        'SilvercartAvailabilityStatus.ID' => $this->ID
            ));
            if ($statusList) {
                foreach ($statusList as $status) {
                    $status->SetForPositiveStock = false;
                    $status->write();
                }
            }
        }
    }
    
}