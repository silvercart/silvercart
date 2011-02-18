<?php

/**
 * page type for a contact form response
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 21.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartContactFormResponsePage extends Page {

    public static $singular_name = "contact form response page";
    public static $allowed_children = array(
        'none'
    );

}

/**
 * controller peer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 21.10.2010
 */
class SilvercartContactFormResponsePage_Controller extends Page_Controller {

}