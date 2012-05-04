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
 * @copyright 2011 pixeltricks GmbH
 * @since 03.05.2012
 * @license LGPL
 */
class SilvercartSiteConfig extends DataObjectDecorator {
    
    /**
     * Adds a translation section
     *
     * @param FieldSet &$fields The FieldSet
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public function updateCMSFields(&$fields) {
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
