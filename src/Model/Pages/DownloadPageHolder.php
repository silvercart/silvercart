<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\DownloadPage;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\View\ArrayData;

/**
 * DownloadPageHolder.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DownloadPageHolder extends \Page
{
    /**
     * Configuration property to enable the download search.
     *
     * @var bool
     */
    private static $enable_download_search = true;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartDownloadPageHolder';
    /**
     * allowed child pages in site tree
     *
     * @var array
     */
    private static $allowed_children = [
        DownloadPage::class,
    ];
    
    /**
     * returns the singular name
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * returns the plural name
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * Returns a list of breadcrumbs for the current page.
     *
     * @param int $maxDepth The maximum depth to traverse.
     * @param boolean|string $stopAtPageType ClassName of a page to stop the upwards traversal.
     * @param boolean $showHidden Include pages marked with the attribute ShowInMenus = 0
     *
     * @return ArrayList
    */
    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false) : ArrayList
    {
        $page  = $this;
        $pages = [];
        while ($page
            && $page->exists()
            && (!$maxDepth
             || count($pages) < $maxDepth)
            && (!$stopAtPageType
             || $page->ClassName != $stopAtPageType)
        ) {
            if ($showHidden
             || $page->ShowInMenus
             || ($page->ID == $this->ID)
            ) {
                $pages[] = $page;
            }
            $page = $page->Parent();
        }
        if (Controller::curr()->getAction() == 'results') {
            $title = DBText::create()
                    ->setValue(_t(DownloadPageHolder::class . '.SearchResults', 'Search Results'));
            array_unshift(
                    $pages,
                    ArrayData::create([
                        'MenuTitle' => $title,
                        'Title'     => $title,
                        'Link'      => $this->Link('results'),
                    ])
            );
        }
        return ArrayList::create(array_reverse($pages));
    }
    
    /**
     * Returns whether to enable the download search or not.
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.08.2019
     */
    public function EnableDownloadSearch() : bool
    {
        return (bool) $this->config()->enable_download_search;
    }
}