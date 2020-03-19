<?php

namespace SilverCart\Extensions\GoogleSitemaps;

use SilverCart\Model\Pages\ProductGroupPage;
use SilverCart\Model\Product\Product;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\DataList;
use SilverStripe\Versioned\Versioned;

/**
 * Extension for Wilr\GoogleSitemaps\GoogleSitemap.
 *
 * @package SilverCart
 * @subpackage Extensions\GoogleSitemaps
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 18.03.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GoogleSitemapExtension extends Extension
{
    /**
     * Alters the Google Sitemap $list for the given $class.
     * 
     * @param DataList $list  List to alter
     * @param string   $class Class
     * 
     * @return void
     */
    public function alterDataList(DataList &$list, string $class) : void
    {
        if ($class === Product::class) {
            // removes all products with invalid product groups (not related or not published)
            $pgpTable = ProductGroupPage::singleton()->stageTable(ProductGroupPage::config()->table_name, Versioned::LIVE);
            $list = $list
                    ->leftJoin($pgpTable, "PGP.ID = ProductGroupID", 'PGP')
                    ->where('PGP.ID IS NOT NULL');
        }
    }
}