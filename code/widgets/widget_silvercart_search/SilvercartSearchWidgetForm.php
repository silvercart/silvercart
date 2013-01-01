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
 * @subpackage Widgets
 */

/**
 * form definition
 *
 * @package Silvercart
 * @subpackage Widgets
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 26.05.2011
 */
class SilvercartSearchWidgetForm extends CustomHtmlForm {

    /**
     * Form field definition
     *
     * @var array
     * @since 26.05.2011
     */
    protected $formFields = array(
        'quickSearchQuery' => array(
            'type'              => 'TextField',
            'title'             => '',
            'value'             => '',
            'checkRequirements' => array(
                'isFilledIn' => true
            )
        )
    );

    /**
     * Preferences
     *
     * @var array
     * @since 30.10.2012
     */
    protected $preferences = array(
        'doJsValidationScrolling' => false,
    );

    /**
     * Save search query in session and Redirect to the search results page.
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return array to be rendered in the controller
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    protected function submitSuccess($data, $form, $formData) {
        Session::set("searchQuery", $formData['quickSearchQuery']);
        $searchResultsPage = SilvercartTools::PageByIdentifierCode("SilvercartSearchResultsPage");

        if (!$searchResultsPage) {
            $searchResultsPage = Translatable::get_one_by_locale('SiteTree', Translatable::default_locale(), "ClassName='SilvercartSearchResultsPage'");

            if ($searchResultsPage) {
                $translatedPage = $searchResultsPage->createTranslation(Translatable::get_current_locale());
                $translatedPage->write();
                $translatedPage->publish('Live', 'Stage');

                $this->getController()->redirect($translatedPage->RelativeLink());
            } else {
                throw new Exception(
                    sprintf(
                        _t('SilvercartPage.NOT_FOUND'),
                        _t('SilvercartSearchResultsPage.SINGULARNAME')
                    )
                );
            }
        } else {
            $this->getController()->redirect($searchResultsPage->RelativeLink());
        }
    }

    /**
     * Set texts for preferences with i18n methods.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 26.05.2011
     */
    public function preferences() {
        $this->preferences['submitButtonTitle'] = _t('SilvercartSearchWidgetForm.SUBMITBUTTONTITLE');
        
        $this->formFields['quickSearchQuery']['title'] = _t('SilvercartSearchWidgetForm.SEARCHLABEL');

        parent::preferences();
    }
}
