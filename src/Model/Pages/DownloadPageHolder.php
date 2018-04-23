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
class DownloadPageHolder extends \Page {

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
    private static $allowed_children = array(
        DownloadPage::class,
    );
    
    /**
     * returns the singular name
     * 
     * @return string 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }
    
    /**
     * returns the plural name
     * 
     * @return string 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function plural_name() {
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
    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false) {
        $page = $this;
        $pages = array();

        while ($page
            && $page->exists()
            && (!$maxDepth || count($pages) < $maxDepth)
            && (!$stopAtPageType || $page->ClassName != $stopAtPageType)
        ) {
            if ($showHidden || $page->ShowInMenus || ($page->ID == $this->ID)) {
                $pages[] = $page;
            }

            $page = $page->Parent();
        }
        if (Controller::curr()->getAction() == 'results') {
            $title = new DBText();
            $title->setValue(_t(DownloadPageHolder::class . '.SearchResults', 'Search Results'));
            array_unshift(
                    $pages,
                    new ArrayData(
                            array(
                                'MenuTitle' => $title,
                                'Title'     => $title,
                                'Link'      => '',
                            )
                    )
            );
        }

        return new ArrayList(array_reverse($pages));
    }
    
}