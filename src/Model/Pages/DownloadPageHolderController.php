<?php

namespace SilverCart\Model\Pages;

use SilverCart\Forms\DownloadSearchForm;
use SilverStripe\Control\HTTPRequest;

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
class DownloadPageHolderController extends \PageController {
    
    /**
     * Allowed actions
     * 
     * @var array
     */
    private static $allowed_actions = array(
        'DownloadSearchForm',
        'results',
    );

    /**
     * Returns the DownloadSearchForm.
     * 
     * @return DownloadSearchForm
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.11.2017
     */
    public function DownloadSearchForm() {
        $form = new DownloadSearchForm($this);
        return $form;
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
    public function results(HTTPRequest $request) {
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
    public function getFiles() {
        return $this->getSearchResults();
    }
    
    /**
     * Returns the search results
     * 
     * @return \SilverStripe\ORM\DataList
     */
    public function getSearchResults() {
        return DownloadSearchForm::get_current_results();
    }
    
    /**
     * Returns the search results count.
     * 
     * @return int
     */
    public function getSearchResultsCount() {
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
    public function getSearchQuery() {
        return DownloadSearchForm::get_current_query();
    }
    
}