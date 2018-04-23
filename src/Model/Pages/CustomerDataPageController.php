<?php

namespace SilverCart\Model\Pages;

use SilverCart\Forms\EditProfileForm;
use SilverCart\Model\Pages\MyAccountHolderController;

/**
 * CustomerDataPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CustomerDataPageController extends MyAccountHolderController {
    
    /**
     * List of allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'EditProfileForm',
    );

    /**
     * Returns the EditProfileForm.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.11.2017
     */
    public function EditProfileForm() {
        $form = new EditProfileForm($this);
        return $form;
    }
}