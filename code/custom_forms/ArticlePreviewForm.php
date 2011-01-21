<?php
/**
 * Beschreibung des Formulars.
 *
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 */
class ArticlePreviewForm extends CustomHtmlForm {
    /**
     * Enthaelt die zu pruefenden und zu verarbeitenden Formularfelder.
     *
     * @var array
     */
    protected $formFields = array
    (
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
     * form settings, mainly submit button´s name
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 1.11.2010
     * @return void
     */
    protected $preferences = array(
        'submitButtonTitle'         => 'in den Warenkorb',
        'doJsValidationScrolling'   => false
    );

    /**
     * Setzt Initialwerte in Formularfeldern.
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function fillInFieldValues() {
    }

    /**
     * Wird ausgefuehrt, wenn nach dem Senden des Formulars keine Validierungs-
     * fehler aufgetreten sind.
     *
     * @param SS_HTTPRequest $data     session data
     * @param Form           $form     form object
     * @param array          $formData CustomHTMLForms session data
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
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