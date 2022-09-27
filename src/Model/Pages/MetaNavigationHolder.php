<?php

namespace SilverCart\Model\Pages;

use Page;

/**
 * This site is not visible in the frontend.
 * Its purpose is to gather the meta navigation sites in the backend for better usability.
 * Now a shop admin has a correspondence between front end site order and backend tree structure.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MetaNavigationHolder extends Page
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartMetaNavigationHolder';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-tree';
}