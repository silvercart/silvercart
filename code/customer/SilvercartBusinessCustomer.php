<?php

/**
 * abstract for a business customer which has own attributes.
 * They are treated differently when it comes to billing
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license BSD
 */
class SilvercartBusinessCustomer extends Member {

    public static $db = array(
        'UmsatzsteuerID' => 'VarChar'
    );
}
