<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Model_Fieldtypes
 */

/**
 * This is an extended Money Field to modify scaffolding and add some functions.
 *
 * @package Silvercart
 * @subpackage Model_Fieldtypes
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 13.02.2013
 * @license see license file in modules root directory
 */
class SilvercartDBLocale extends DBLocale {

    /**
     * Returns a LanguageDropdownField instance used as a default for form 
     * scaffolding.
     * 
     * @param string $title  Optional. Localized title of the generated instance
     * @param array  $params Optional.
     * 
     * @return FormField
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.03.2013
     */
    public function scaffoldFormField($title = null, $params = null) {
        if (is_null($title)) {
            $title = _t('SilvercartConfig.TRANSLATION');
        }
        $instance                   = null;
        $alreadyTranslatedLocales   = array();
        $translatingClass           = 'SiteTree';
        if (array_key_exists('object', $params)) {
            $translatingClass   = $params['object']->ClassName;
            $instance           = $params['object'];
        }
        if ($instance) {
            $alreadyTranslatedLocales   = $instance->getTranslatedLocales();
            unset($alreadyTranslatedLocales[$instance->Locale]);
        }
        $localeDropdown = new LanguageDropdownField(
            $this->name,
            $title, 
            $alreadyTranslatedLocales,
            $translatingClass,
            'Locale-Native',
            $instance
        );
        $currentLocale          = Translatable::get_current_locale();
        $localesWithTitle       = $localeDropdown->getSource();
        $usedLocalesWithTitle   = Translatable::get_existing_content_languages('SiteTree');
        $languageList           = array();
        $usedLanguageList       = array();
        foreach ($localesWithTitle as $locale => $localeTitle) {
            if (is_array($localeTitle)) {
                foreach ($localeTitle as $locale2 => $title2) {
                    $title2 = SilvercartLanguageHelper::getLanguageDisplayTitle($locale2, $currentLocale);
                    if (array_key_exists($locale2, $usedLocalesWithTitle)) {
                        $usedLanguageList[$locale2] = $title2;
                        unset($languageList[$locale2]);
                    } else {
                        $languageList[$locale2] = $title2;
                    }
                }
            } else {
                $localeTitle = SilvercartLanguageHelper::getLanguageDisplayTitle($locale, $currentLocale);
                if (array_key_exists($locale, $usedLocalesWithTitle)) {
                    $usedLanguageList[$locale] = $localeTitle;
                    unset($languageList[$locale]);
                } else {
                    $languageList[$locale] = $localeTitle;
                }
            }
        }
        asort($languageList);
        
        if (count($usedLanguageList)) {
            asort($usedLanguageList);
            $languageList = array(
                _t('Form.LANGAVAIL',    "Available languages")  => $usedLanguageList,
                _t('Form.LANGAOTHER',   "Other languages")      => $languageList
            );
        }
        
        $localeDropdown->setSource($languageList);
        return $localeDropdown;
    }

}
