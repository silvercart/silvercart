<?php

/**
 * form definition
 *
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 */
class SilvercartQuickSearchForm extends CustomHtmlForm {

    /**
     * form field definition
     *
     * @var array
     */
    protected $formFields = array
        (
        'quickSearchQuery' => array(
            'type' => 'TextField',
            'title' => '',
            'value' => '',
            'checkRequirements' => array(
            )
        )
    );

    /**
     * form settings, mainly submit button´s name
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.11.2010
     * @return void
     */
    protected $preferences = array(
        'submitButtonTitle' => ''
    );

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return array to be rendered in the controller
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Oliver Scheer <oscheer@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function submitSuccess($data, $form, $formData) {

        Session::set("searchQuery", $formData['quickSearchQuery']);
        Director::redirect(sprintf("/%s", _t('SilvercartSearchResultsPage.URL_SEGMENT')));

    }
}