<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\OrderAddress;

/**
 * abstract for an orders shipping address.
 * instances of $this cannot be changed by a customer.
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class OrderShippingAddress extends OrderAddress
{
    /**
     * API access is allowed for this object
     *
     * @var string
     */
    private static $api_access = true;

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * Returns the translated plural name of the object.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);  
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member current member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canView($member = null) : bool
    {
        return $this->Order()->canView($member);
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member current member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canEdit($member = null) : bool
    {
        return $this->Order()->canEdit($member);
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member current member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.07.2012
     */
    public function canDelete($member = null) : bool
    {
        return $this->Order()->canDelete($member);
    }
    
    /**
     * Indicates that this is the shipping address.
     * 
     * @return bool
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function isShippingAddress() : bool
    {
        return true;
    }
}