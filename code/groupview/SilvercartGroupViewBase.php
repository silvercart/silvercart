<?php

/**
 * provides the base logic for a group view type.
 *
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 14.11.2002
 * @license BSD
 */
class SilvercartGroupViewBase extends DataObject {

    protected $Code;
    protected $Image;
    protected $Label;

    protected $active = null;
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
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public function isActive() {
        return $this->getActive();
    }

}