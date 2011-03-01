<?php
/*
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
    protected $Label;

    protected $active = null;
    protected $activeHolder = null;
    protected $defaultPreferences = array(
        'code' => '',
        'image' => '',
        'label' => '',
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
        global $project;
        $this->preferences = $this->preferences();
        if (!array_key_exists('image', $this->preferences)
         || empty ($this->preferences['image'])) {
            if (is_file(Director::baseFolder() . '/' . $project . '/images/icons/32x32_group_view_' . $this->preferences['code'] . '.png')) {
                $this->preferences['image'] = '/' . $project . '/images/icons/32x32_group_view_' . $this->preferences['code'] . '.png';
            } elseif (is_file(Director::baseFolder() . '/silvercart/images/icons/32x32_group_view_' . $this->preferences['code'] . '.png')) {
                $this->preferences['image'] = '/silvercart/images/icons/32x32_group_view_' . $this->preferences['code'] . '.png';
            } else {
                $this->preferences['image'] = '';
            }
        }
        $this->setCode($this->preferences['code']);
        $this->setImage($this->preferences['image']);
        $this->setLabel($this->preferences['label']);
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
     */
    public function getImage() {
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
        return $this->Label;
    }

    /**
     * sets the group views label
     *
     * @param string $Label the group views label
     *
     * @return void
     */
    public function setLabel($Label) {
        $this->Label = $Label;
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