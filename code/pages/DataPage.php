<?php

/**
 * Shows customerdata + edit
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class DataPage extends Page {

    public static $singular_name = "Meine Daten";
    public static $can_be_root = false;

}

/**
 * correlating controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class DataPage_Controller extends Page_Controller {

    /**
     * Initialisiert das Formularobjekt.
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 23.10.2010
     * @return void
     */
    public function init() {
        $this->registerCustomHtmlForm('EditProfileForm', new EditProfileForm($this));
        parent::init();
    }
}