<?php

namespace SilverCart\Model\Pages;

use Page;

/**
 * FrontPage.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class FrontPage extends Page
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartFrontPage';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-shop';
}