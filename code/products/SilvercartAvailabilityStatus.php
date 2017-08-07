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
    private static $db = array(
        'Code'                => 'VarChar',
        'SeoMicrodataCode'    => "Enum(',Discontinued,InStock,InStoreOnly,LimitedAvailability,OnlineOnly,OutOfStock,PreOrder,PreSale,SoldOut','')",
        'badgeColor'          => "Enum('default,success,warning,important,info,inverse','default')",
        'SetForPositiveStock' => 'Boolean(0)',
        'SetForNegativeStock' => 'Boolean(0)',
        'IsDefault'           => 'Boolean',
    );
    
    /**
     * field casting
     *
     * @var array
     */
    private static $casting = array(
        'Title'          => 'Text',
        'AdditionalText' => 'Text',
        'MicrodataCode'  => 'Text',
        'SetForPositiveStockNice' => 'Text',
        'SetForNegativeStockNice' => 'Text',
        'BadgeColorIndicator'     => 'HTMLText',
        'IsDefaultString'         => 'Text',
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'SilvercartAvailabilityStatusLanguages' => 'SilvercartAvailabilityStatusLanguage'
    );
    
    /**
     * Default DB attribute values.
     *
     * @var array
     */
    private static $defaults = array(
        'badgeColor' => "default",
    );
    
    /**
     * List of default microdata codes.
     *
     * @var array
     */
    public static $default_microdata_codes = array(
        'available'           => 'InStock',
        'not-available'       => 'OutOfStock',
        'Discontinued'        => 'Discontinued',
        'InStock'             => 'InStock',
        'InStoreOnly'         => 'InStoreOnly',
        'LimitedAvailability' => 'LimitedAvailability',
        'OnlineOnly'          => 'OnlineOnly',
        'OutOfStock'          => 'OutOfStock',
        'PreOrder'            => 'PreOrder',
        'PreSale'             => 'PreSale',
        'SoldOut'             => 'SoldOut',
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
     * @since 03.07.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'badgeColor'                            => _t('SilvercartOrderStatus.BADGECOLOR'),
                'Code'                                  => _t('SilvercartOrderStatus.CODE'),
                'Title'                                 => _t('SilvercartAvailabilityStatus.SINGULARNAME'),
                'SeoMicrodataCode'                      => _t('SilvercartAvailabilityStatus.SeoMicrodataCode'),
                'SeoMicrodataCodeDescription'           => _t('SilvercartAvailabilityStatus.SeoMicrodataCodeDescription'),
                'SeoMicrodataCodeDiscontinued'          => _t('SilvercartAvailabilityStatus.SeoMicrodataCodeDiscontinued'),
                'SeoMicrodataCodeInStock'               => _t('SilvercartAvailabilityStatus.SeoMicrodataCodeInStock'),
                'SeoMicrodataCodeInStoreOnly'           => _t('SilvercartAvailabilityStatus.SeoMicrodataCodeInStoreOnly'),
                'SeoMicrodataCodeLimitedAvailability'   => _t('SilvercartAvailabilityStatus.SeoMicrodataCodeLimitedAvailability'),
                'SeoMicrodataCodeOnlineOnly'            => _t('SilvercartAvailabilityStatus.SeoMicrodataCodeOnlineOnly'),
                'SeoMicrodataCodeOutOfStock'            => _t('SilvercartAvailabilityStatus.SeoMicrodataCodeOutOfStock'),
                'SeoMicrodataCodePreOrder'              => _t('SilvercartAvailabilityStatus.SeoMicrodataCodePreOrder'),
                'SeoMicrodataCodePreSale'               => _t('SilvercartAvailabilityStatus.SeoMicrodataCodePreSale'),
                'SeoMicrodataCodeSoldOut'               => _t('SilvercartAvailabilityStatus.SeoMicrodataCodeSoldOut'),
                'AdditionalText'                        => _t('SilvercartAvailabilityStatus.ADDITIONALTEXT'),
                'SetForPositiveStock'                   => _t('SilvercartAvailabilityStatus.SetForPositiveStock'),
                'SetForPositiveStockDesc'               => _t('SilvercartAvailabilityStatus.SetForPositiveStockDesc'),
                'SetForPositiveStockShort'              => _t('SilvercartAvailabilityStatus.SetForPositiveStockShort'),
                'SetForNegativeStock'                   => _t('SilvercartAvailabilityStatus.SetForNegativeStock'),
                'SetForNegativeStockDesc'               => _t('SilvercartAvailabilityStatus.SetForNegativeStockDesc'),
                'SetForNegativeStockShort'              => _t('SilvercartAvailabilityStatus.SetForNegativeStockShort'),
                'SilvercartAvailabilityStatusLanguages' => _t('SilvercartAvailabilityStatusLanguage.SINGULARNAME'),
                'IsDefault'                             => _t('SilvercartAvailabilityStatus.ISDEFAULT'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this, 'Code', false);

        $htmlDefault   = new HTMLText();
        $htmlSuccess   = new HTMLText();
        $htmlWarning   = new HTMLText();
        $htmlImportant = new HTMLText();
        $htmlInfo      = new HTMLText();
        $htmlInverse   = new HTMLText();
        $htmlDefault   ->setValue('<span style="padding: 4px 8px; color: #fff; background-color:#999999">' . $this->Title . '</span>');
        $htmlSuccess   ->setValue('<span style="padding: 4px 8px; color: #fff; background-color:#468847">' . $this->Title . '</span>');
        $htmlWarning   ->setValue('<span style="padding: 4px 8px; color: #fff; background-color:#f89406">' . $this->Title . '</span>');
        $htmlImportant ->setValue('<span style="padding: 4px 8px; color: #fff; background-color:#b94a48">' . $this->Title . '</span>');
        $htmlInfo      ->setValue('<span style="padding: 4px 8px; color: #fff; background-color:#3a87ad">' . $this->Title . '</span>');
        $htmlInverse   ->setValue('<span style="padding: 4px 8px; color: #fff; background-color:#333333">' . $this->Title . '</span>');
        
        $badgeColorSource = array(
            'default'   => $htmlDefault,
            'success'   => $htmlSuccess,
            'warning'   => $htmlWarning,
            'important' => $htmlImportant,
            'info'      => $htmlInfo,
            'inverse'   => $htmlInverse,
        );
        
        $fields->removeByName('badgeColor');
        $fields->addFieldToTab(
                'Root.Main',
                new OptionsetField('badgeColor', $this->fieldLabel('badgeColor'), $badgeColorSource)
        );
        
        $enumValues = $this->owner->dbObject('SeoMicrodataCode')->enumValues();
        $i18nSource = array();
        foreach ($enumValues as $value => $label) {
            if (empty($label)) {
                $i18nSource[$value] = '';
            } else {
                $i18nSource[$value] = _t('SilvercartAvailabilityStatus.SeoMicrodataCode' . $label, $label);
            }
        }
        $fields->dataFieldByName('SeoMicrodataCode')->setSource($i18nSource);
        $fields->dataFieldByName('SeoMicrodataCode')->setDescription($this->fieldLabel('SeoMicrodataCodeDescription'));

        return $fields;
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.07.2014
     */
    public function summaryFields() {
        $summaryFields = array(
            'BadgeColorIndicator' => $this->fieldLabel('badgeColor'),
            'Title' => $this->fieldLabel('Title'),
            'Code'  => $this->fieldLabel('Code'),
            'SetForNegativeStockNice' => $this->fieldLabel('SetForNegativeStockShort'),
            'SetForPositiveStockNice' => $this->fieldLabel('SetForPositiveStockShort'),
            'IsDefaultString'   => $this->fieldLabel('IsDefault'),
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
     * Getter for the pseudo attribute title.
     * Returns the title in the corresponding frontend language.
     *
     * @return string
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
    /**
     * Returns the title for SEO microdata
     *
     * @return string
     */
    public function getMicrodataCode() {
        $microDataCode = $this->SeoMicrodataCode;
        if (empty($microDataCode) &&
            array_key_exists($this->Code, self::$default_microdata_codes)) {
            $microDataCode = self::$default_microdata_codes[$this->Code];
        }
        if (!empty($microDataCode)) {
            $microDataCode = 'http://schema.org/' . $microDataCode;
        }
        return $microDataCode;
    }

    /**
     * getter for the pseudo attribute AdditionalText
     *
     * @return string
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
     * Casting to get the IsDefault state as a readable string
     *
     * @return string
     */
    public function getIsDefaultString() {
        $IsDefaultString = _t('Silvercart.NO');
        if ($this->IsDefault) {
            $IsDefaultString = _t('Silvercart.YES');
        }
        return $IsDefaultString;
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
     * @since 03.07.2014
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
        
        $defaultStatus = self::getDefault();
        if (!$defaultStatus) {
            $defaultStatus = $this;
            $this->IsDefault = true;
        } elseif ($this->IsDefault &&
                  $defaultStatus->ID != $this->ID) {
            $defaultStatus->IsDefault = false;
            $defaultStatus->write();
        }
    }
    
    /**
     * Returns the default tax rate
     * 
     * @return SilvercartTax
     */
    public static function getDefault() {
        return DataObject::get_one('SilvercartAvailabilityStatus', '"IsDefault" = 1');
    }
    
    /**
     * Helper for summary fields.
     * Returns the badge color indicator.
     * 
     * @return string
     */
    public function getBadgeColorIndicator() {
        $badgeColorSource = array(
            'default'   => '<span style="padding: 4px 8px; color: #fff; background-color:#999999">' . $this->Title . '</span>',
            'success'   => '<span style="padding: 4px 8px; color: #fff; background-color:#468847">' . $this->Title . '</span>',
            'warning'   => '<span style="padding: 4px 8px; color: #fff; background-color:#f89406">' . $this->Title . '</span>',
            'important' => '<span style="padding: 4px 8px; color: #fff; background-color:#b94a48">' . $this->Title . '</span>',
            'info'      => '<span style="padding: 4px 8px; color: #fff; background-color:#3a87ad">' . $this->Title . '</span>',
            'inverse'   => '<span style="padding: 4px 8px; color: #fff; background-color:#333333">' . $this->Title . '</span>',
        );
        if (empty($this->badgeColor) ||
            !array_key_exists($this->badgeColor, $badgeColorSource)) {
            $this->badgeColor = 'default';
        }
        $htmlText = HTMLText::create();
        $htmlText->setValue($badgeColorSource[$this->badgeColor]);
        return $htmlText;
    }
    
}

/**
 * Translations for SilvercartAvailabilityStatus
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 14.01.2012
 * @license see license file in modules root directory
 */
class SilvercartAvailabilityStatusLanguage extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'Title'          => 'VarChar',
        'AdditionalText' => 'Text',
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartAvailabilityStatus' => 'SilvercartAvailabilityStatus'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 15.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Title'          => _t('SilvercartAvailabilityStatus.SINGULARNAME'),
                    'AdditionalText' => _t('SilvercartAvailabilityStatus.ADDITIONALTEXT'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}