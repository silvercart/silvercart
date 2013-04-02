<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @since 22.03.2011
 * @license see license file in modules root directory
 */
class SilvercartMain extends DataExtension {
    
    /**
     * List of allowed actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'createsitetreetranslation',
        'publishsitetree',
    );
    
    /**
     * Some extra routines after init
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.06.2012
     */
    public function onAfterInit() {
        Requirements::css('silvercart/css/backend/SilvercartMain.css');
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
        $this->owner->redirect($url);
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
        
        $this->owner->redirect($url);
        FormResponse::add(sprintf('window.location.href = "%s";', $url));
        return FormResponse::respond();
    }
}