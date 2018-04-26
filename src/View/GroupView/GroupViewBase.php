<?php

namespace SilverCart\View\GroupView;

use SilverCart\View\GroupView\GroupViewHandler;
use SilverStripe\Control\Director;
use SilverStripe\View\ViewableData;

/**
 * Provides the base logic for a group view type.
 *
 * @package SilverCart
 * @subpackage View_GroupView
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GroupViewBase extends ViewableData {

    /**
     * Short code to use for the view
     *
     * @var string
     */
    protected $Code;
    
    /**
     * Image/icon to use for the view
     *
     * @var sring
     */
    protected $Image;

    /**
     * indicates whether the view is active
     *
     * @var bool 
     */
    protected $active = null;

    /**
     * indicates whether the view is the active one for holders
     *
     * @var bool 
     */
    protected $activeHolder = null;

    /**
     * Default preferences
     *
     * @var array 
     */
    protected $defaultPreferences = array(
        'code' => '',
        'image' => '',
        'image_active' => '',
        'image_inactive' => '',
        'i18n_key' => '',
        'i18n_default' => '',
    );
    
    /**
     * Extended preferences
     *
     * @var array 
     */
    protected $preferences = array();

    /**
     * default constructor. reads the preferences from extendet group views and
     * initializes the group view object.
     *
     * @param array $record      array of field values
     * @param bool  $isSingleton true if this is a singleton() object
     *
     * @return void
     *
     * @global string $project the name of the userdefined project
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public function  __construct() {
        $this->preferences = $this->preferences();
        $this->setCode($this->preferences['code']);
    }

    /**
     * provides the default preferences. Must be overwritten by extensions.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     * @see self::$defaultPreferences
     */
    protected function preferences() {
        return $this->defaultPreferences;
    }

    /**
     * returns the group views code
     *
     * @return string
     */
    public function getCode() {
        return $this->Code;
    }

    /**
     * sets the group views code
     *
     * @param string $Code the group views code
     *
     * @return void
     */
    public function setCode($Code) {
        $this->Code = $Code;
    }

    /**
     * returns the group views image
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public function Image() {
        global $project;

        if (!$this->Image) {
            if ($this->isActive()) {
                $highlightStatus = 'active';
            } else {
                $highlightStatus = 'inactive';
            }
            
            if (is_file(Director::baseFolder() . '/' . $this->preferences['image_' . $highlightStatus])) {
                $this->preferences['image'] = Director::absoluteBaseURL() . '/' . $this->preferences['image_' . $highlightStatus];
            } elseif (is_file(Director::baseFolder() . '/' . $project . '/img/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png')) {
                $this->preferences['image'] = Director::absoluteBaseURL() . $project . '/img/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png';
            } elseif (is_file(Director::publicFolder() . '/resources/vendor/silvercart/silvercart/client/img/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png')) {
                $this->preferences['image'] = Director::absoluteBaseURL() . 'resources/vendor/silvercart/silvercart/client/img/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png';
            } else {
                $this->preferences['image'] = '';
            }

            $this->setImage($this->preferences['image']);
        }
        
        return $this->Image;
    }

    /**
     * sets the group views image
     *
     * @param string $Image the group views image
     *
     * @return void
     */
    public function setImage($Image) {
        $this->Image = $Image;
    }

    /**
     * returns the group views label
     *
     * @return string
     */
    public function getLabel() {
        return _t($this->preferences['i18n_key'], $this->preferences['i18n_default']);;
    }

    /**
     * returns, wether the group view is active or not
     *
     * @return bool
     */
    public function getActive() {
        if (is_null($this->active)) {
            $this->setActive($this->Code == GroupViewHandler::getActiveGroupView());
        }
        return $this->active;
    }

    /**
     * sets, wether the group view is active or not
     *
     * @param bool $active activity of the group view
     *
     * @return void
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /**
     * returns, wether the group view is active or not
     *
     * @return bool
     */
    public function getActiveHolder() {
        if (is_null($this->activeHolder)) {
            $this->setActiveHolder($this->Code == GroupViewHandler::getActiveGroupHolderView());
        }
        return $this->activeHolder;
    }

    /**
     * sets, wether the group view is active or not
     *
     * @param bool $activeHolder activity of the group view
     *
     * @return void
     */
    public function setActiveHolder($activeHolder) {
        $this->activeHolder = $activeHolder;
    }

    /**
     * returns, wether the group view is active or not
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public function isActive() {
        return $this->getActive();
    }

    /**
     * returns, wether the group view is active or not
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public function isActiveHolder() {
        return $this->getActiveHolder();
    }

}