<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Config
 */

/**
 * This class is used to add a translation section to the original SiteConfig 
 * object in the cms section.
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 04.04.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartSiteConfig extends DataExtension {
    
    /**
     * extra attributes
     *
     * @var array
     */
    public static $db = array(
        'GoogleAnalyticsTrackingCode'   => 'Text',
        'GoogleConversionTrackingCode'  => 'Text',
        'GoogleWebmasterCode'           => 'Text',
        'PiwikTrackingCode'             => 'Text',
        'FacebookLink'                  => 'Text',
        'TwitterLink'                   => 'Text',
        'XingLink'                      => 'Text',
    );
    
    /**
     * Updates the fields labels
     *
     * @param array &$labels Labels to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2013
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
                array(
                    'GoogleAnalyticsTrackingCode'   => _t('SilvercartSiteConfig.GOOGLE_ANALYTICS_TRACKING_CODE'),
                    'GoogleConversionTrackingCode'  => _t('SilvercartSiteConfig.GOOGLE_CONVERSION_TRACKING_CODE'),
                    'GoogleWebmasterCode'           => _t('SilvercartSiteConfig.GOOGLE_WEBMASTER_CODE'),
                    'PiwikTrackingCode'             => _t('SilvercartSiteConfig.PIWIK_TRACKING_CODE'),
                    'FacebookLink'                  => _t('SilvercartSiteConfig.FACEBOOK_LINK'),
                    'TwitterLink'                   => _t('SilvercartSiteConfig.TWITTER_LINK'),
                    'XingLink'                      => _t('SilvercartSiteConfig.XING_LINK'),
                    'SeoTab'                        => _t('Silvercart.SEO'),
                    'SocialMediaTab'                => _t('Silvercart.SOCIALMEDIA'),
                    'TranslationsTab'               => _t('Silvercart.TRANSLATIONS'),
                    'CreateTransHeader'             => _t('Translatable.CREATE'),
                    'CreateTransDescription'        => _t('Translatable.CREATE_TRANSLATION_DESC'),
                    'NewTransLang'                  => _t('Translatable.NEWLANGUAGE'),
                    'createsitetreetranslation'     => _t('Translatable.CREATEBUTTON'),
                    'createsitetreetranslationDesc' => _t('Translatable.CREATEBUTTON_DESC'),
                    'publishsitetree'               => _t('Translatable.PUBLISHBUTTON'),
                    'ExistingTransHeader'           => _t('Translatable.EXISTING'),
                    'CurrentLocale'                 => _t('Translatable.CURRENTLOCALE'),
                )
        );
    }
    
    /**
     * Adds a translation section
     *
     * @param FieldList $fields The FieldList
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2013
     */
    public function updateCMSFields(FieldList $fields) {
        $fields->findOrMakeTab('Root.SEO')          ->setTitle($this->owner->fieldLabel('SeoTab'));
        $fields->findOrMakeTab('Root.SocialMedia')  ->setTitle($this->owner->fieldLabel('SocialMediaTab'));
        $fields->findOrMakeTab('Root.Translations') ->setTitle($this->owner->fieldLabel('TranslationsTab'));
        
        $googleWebmasterCodeField           = new TextField('GoogleWebmasterCode',              $this->owner->fieldLabel('GoogleWebmasterCode'));
        $googleAnalyticsTrackingCodeField   = new TextareaField('GoogleAnalyticsTrackingCode',  $this->owner->fieldLabel('GoogleAnalyticsTrackingCode'));
        $googleConversionTrackingCodeField  = new TextareaField('GoogleConversionTrackingCode', $this->owner->fieldLabel('GoogleConversionTrackingCode'));
        $piwikTrackingCodeField             = new TextareaField('PiwikTrackingCode',            $this->owner->fieldLabel('PiwikTrackingCode'));
        
        $fields->addFieldToTab('Root.SEO', $googleWebmasterCodeField);
        $fields->addFieldToTab('Root.SEO', $googleAnalyticsTrackingCodeField);
        $fields->addFieldToTab('Root.SEO', $googleConversionTrackingCodeField);
        $fields->addFieldToTab('Root.SEO', $piwikTrackingCodeField);
        
        $facebookLinkField  = new TextField('FacebookLink',     $this->owner->fieldLabel('FacebookLink'));
        $twitterLinkField   = new TextField('TwitterLink',      $this->owner->fieldLabel('TwitterLink'));
        $xingLinkField      = new TextField('XingLink',         $this->owner->fieldLabel('XingLink'));
        
        $fields->addFieldToTab('Root.SocialMedia', $facebookLinkField);
        $fields->addFieldToTab('Root.SocialMedia', $twitterLinkField);
        $fields->addFieldToTab('Root.SocialMedia', $xingLinkField);
        
        $translatable = new Translatable();
        $translatable->setOwner($this->owner);
        $translatable->updateCMSFields($fields);
        
        $localeField    = new TextField('CurrentLocale',                        $this->owner->fieldLabel('CurrentLocale'),              i18n::get_locale_name($this->owner->Locale));
        $createButton   = new InlineFormAction('createsitetreetranslation',     $this->owner->fieldLabel('createsitetreetranslation'));
        $publishButton  = new InlineFormAction('publishsitetree',               $this->owner->fieldLabel('publishsitetree'));
        
        $localeField->setReadonly(true);
        $localeField->setDisabled(true);
        $createButton->setRightTitle($this->owner->fieldLabel('createsitetreetranslationDesc'));
        $createButton->includeDefaultJS(false);
        $createButton->addExtraClass('createTranslationButton');
        $publishButton->includeDefaultJS(false);
        $publishButton->addExtraClass('createTranslationButton');
        
        $fields->addFieldToTab('Root.Translations', $localeField,   'CreateTransHeader');
        $fields->addFieldToTab('Root.Translations', $createButton,  'createtranslation');
        $fields->addFieldToTab('Root.Translations', $publishButton, 'createtranslation');
        $fields->removeByName('createtranslation');
    }
    
}
