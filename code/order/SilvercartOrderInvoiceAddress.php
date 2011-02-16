<?php

/**
 * abstract for an orders invoice address
 * instances of $this cannot be changed by a customer
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 22.10.2010
 * @license BSD
 */
class SilvercartOrderInvoiceAddress extends SilvercartOrderAddress {
    public static $singular_name = "order invoice address";
    public static $plural_name = "order invoice addresses";
}

