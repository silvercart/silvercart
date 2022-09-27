<?php

namespace SilverCart\Model\Pages;

use SilverCart\Model\Pages\MetaNavigationHolder;

/**
 * Show and process a revocation form.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class RevocationFormPage extends MetaNavigationHolder
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartRevocationFormPage';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-alt';
}