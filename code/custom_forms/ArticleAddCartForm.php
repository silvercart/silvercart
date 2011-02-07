<?php

/**
 * form definition
 *
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 */
class ArticleAddCartForm extends CustomHtmlForm {

    /**
     * field configuration
     *
     * @var array
     */
    protected $formFields = array(
        'articleAmount' => array(
            'type' => 'TextField',
            'title' => 'Anzahl',
            'value' => '1',
            'checkRequirements' => array(
                'isFilledIn' => true,
                'isNumbersOnly' => true
            )
        )
    );
    /**
     * preferences
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 21.12.2010
     */
    protected $preferences = array(
        'submitButtonTitle' => 'in den Warenkorb',
        'doJsValidationScrolling' => false
    );

    /**
     * Set initial form values
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 02.02.2010
     */
    protected function fillInFieldValues() {
        $this->formFields['articleAmount']['title'] = _t('Article.QUANTITY');
        $this->preferences['submitButtonTitle'] = _t('Article.ADD_TO_CART');
    }

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
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
            Director::redirect($backLink, 302);
        } else {
            Director::redirect($backLink, 302);
            exit();
        }
    }

}