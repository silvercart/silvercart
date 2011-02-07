<?php

/**
 * abstract for an orders shipping address
 * instances of $this cannot be changed by a customer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 22.10.2010
 * @license BSD
 */
class OrderShippingAddress extends OrderAddress {
    public static $singular_name = "order shipping address";
    public static $plural_name = "order shipping addresses";
}

