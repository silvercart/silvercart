<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Dev\Tools;

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
class LeftAndMain extends \SilverStripe\Admin\LeftAndMain
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
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.09.2018
     */
    protected function init()
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.09.2018
     */
    protected function beforeUpdateInit($callback)
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.09.2018
     */
    protected function beforeUpdateEditForm($callback)
    {
        $this->beforeExtending('updateEditForm', $callback);
    }

    /**
     * title in the top bar of the CMS
     *
     * @return string 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.02.2013
     */
    public function SectionTitle()
    {
        $sectionTitle = parent::SectionTitle();
        if (class_exists($this->modelClass)) {
            $sectionTitle = Tools::singular_name_for(singleton($this->modelClass));
        }
        return $sectionTitle;
    }
    
    /**
     * Workaround to hide this class in CMS menu.
     * 
     * @param Member $member Member
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.10.2017
     */
    public function canView($member = null)
    {
        if (get_class($this) === LeftAndMain::class) {
            return false;
        }
        return parent::canView($member);
    }
}