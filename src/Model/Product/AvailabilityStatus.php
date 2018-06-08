<?php

namespace SilverCart\Model\Product;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\Model\Product\AvailabilityStatusTranslation;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Filters\PartialMatchFilter;

/**
 * Abstract for an availibility status.
 *
 * @package SilverCart
 * @subpackage Model_Product
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 29.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AvailabilityStatus extends DataObject {

    /**
     * attributes
     *
     * @var array
     */
    private static $db = array(
        'Code'                => 'Varchar',
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
        'Title'                   => 'Text',
        'AdditionalText'          => 'Text',
        'MicrodataCode'           => 'Text',
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
        'AvailabilityStatusTranslations' => AvailabilityStatusTranslation::class,
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
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartAvailabilityStatus';
    
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
        return Tools::singular_name_for($this);
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
        return Tools::plural_name_for($this);
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
                'badgeColor'                            => OrderStatus::singleton()->fieldLabel('BadgeColor'),
                'Code'                                  => OrderStatus::singleton()->fieldLabel('Code'),
                'Title'                                 => $this->singular_name(),
                'SeoMicrodataCode'                      => _t(AvailabilityStatus::class . '.SeoMicrodataCode', 'SEO microdata code'),
                'SeoMicrodataCodeDescription'           => _t(AvailabilityStatus::class . '.SeoMicrodataCodeDescription', 'Set up one of these values to increase the SEO visibility.'),
                'SeoMicrodataCodeDiscontinued'          => _t(AvailabilityStatus::class . '.SeoMicrodataCodeDiscontinued', 'Discontinued'),
                'SeoMicrodataCodeInStock'               => _t(AvailabilityStatus::class . '.SeoMicrodataCodeInStock', 'In stock'),
                'SeoMicrodataCodeInStoreOnly'           => _t(AvailabilityStatus::class . '.SeoMicrodataCodeInStoreOnly', 'In store only'),
                'SeoMicrodataCodeLimitedAvailability'   => _t(AvailabilityStatus::class . '.SeoMicrodataCodeLimitedAvailability', 'Limited availability'),
                'SeoMicrodataCodeOnlineOnly'            => _t(AvailabilityStatus::class . '.SeoMicrodataCodeOnlineOnly', 'Online only'),
                'SeoMicrodataCodeOutOfStock'            => _t(AvailabilityStatus::class . '.SeoMicrodataCodeOutOfStock', 'Out of stock'),
                'SeoMicrodataCodePreOrder'              => _t(AvailabilityStatus::class . '.SeoMicrodataCodePreOrder', 'Preorder'),
                'SeoMicrodataCodePreSale'               => _t(AvailabilityStatus::class . '.SeoMicrodataCodePreSale', 'Presale'),
                'SeoMicrodataCodeSoldOut'               => _t(AvailabilityStatus::class . '.SeoMicrodataCodeSoldOut', 'Soldout'),
                'AdditionalText'                        => _t(AvailabilityStatus::class . '.ADDITIONALTEXT', 'Additional text'),
                'SetForPositiveStock'                   => _t(AvailabilityStatus::class . '.SetForPositiveStock', 'Assign automatically when a products stock changes from 0 to > 0.'),
                'SetForPositiveStockDesc'               => _t(AvailabilityStatus::class . '.SetForPositiveStockDesc', ' '),
                'SetForPositiveStockShort'              => _t(AvailabilityStatus::class . '.SetForPositiveStockShort', 'Auto. < 1'),
                'SetForNegativeStock'                   => _t(AvailabilityStatus::class . '.SetForNegativeStock', 'Assign automatically when a products stock changes from > 0 to 0.'),
                'SetForNegativeStockDesc'               => _t(AvailabilityStatus::class . '.SetForNegativeStockDesc', ' '),
                'SetForNegativeStockShort'              => _t(AvailabilityStatus::class . '.SetForNegativeStockShort', 'Auto. < 1'),
                'AvailabilityStatusTranslation'         => AvailabilityStatusTranslation::singleton()->singular_name(),
                'AvailabilityStatusTranslations'        => AvailabilityStatusTranslation::singleton()->plural_name(),
                'IsDefault'                             => _t(AvailabilityStatus::class . '.ISDEFAULT', 'Is default'),
                'Yes'                                   => Tools::field_label('Yes'),
                'No'                                    => Tools::field_label('No'),
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
        $fields = DataObjectExtension::getCMSFields($this, 'Code', false);
        
        $badgeColorSource = array(
            'default'   => Tools::string2html('<span style="padding: 4px 8px; color: #fff; background-color:#999999">' . $this->Title . '</span>'),
            'success'   => Tools::string2html('<span style="padding: 4px 8px; color: #fff; background-color:#468847">' . $this->Title . '</span>'),
            'warning'   => Tools::string2html('<span style="padding: 4px 8px; color: #fff; background-color:#f89406">' . $this->Title . '</span>'),
            'important' => Tools::string2html('<span style="padding: 4px 8px; color: #fff; background-color:#b94a48">' . $this->Title . '</span>'),
            'info'      => Tools::string2html('<span style="padding: 4px 8px; color: #fff; background-color:#3a87ad">' . $this->Title . '</span>'),
            'inverse'   => Tools::string2html('<span style="padding: 4px 8px; color: #fff; background-color:#333333">' . $this->Title . '</span>'),
        );
        
        $fields->removeByName('badgeColor');
        $fields->addFieldToTab(
                'Root.Main',
                new OptionsetField('badgeColor', $this->fieldLabel('badgeColor'), $badgeColorSource)
        );
        
        $enumValues = $this->dbObject('SeoMicrodataCode')->enumValues();
        $i18nSource = array();
        foreach ($enumValues as $value => $label) {
            if (empty($label)) {
                $i18nSource[$value] = '';
            } else {
                $i18nSource[$value] = $this->fieldLabel('SeoMicrodataCode' . $label);
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
            'AvailabilityStatusTranslations.Title' => array(
                'title' => $this->fieldLabel('Title'),
                'filter' => PartialMatchFilter::class,
            ),
            'Code' => array(
                'title' => $this->fieldLabel('Code'),
                'filter' => PartialMatchFilter::class,
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
        return $this->getTranslationFieldValue('Title');
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
        return $this->getTranslationFieldValue('AdditionalText');
    }

    /**
     * Returns "Yes" (i18n) or an empty string.
     *
     * @return string
     */
    public function getSetForPositiveStockNice() {
        return $this->SetForPositiveStock ? $this->fieldLabel('Yes') : '';
    }

    /**
     * Returns "Yes" (i18n) or an empty string.
     *
     * @return string
     */
    public function getSetForNegativeStockNice() {
        return $this->SetForNegativeStock ? $this->fieldLabel('Yes') : '';
    }

    /**
     * Casting to get the IsDefault state as a readable string
     *
     * @return string
     */
    public function getIsDefaultString() {
        $IsDefaultString = $this->fieldLabel('No');
        if ($this->IsDefault) {
            $IsDefaultString = $this->fieldLabel('Yes');
        }
        return $IsDefaultString;
    }
    
    /**
     * Returns the availability status to use when a product gets a negative
     * stock.
     * 
     * @return AvailabilityStatus
     */
    public static function get_negative_status() {
        return AvailabilityStatus::get()->filter('SetForNegativeStock', '1')->first();
    }
    
    /**
     * Returns the availability status to use when a product gets a positive
     * stock.
     * 
     * @return AvailabilityStatus
     */
    public static function get_positive_status() {
        return AvailabilityStatus::get()->filter('SetForPositiveStock', '1')->first();
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
            $statusList = AvailabilityStatus::get()->filter(['SetForNegativeStock' => 1])->exclude(['ID' => $this->ID]);
            if ($statusList->exists()) {
                foreach ($statusList as $status) {
                    $status->SetForNegativeStock = false;
                    $status->write();
                }
            }
        }
        if ($this->SetForPositiveStock) {
            $statusList = AvailabilityStatus::get()->filter(['SetForPositiveStock' => 1])->exclude(['ID' => $this->ID]);
            if ($statusList->exists()) {
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
     * @return Tax
     */
    public static function getDefault() {
        return AvailabilityStatus::get()->filter('IsDefault', true)->first();
    }
    
    /**
     * Helper for summary fields.
     * Returns the badge color indicator.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
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
        return Tools::string2html($badgeColorSource[$this->badgeColor]);
    }
    
}