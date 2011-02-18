<?php

/**
 * gathers all product categories
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartProductCategoryHolder extends Page {

    public static $singular_name = "";
    public static $allowed_children = array(
        'SilvercartProductCategoryPage'
    );

}

/**
 * correlated controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license BSD
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartProductCategoryHolder_Controller extends Page_Controller {

}