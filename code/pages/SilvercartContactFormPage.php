<?php

/**
 * show an process a contact form
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartContactFormPage extends Page {

    public static $singular_name = "contact form page";
    public static $allowed_children = array(
        'SilvercartContactFormResponsePage'
    );

}

/**
 * Controller of this page type
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartContactFormPage_Controller extends Page_Controller {

    /**
     * initialisation of the form object
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     * @return void
     */
    public function init() {
        $this->registerCustomHtmlForm('SilvercartContactForm', new SilvercartContactForm($this));
        parent::init();
    }
}