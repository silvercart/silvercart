<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverCart\Model\Shipment\Carrier;
use SilverStripe\ORM\DataList;

/**
 * shows the shipping fee matrix.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShippingFeesPage extends MetaNavigationHolder
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShippingFeesPage';
    /**
     * allowed children on site tree
     *
     * @var array
     */
    private static $allowed_children = 'none';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-posts';

    /**
     * Returns all carriers.
     *
     * @return DataList
     */
    public function Carriers() : DataList
    {
        return Carrier::get()->sort(['priority' => 'DESC']);
    }
}