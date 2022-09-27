<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\Pages\MetaNavigationHolder;

/**
 * SiteMapPage.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SiteMapPage extends MetaNavigationHolder
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartSiteMapPage';
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
    private static $icon_class = 'font-icon-flow-tree';
}