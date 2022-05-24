<?php

namespace SilverCart\View\GroupView;

use SilverCart\View\GroupView\GroupViewHandler;
use SilverStripe\Control\Director;
use SilverStripe\View\ViewableData;

/**
 * Provides the base logic for a group view type.
 *
 * @package SilverCart
 * @subpackage View\GroupView
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GroupViewBase extends ViewableData
{
    /**
     * Short code to use for the view
     *
     * @var string
     */
    protected $Code = '';
    /**
     * Image/icon to use for the view
     *
     * @var sring
     */
    protected $Image = '';
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
    protected $defaultPreferences = [
        'code' => '',
        'image' => '',
        'image_active' => '',
        'image_inactive' => '',
        'i18n_key' => '',
        'i18n_default' => '',
    ];
    /**
     * Extended preferences
     *
     * @var array 
     */
    protected $preferences = [];

    /**
     * default constructor. reads the preferences from extendet group views and
     * initializes the group view object.
     *
     * @param array $record      array of field values
     * @param bool  $isSingleton true if this is a singleton() object
     *
     * @return void
     */
    public function  __construct()
    {
        $this->preferences = $this->preferences();
        $this->setCode($this->preferences['code']);
    }

    /**
     * provides the default preferences. Must be overwritten by extensions.
     *
     * @return array
     *
     * @see self::$defaultPreferences
     */
    protected function preferences() : array
    {
        return $this->defaultPreferences;
    }

    /**
     * returns the group views code
     *
     * @return string
     */
    public function getCode() : string
    {
        return $this->Code;
    }

    /**
     * sets the group views code
     *
     * @param string $Code the group views code
     *
     * @return GroupViewBase
     */
    public function setCode(string $Code) : GroupViewBase
    {
        $this->Code = $Code;
        return $this;
    }

    /**
     * returns the group views action
     *
     * @return string
     */
    public function getAction() : string
    {
        return "switchGroupView/{$this->getCode()}";
    }

    /**
     * returns the group views image
     *
     * @return string
     * 
     * @global string $project the name of the userdefined project
     */
    public function Image() : string
    {
        global $project;
        if (empty($this->Image)) {
            if ($this->isActive()) {
                $highlightStatus = 'active';
            } else {
                $highlightStatus = 'inactive';
            }
            if (is_file(Director::baseFolder() . '/' . $this->preferences['image_' . $highlightStatus])) {
                $this->preferences['image'] = Director::absoluteBaseURL() . '/' . $this->preferences['image_' . $highlightStatus];
            } elseif (is_file(Director::baseFolder() . '/' . $project . '/img/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png')) {
                $this->preferences['image'] = Director::absoluteBaseURL() . $project . '/img/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png';
            } elseif (is_file(Director::publicFolder() . '/' . RESOURCES_DIR . '/vendor/silvercart/silvercart/client/img/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png')) {
                $this->preferences['image'] = Director::absoluteBaseURL() . '/' . RESOURCES_DIR . '/vendor/silvercart/silvercart/client/img/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png';
            } else {
                $this->preferences['image'] = '';
            }

            $this->setImage($this->preferences['image']);
        }
        return (string) $this->Image;
    }

    /**
     * sets the group views image
     *
     * @param string $Image the group views image
     *
     * @return GroupViewBase
     */
    public function setImage(string $Image) : GroupViewBase
    {
        $this->Image = $Image;
        return $this;
    }

    /**
     * returns the group views label
     *
     * @return string
     */
    public function getLabel() : string
    {
        return _t($this->preferences['i18n_key'], $this->preferences['i18n_default']);
    }

    /**
     * returns, wether the group view is active or not
     *
     * @return bool
     */
    public function getActive() : bool
    {
        if (is_null($this->active)) {
            $this->setActive($this->Code == GroupViewHandler::getActiveGroupView());
        }
        return (bool) $this->active;
    }

    /**
     * sets, wether the group view is active or not
     *
     * @param bool $active activity of the group view
     *
     * @return GroupViewBase
     */
    public function setActive(bool $active) : GroupViewBase
    {
        $this->active = $active;
        return $this;
    }

    /**
     * returns, wether the group view is active or not
     *
     * @return bool
     */
    public function getActiveHolder() : bool
    {
        if (is_null($this->activeHolder)) {
            $this->setActiveHolder($this->Code == GroupViewHandler::getActiveGroupHolderView());
        }
        return (bool) $this->activeHolder;
    }

    /**
     * sets, wether the group view is active or not
     *
     * @param bool $activeHolder activity of the group view
     *
     * @return GroupViewBase
     */
    public function setActiveHolder(bool $activeHolder) : GroupViewBase
    {
        $this->activeHolder = $activeHolder;
        return $this;
    }

    /**
     * returns, wether the group view is active or not
     *
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->getActive();
    }

    /**
     * returns, wether the group view is active or not
     *
     * @return bool
     */
    public function isActiveHolder() : bool
    {
        return $this->getActiveHolder();
    }
}