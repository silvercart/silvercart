<?php

namespace SilverCart\Model\Pages;

use SilverCart\Forms\RevocationForm;
use SilverCart\Model\Pages\MetaNavigationHolderController;

/**
 * RevocationFormPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RevocationFormPageController extends MetaNavigationHolderController {
    
    /**
     * Allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = array(
        'RevocationForm',
        'success',
    );

    /**
     * Returns the RevocationForm.
     *
     * @return RevocationForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function RevocationForm() {
        $form = new RevocationForm($this);
        return $form;
    }
}