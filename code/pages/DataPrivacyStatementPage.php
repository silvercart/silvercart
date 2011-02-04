<?php

/**
 * page type display of terms and conditions
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 15.11.10
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class DataPrivacyStatementPage extends Page {

    public static $singular_name = "privacy policy page";
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
 * @since 15.11.2010
 */
class DataPrivacyStatementPage_Controller extends Page_Controller {

}