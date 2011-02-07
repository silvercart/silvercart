<?php
/**
 * form definition
 *
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 */
class ArticlePreviewForm extends CustomHtmlForm {
    /**
     * field configuration
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
     * initial field values
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
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