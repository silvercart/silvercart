<?php

namespace SilverCart\Model\Pages;

use SilverCart\Forms\DownloadSearchForm;
use SilverCart\Model\Pages\Page as SilverCartPage;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * DownloadPageHolder Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DownloadPageHolderController extends \PageController
{
    /**
     * Allowed actions
     * 
     * @var array
     */
    private static $allowed_actions = [
        'DownloadSearchForm',
        'results',
    ];

    /**
     * Returns the DownloadSearchForm.
     * 
     * @return DownloadSearchForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function DownloadSearchForm() : DownloadSearchForm
    {
        return DownloadSearchForm::create($this);
    }
    
    /**
     * Action to reder the search results
     * 
     * @param HTTPRequest $request Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2013
     */
    public function results(HTTPRequest $request)
    {
        $results = DownloadSearchForm::get_current_results();
        if (is_null($results)) {
            $this->redirect($this->Link());
        }
        return $this->render();
    }
    
    /**
     * Returns the search results-
     * Alias for self::getSearchResults().
     * 
     * @return \SilverStripe\ORM\DataList
     */
    public function getFiles()
    {
        return $this->getSearchResults();
    }
    
    /**
     * Returns the search results
     * 
     * @return \SilverStripe\ORM\DataList
     */
    public function getSearchResults()
    {
        return DownloadSearchForm::get_current_results();
    }
    
    /**
     * Returns the search results count.
     * 
     * @return int
     */
    public function getSearchResultsCount() : int
    {
        $results = $this->getSearchResults();
        if (!$results) {
            $count = 0;
        } else {
            $count = $results->count();
        }
        return $count;
    }
    
    /**
     * Returns the search query
     * 
     * @return string
     */
    public function getSearchQuery() : string
    {
        return (string) DownloadSearchForm::get_current_query();
    }
    
    /**
     * Uses the children of MetaNavigationHolder to render a subnavigation
     * with the SilverCart/Model/Pages/Includes/SubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getSubNavigation($identifierCode = SilverCartPage::IDENTIFIER_PRODUCT_GROUP_HOLDER) : DBHTMLText
    {
        $subNavigation = null;
        $parent        = $this->data()->Parent();
        while (is_null($subNavigation)
             && $parent->exists()
        ) {
            if ($parent instanceof MyAccountHolder) {
                $ctrl          = ModelAsController::controller_for($parent);
                $subNavigation = $ctrl->getSubNavigation($identifierCode);
            }
            $parent = $parent->Parent();
        }
        if (is_null($subNavigation)) {
            $subNavigation = parent::getSubNavigation($identifierCode);
        }
        return $subNavigation;
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
        return $this->data()->EnableDownloadSearch();
    }
}