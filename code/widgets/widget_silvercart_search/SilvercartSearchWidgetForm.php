<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * form definition
 *
 * @package Silvercart
 * @subpackage Widgets
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license see license file in modules root directory
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
     * @since 26.05.2011
     */
    public function preferences() {
        $this->preferences['submitButtonTitle'] = _t('SilvercartSearchWidgetForm.SUBMITBUTTONTITLE');
        
        $this->formFields['quickSearchQuery']['title'] = _t('SilvercartSearchWidgetForm.SEARCHLABEL');

        parent::preferences();
    }
}
