<?php
/**
 * Beschreibung des Formulars.
 *
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 */
class ArticleAddCartForm extends CustomHtmlForm {
    /**
     * Enthaelt die zu pruefenden und zu verarbeitenden Formularfelder.
     *
     * @var array
     */
    protected $formFields = array(
        'articleAmount' => array(
            'type'              => 'TextField',
            'title'             => 'Anzahl',
            'value'             => '1',
            'checkRequirements' => array(
                'isFilledIn'    => true,
                'isNumbersOnly' => true
            )
        )
    );

    /**
     * Voreinstellungen
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.12.2010
     */
    protected $preferences = array(
        'submitButtonTitle'         => 'in den Warenkorb',
        'doJsValidationScrolling'   => false
    );

    /**
     * Wird ausgefuehrt, wenn nach dem Senden des Formulars keine Validierungs-
     * fehler aufgetreten sind.
     *
     * @param SS_HTTPRequest $data     session data
     * @param Form           $form     form object
     * @param array          $formData CustomHTMLForms session data
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @return void
     * @since 23.10.2010
     */
    protected function submitSuccess($data, $form, $formData) {
        $backLink = $this->controller->Link();

        if (isset($formData['backLink'])) {
            $backLink = $formData['backLink'];
        }

        if (ShoppingCart::addArticle($formData)) {
            Director::redirect($backLink,302);
        } else {
            Director::redirect($backLink,302);
            exit();
        }
    }
}