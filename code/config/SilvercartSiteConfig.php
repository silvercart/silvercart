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
 * @subpackage Config
 */

/**
 * This class is used to add a translation section to the original SiteConfig 
 * object in the cms section.
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 15.06.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartSiteConfig extends DataObjectDecorator {
    
    /**
     * Returns the extra statice
     *
     * @return array 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.06.2012
     */
    public function extraStatics() {
        return array(
            'db' => array(
                'GoogleAnalyticsTrackingCode'   => 'Text',
                'GoogleWebmasterCode'           => 'Text',
                'PiwikTrackingCode'             => 'Text',
            ),
        );
    }
    
    /**
     * Updates the fields labels
     *
     * @param array &$labels Labels to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.06.2012
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
                array(
                    'GoogleAnalyticsTrackingCode'   => _t('SilvercartSiteConfig.GOOGLE_ANALYTICS_TRACKING_CODE'),
                    'GoogleWebmasterCode'           => _t('SilvercartSiteConfig.GOOGLE_WEBMASTER_CODE'),
                    'PiwikTrackingCode'             => _t('SilvercartSiteConfig.PIWIK_TRACKING_CODE'),
                )
        );
    }
    
    /**
     * Adds a translation section
     *
     * @param FieldSet &$fields The FieldSet
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.06.2012
     */
    public function updateCMSFields(FieldSet &$fields) {
        $seoTab = $fields->findOrMakeTab('Root.SEO', _t('Silvercart.SEO'));
        
        $googleAnalyticsTrackingCodeField   = new TextareaField('GoogleAnalyticsTrackingCode',  $this->owner->fieldLabel('GoogleAnalyticsTrackingCode'));
        $googleWebmasterCodeField           = new TextareaField('GoogleWebmasterCode',          $this->owner->fieldLabel('GoogleWebmasterCode'));
        $piwikTrackingCodeField             = new TextareaField('PiwikTrackingCode',            $this->owner->fieldLabel('PiwikTrackingCode'));
        
        $fields->addFieldToTab('Root.SEO', $googleAnalyticsTrackingCodeField);
        $fields->addFieldToTab('Root.SEO', $googleWebmasterCodeField);
        $fields->addFieldToTab('Root.SEO', $piwikTrackingCodeField);
        
        // used in CMSMain->init() to set language state when reading/writing record
        $fields->push(new HiddenField("Locale", "Locale", $this->owner->Locale));
        
        $alreadyTranslatedLocales = Translatable::get_existing_content_languages('SiteConfig');
        foreach ($alreadyTranslatedLocales as $locale => $name) {
            $alreadyTranslatedLocales[$locale] = $locale;
        }
                
        $fields->addFieldsToTab(
                'Root', new Tab('Translations', _t('Translatable.TRANSLATIONS', 'Translations'),
                        new HeaderField('CreateTransHeader', _t('Translatable.CREATE', 'Create new translation'), 2),
                        new LiteralField('CreateTransDescription', '<p>' . _t('SilvercartSiteConfig.CREATE_TRANSLATION_DESC', 'Create new translation') . '</p>'),
                        $langDropdown = new LanguageDropdownField(
                                "NewTransLang",
                                _t('Translatable.NEWLANGUAGE', 'New language'),
                                $alreadyTranslatedLocales,
                                'SiteConfig',
                                'Locale-English',
                                $this->owner
                        ),
                        $createButton = new InlineFormAction('createsitetreetranslation', _t('Translatable.CREATEBUTTON', 'Create')),
                        $publishButton = new InlineFormAction('publishsitetree', _t('SilvercartSiteConfig.PUBLISHBUTTON', 'Publish all pages of this translation'))
                )
        );
        $createButton->includeDefaultJS(false);
        $publishButton->includeDefaultJS(false);

        if ($alreadyTranslatedLocales) {
            $fields->addFieldToTab(
                    'Root.Translations', new HeaderField('ExistingTransHeader', _t('Translatable.EXISTING', 'Existing translations:'), 3)
            );
            $existingTransHTML = '<ul>';
            foreach ($alreadyTranslatedLocales as $i => $langCode) {
                $existingTranslation = DataObject::get_one(
                        'SiteConfig',
                        sprintf(
                                "`Locale` = '%s'",
                                $langCode
                        )
                );
                if ($existingTranslation) {
                    $existingTransHTML .= sprintf('<li><a href="%s">%s</a></li>', sprintf('admin/show/root/?locale=%s', $langCode), i18n::get_locale_name($langCode)
                    );
                }
            }
            $existingTransHTML .= '</ul>';
            $fields->addFieldToTab(
                    'Root.Translations', new LiteralField('existingtrans', $existingTransHTML)
            );
        }

        $langDropdown->addExtraClass('languageDropdown');
        $createButton->addExtraClass('createTranslationButton');
        $publishButton->addExtraClass('createTranslationButton');
    }
    
}
