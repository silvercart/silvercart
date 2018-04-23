<?php

namespace SilverCart\Forms\FormFields;

use SilverCart\Model\Pages\PageController;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\Form;
use Translatable;

/** 
 * A formfield for changing the frontends language.
 *
 * @package SilverCart
 * @subpackage Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class LanguageDropdownField extends DropdownField {
    
    /**
     * Controller
     *
     * @var PageController
     */
    protected $controller;

    /**
     * Creates a new silvercart language dropdown field.
     *
     * @param string $name        The fields name
     * @param string $title       The fields title
     * @param array  $source      An map of the dropdown items (will be 
     * @param string $value       Current value
     * @param Form   $form        Form
     * @param string $emptyString Empty string
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function __construct($name, $title = null, $source = [], $value = "", $form = null, $emptyString = null) {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->setController($form->getController());
        
        $currentLocale      = Translatable::get_current_locale();
        $translations       = $this->getController()->getTranslations();
        $translationSource  = [];
        $translationSource[$currentLocale] = [
            'title' => $this->getDisplayLanguage($currentLocale, $currentLocale),
            'rel'   => $this->getIso2($currentLocale),
        ];
        foreach ($translations as $translation) {
            $translationSource[$translation->Locale] = [
                'title' => $this->getDisplayLanguage($translation->Locale, $currentLocale),
                'rel'   => $this->getIso2($translation->Locale),
            ];
        }
        $this->setSource($translationSource);
    }
    
    /**
     * Returns the display language for the given locale
     *
     * @param string $locale    Locale to get language name for
     * @param string $in_locale Native locale
     * 
     * @return string
     */
    public function getDisplayLanguage($locale, $in_locale) {
        $displayLanguage = sprintf(
            "%s - %s",
            TranslationTools::get_display_language($locale, $in_locale),
            TranslationTools::get_display_language($locale, $locale)
        );
        return $displayLanguage;
    }


    /**
     * Returns the ISO2 for the given locale
     *
     * @param string $locale Locale
     * 
     * @return string
     */
    public function getIso2($locale) {
        $parts = explode('_', $locale);
        return strtolower($parts[1]);
    }

    /**
     * Returns a <select> tag containing all the appropriate <option> tags.
     * Makes use of {@link FormField->createTag()} to generate the <select>
     * tag and option elements inside is as the content of the <select>.
     * 
     * @param array $properties not in use, just declared to be compatible with parent
     * 
     * @return string HTML tag for this dropdown field
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.06.2014
     */
    public function Field($properties = []) {
        $options    = '';
        $controller = $this->getController();

        $source = $this->getSource();
        if ($source) {
            // For SQLMap sources, the empty string needs to be added specially
            if (is_object($source) && $this->emptyString) {
                $options .= $this->createTag('option', ['value' => ''], $this->emptyString);
            }

            foreach ($source as $value => $properties) {
                $title  = $properties['title'];
                $rel    = $properties['rel'];
                
                if (!empty($value)) {
                    if ($value) {
                        $selected = ($value == $this->value) ? 'selected' : null;
                    } else {
                        $selected = ($value === $this->value) ? 'selected' : null;
                    }
                    $this->isSelected = ($selected) ? true : false;
                }


                $link = "#";
                if ($controller) {
                    $translation = $controller->getTranslation($value);
                    if ($translation instanceof SiteTree) {
                        $link = $translation->Link();
                    }
                }

                $options .= $this->createTag(
                    'option', [
                        'selected'  => $selected,
                        'value'     => $value.'|'.$link,
                        'class'     => $rel
                    ],
                    Convert::raw2xml($title)
                );
            }
        }

        $attributes = [
            'class' => ($this->extraClass() ? $this->extraClass() : ''),
            'id' => $this->id(),
            'name' => $this->name,
            'tabindex' => $this->getAttribute("tabindex")
        ];

        if ($this->disabled) {
            $attributes['disabled'] = 'disabled';
        }

        return $this->createTag('select', $attributes, $options);
    }
    
    /**
     * Returns the controller
     *
     * @return PageController 
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Sets the controller
     *
     * @param PageController $controller Controller
     * 
     * @return void
     */
    public function setController($controller) {
        $this->controller = $controller;
    }
    
}