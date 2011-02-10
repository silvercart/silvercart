<?php

/**
 * Increment a cart positions quantity;
 * only a button
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 09.02.2011
 * @license BSD
 */
class DecrementPositionQuantityForm extends CustomHtmlForm {

    /**
     * form settings, mainly submit button´s name
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 09.02.2011
     * @return void
     */
    protected $preferences = array(
        'submitButtonTitle' => '-',
        'doJsValidationScrolling' => false
    );

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

        if ($formData['positionID']) {

            //check if the position belongs to this user. Malicious people could manipulate it.
            $member = Member::currentUser();
            $position = DataObject::get_by_id('ShoppingCartPosition', $formData['positionID']);
            if ($position && ($member->shoppingCart()->ID == $position->shoppingCartID)) {
                if ($position->Quantity == 1) {
                    $position->delete();
                } else {
                    $position->Quantity--;
                    $position->write();
                }
                Director::redirect($this->controller->Link());
            }
        }
    }

}

