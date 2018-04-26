<?php

namespace SilverCart\ORM\FieldType;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Model\Config;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\DropdownField;
use SilverStripe\i18n\i18n;

/**
 * This is an extended Money Field to modify scaffolding and add some functions.
 *
 * @package SilverCart
 * @subpackage ORM_FieldType
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DBLocale extends \SilverStripe\ORM\FieldType\DBLocale {

    /**
     * Returns a locale DropdownField instance used as a default for form 
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
            $title = _t(Config::class . '.TRANSLATION', 'Translation');
        }
        $localeDropdown = DropdownField::create(
                $this->name,
                $title,
                i18n::getData()->getLocales()
        );
        $currentLocale          = Tools::current_locale();
        $localesWithTitle       = $localeDropdown->getSource();
        $usedLocalesWithTitle   = Tools::content_locales()->toArray();
        $languageList           = array();
        $usedLanguageList       = array();
        foreach ($localesWithTitle as $locale => $localeTitle) {
            if (is_array($localeTitle)) {
                foreach ($localeTitle as $locale2 => $title2) {
                    $title2 = TranslationTools::get_translation_display_title($locale2, $currentLocale);
                    if (array_key_exists($locale2, $usedLocalesWithTitle)) {
                        $usedLanguageList[$locale2] = $title2;
                        unset($languageList[$locale2]);
                    } else {
                        $languageList[$locale2] = $title2;
                    }
                }
            } else {
                $localeTitle = TranslationTools::get_translation_display_title($locale, $currentLocale);
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
