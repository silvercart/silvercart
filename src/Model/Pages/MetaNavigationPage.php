<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\Pages\MetaNavigationHolder;

/**
 * page type display of terms and conditions or other meta information.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MetaNavigationPage extends MetaNavigationHolder
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartMetaNavigationPage';
    /**
     * list of allowed children page types
     *
     * @var array
     */
    private static $allowed_children = [
        MetaNavigationPage::class,
    ];
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-document';
}