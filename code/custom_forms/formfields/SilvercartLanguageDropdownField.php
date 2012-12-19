<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage FormFields
 */

/**
 * A formfield for changing the frontends language
 *
 * @package Silvercart
 * @subpackage FormFields
 * @copyright pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.04.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartLanguageDropdownField extends DropdownField {
    
    /**
     * Controller
     *
     * @var SilvercartPage_Controller
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
    public function __construct($name, $title = null, $source = array(), $value = "", $form = null, $emptyString = null) {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->setController($form->controller);
        
        $currentLocale      = Translatable::get_current_locale();
        $translations       = $this->getController()->getTranslations();
        $translationSource  = array();
        $translationSource[$currentLocale] = array(
            'title' => $this->getDisplayLanguage($currentLocale, $currentLocale),
            'rel'   => $this->getIso2($currentLocale),
        );
        foreach ($translations as $translation) {
            $translationSource[$translation->Locale] = array(
                'title' => $this->getDisplayLanguage($translation->Locale, $currentLocale),
                'rel'   => $this->getIso2($translation->Locale),
            );
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
            SilvercartLanguageHelper::getDisplayLanguage($locale, $in_locale),
            SilvercartLanguageHelper::getDisplayLanguage($locale, $locale)
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
     * @return string HTML tag for this dropdown field
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function Field() {
        $options = '';

        $source = $this->getSource();
        if ($source) {
            // For SQLMap sources, the empty string needs to be added specially
            if (is_object($source) && $this->emptyString) {
                $options .= $this->createTag('option', array('value' => ''), $this->emptyString);
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

                $options .= $this->createTag(
                    'option', array(
                        'selected'  => $selected,
                        'value'     => $value,
                        'class'     => $rel,
                    ),
                    Convert::raw2xml($title)
                );
            }
        }

        $attributes = array(
            'class' => ($this->extraClass() ? $this->extraClass() : ''),
            'id' => $this->id(),
            'name' => $this->name,
            'tabindex' => $this->getTabIndex()
        );

        if ($this->disabled) {
            $attributes['disabled'] = 'disabled';
        }

        return $this->createTag('select', $attributes, $options);
    }
    
    /**
     * Returns the controller
     *
     * @return SilvercartPage_Controller 
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Sets the controller
     *
     * @param SilvercartPage_Controller $controller Controller
     * 
     * @return void
     */
    public function setController($controller) {
        $this->controller = $controller;
    }
    
}