<?php

/**
 * Child of customer area; overview of all addresses;
 *
 * @copyright 2010 pixeltricks GmbH
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 18.10.2010
 * @license BSD
 */
class AddressHolder extends Page {

    public static $singular_name = "";
    public static $can_be_root = false;
    public static $allowed_children = array(
        "AddressPage"
    );

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        return $fields;
    }

}

/**
 * Controller Class
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 18.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class AddressHolder_Controller extends Page_Controller {

    /**
     * execute these statements on object call
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public function init() {
        parent::init();
    }

}