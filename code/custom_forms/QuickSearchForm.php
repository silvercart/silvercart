<?php

/**
 * Beschreibung des Formulars.
 *
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 */
class QuickSearchForm extends CustomHtmlForm {

    /**
     * Enthaelt die zu pruefenden und zu verarbeitenden Formularfelder.
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
     * Wird ausgefuehrt, wenn nach dem Senden des Formulars keine Validierungs-
     * fehler aufgetreten sind.
     *
     * @param SS_HTTPRequest $data     session data
     * @param Form           $form     the form object
     * @param array          $formData CustomHTMLForms Session data
     *
     * @return array to be rendered in the controller
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Oliver Scheer <oscheer@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function submitSuccess($data, $form, $formData) {

        Session::set("searchQuery", $formData['quickSearchQuery']);
        Director::redirect('/suchergebnisse');

    }
}