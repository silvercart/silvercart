<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Backend
 */

/**
 * Modify the Silverstripe CMS section
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 22.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartMain extends DataObjectDecorator {
    
    public static $allowed_actions = array(
        'createsitetreetranslation',
        'publishsitetree',
    );

    /**
     * Here we load additional stylesheets so that we can modify the look of
     * the original Silverstripe CMS.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.03.2011
     */
    public function OnBeforeInit() {
        Requirements::css('silvercart/css/screen/silvercart_main.css');
    }
    
    /**
     * This action will create a translation template for all pages of the 
     * SiteTree for the given language.
     * 
     * @param array $request Request data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public function createsitetreetranslation($request) {
        // Protect against CSRF on destructive action
        if (!SecurityToken::inst()->checkRequest($this->owner->getRequest())) {
            return $this->owner->httpError(400);
        }

        $langCode               = Convert::raw2sql($request['NewTransLang']);
        $this->owner->Locale    = $langCode;

        SilvercartRequireDefaultRecords::doTranslateSiteTree($langCode);
        
        $url = sprintf(
                "%s/root/?locale=%s", 
                $this->owner->Link('show'),
                $langCode
        );
        
        Director::redirect($url);
        FormResponse::add(sprintf('window.location.href = "%s";', $url));
        return FormResponse::respond();
    }
    
    /**
     * This action will publish all pages for the given language
     * 
     * @param array $request Request data
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.05.2012
     */
    public function publishsitetree($request) {
        // Protect against CSRF on destructive action
        if (!SecurityToken::inst()->checkRequest($this->owner->getRequest())) {
            return $this->owner->httpError(400);
        }

        $langCode               = Convert::raw2sql($request['Locale']);
        $this->owner->Locale    = $langCode;

        SilvercartRequireDefaultRecords::doPublishSiteTree($langCode);
        
        $url = sprintf(
                "%s/root/?locale=%s", 
                $this->owner->Link('show'),
                $langCode
        );
        
        Director::redirect($url);
        FormResponse::add(sprintf('window.location.href = "%s";', $url));
        return FormResponse::respond();
    }
}