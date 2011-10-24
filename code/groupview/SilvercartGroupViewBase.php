<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @subpackage Groupview
 */

/**
 * provides the base logic for a group view type.
 *
 * @package Silvercart
 * @subpackage Groupview
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 14.11.2002
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartGroupViewBase extends DataObject {

    protected $Code;
    protected $Image;

    protected $active = null;
    protected $activeHolder = null;
    protected $defaultPreferences = array(
        'code' => '',
        'image' => '',
        'i18n_key' => '',
        'i18n_default' => '',
    );
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
    public function  __construct($record = null, $isSingleton = null) {
        parent::__construct($record, $isSingleton);
        
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

            if (is_file(Director::baseFolder() . '/' . $project . '/images/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png')) {
                $this->preferences['image'] = Director::baseURL() . '/' . $project . '/images/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png';
            } elseif (is_file(Director::baseFolder() . '/silvercart/images/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png')) {
                $this->preferences['image'] = Director::baseURL() . '/silvercart/images/icons/20x20_group_view_' . $this->preferences['code'] . '_' . $highlightStatus . '.png';
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
            $this->setActive($this->Code == SilvercartGroupViewHandler::getActiveGroupView());
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
            $this->setActiveHolder($this->Code == SilvercartGroupViewHandler::getActiveGroupHolderView());
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