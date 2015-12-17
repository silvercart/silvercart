<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * SilvercartDownloadPageHolder 
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory 
 */
class SilvercartDownloadPageHolder extends Page {
    
    /**
     * allowed child pages in site tree
     *
     * @var array
     */
    public static $allowed_children = array(
      'SilvercartDownloadPage',  
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
        return SilvercartTools::singular_name_for($this);
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
        return SilvercartTools::plural_name_for($this);
    }
    
    /**
     * Adds the part for 'download search' to the breadcrumbs. Sets the link for
     * The default action in breadcrumbs.
     *
     * @param int  $maxDepth       maximum levels
     * @param bool $unlinked       link breadcrumbs elements
     * @param bool $stopAtPageType name of PageType to stop at
     * @param bool $showHidden     show pages that will not show in menus
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.06.2011
     */
    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $page = $this;
        $pages = array();

        while(
            $page  
            && (!$maxDepth || count($pages) < $maxDepth) 
            && (!$stopAtPageType || $page->ClassName != $stopAtPageType)
        ) {
            if($showHidden || $page->ShowInMenus || ($page->ID == $this->ID)) { 
                $pages[] = $page;
            }

            $page = $page->Parent;
        }

        if (Controller::curr()->getAction() == 'results') {
            $title = new Text();
            $title->setValue(_t('SilvercartDownloadPageHolder.SearchResults'));
            array_unshift(
                    $pages,
                    new ArrayData(
                            array(
                                'MenuTitle' => $title,
                                'Title' => $title,
                                'Link'  => '',
                            )
                    )
            );
        }

        $template = new SSViewer('BreadcrumbsTemplate');

        return $template->process($this->customise(new ArrayData(array(
            'Pages' => new ArrayList(array_reverse($pages))
        ))));
    }
    
}

/**
 * SilvercartDownloadPageHolder_Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory 
 */
class SilvercartDownloadPageHolder_Controller extends Page_Controller {
    
    /**
     * Allowed actions
     * 
     * @var array
     */
    public static $allowed_actions = array(
        'results',
    );

    /**
     * Init
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2013
     */
    public function init() {
        parent::init();
        $this->registerCustomHtmlForm('SilvercartDownloadSearchForm', new SilvercartDownloadSearchForm($this));
    }
    
    /**
     * Action to reder the search results
     * 
     * @param SS_HTTPRequest $request Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.09.2013
     */
    public function results(SS_HTTPRequest $request) {
        $results = SilvercartDownloadSearchForm::get_current_results();
        if (is_null($results)) {
            $this->redirect($this->Link());
        }
        return $this->render();
    }
    
    /**
     * Returns the search results
     * 
     * @return DataList
     */
    public function getSearchResults() {
        return SilvercartDownloadSearchForm::get_current_results();
    }
    
    /**
     * Returns the search query
     * 
     * @return string
     */
    public function getSearchQuery() {
        return SilvercartDownloadSearchForm::get_current_query();
    }
    
}