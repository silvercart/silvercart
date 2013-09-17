<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright 2012 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License 
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
        $breadcrumbs = parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden);
        if (Controller::curr()->getAction() == 'results') {
            $parts = explode(self::$breadcrumbs_delimiter, $breadcrumbs);
            $downloadHolder = array_pop($parts);
            $parts[] = ("<a href=\"" . $this->Link() . "\">" . $downloadHolder . "</a>");
            $parts[] = _t('SilvercartDownloadPageHolder.SearchResults');
            $breadcrumbs = implode(self::$breadcrumbs_delimiter, $parts);
        }
        return $breadcrumbs;
    }
    
}

/**
 * SilvercartDownloadPageHolder_Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @since 12.07.2012
 * @copyright 2012 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License 
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
     * @return DataObjectSet
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