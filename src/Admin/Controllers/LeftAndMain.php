<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Dev\Tools;
use SilverStripe\Admin\LeftAndMain as SilverStripeLeftAndMain;
use SilverStripe\Security\Member;
use function singleton;

/**
 * LeftAndMain extension for SilverCart.
 * Provides some special functions for SilverCarts admin area.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class LeftAndMain extends SilverStripeLeftAndMain
{
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart';
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     */
    protected function init() : void
    {
        parent::init();
        $this->extend('updateInit');
    }
    
    /**
     * Allows user code to hook into ModelAdmin::init() prior to updateInit 
     * being called on extensions.
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     */
    protected function beforeUpdateInit(callable $callback) : void
    {
        $this->beforeExtending('updateInit', $callback);
    }
    
    /**
     * Allows user code to hook into ModelAdmin::getEditForm() prior to 
     * updateEditForm being called on extensions.
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     */
    protected function beforeUpdateEditForm(callable $callback) : void
    {
        $this->beforeExtending('updateEditForm', $callback);
    }

    /**
     * title in the top bar of the CMS
     *
     * @return string
     */
    public function SectionTitle() : string
    {
        $sectionTitle = parent::SectionTitle();
        if (class_exists((string) $this->modelClass)) {
            $sectionTitle = Tools::singular_name_for(singleton($this->modelClass));
        }
        return (string) $sectionTitle;
    }
    
    /**
     * Workaround to hide this class in CMS menu.
     * 
     * @param Member $member Member
     * 
     * @return bool
     */
    public function canView($member = null) : bool
    {
        if (get_class($this) === LeftAndMain::class) {
            return false;
        }
        return (bool) parent::canView($member);
    }
}