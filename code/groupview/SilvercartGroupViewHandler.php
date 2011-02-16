<?php

/**
 * handles the base logic for product- and productgroup-visualisation
 *
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 14.02.2011
 * @license BSD
 */
class SilvercartGroupViewHandler {

    /**
     * a list of possible group view types
     *
     * @var array
     */
    protected static $groupViews = array();

    /**
     * a list of removed group view types. It is implemented to provide the
     * configuration example in _config.php of silvercart.
     *
     * @var array
     */
    protected static $removedGroupViews = array();

    /**
     * the code of the group view which is choosen by default
     *
     * @var string
     */
    protected static $defaultGroupView = null;

    /**
     * adds a new group view type to the handler.
     *
     * @param string $groupView the class name of the group view to add
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function addGroupView($groupView) {
        if (in_array($groupView, self::$removedGroupViews)) {
            return;
        }
        if (class_exists($groupView)) {
            $gv = new $groupView();
            self::$groupViews[$gv->getCode()] = $groupView;
        }
    }

    /**
     * removes a group view from the handler
     *
     * @param string $groupView the class name of the group view to remove
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function removeGroupView($groupView) {
        if (in_array($groupView, self::$removedGroupViews)) {
            return;
        }
        self::$removedGroupViews[] = $groupView;
        if (in_array($groupView, self::$groupViews)) {
            foreach (self::$groupViews as $index => $value) {
                if ($groupView == $value) {
                    unset (self::$groupViews[$index]);
                }
            }
        }
    }

    /**
     * set the group view to use by default
     *
     * @param string $defaultGroupView the class name of the group view to use by default
     *
     * @return void
     */
    public static function setDefaultGroupView($defaultGroupView = null) {
        if (is_null($defaultGroupView)
         || !in_array($defaultGroupView, self::$groupViews)) {
            foreach (self::$groupViews as $code => $groupView) {
                self::$defaultGroupView = $code;
                return;
            }
        }
        $tmp = array_flip(self::$groupViews);
        self::$defaultGroupView = $tmp[$defaultGroupView];
    }

    /**
     * returns the class name of the default group view
     *
     * @return string
     */
    public static function getDefaultGroupView() {
        return self::$defaultGroupView;
    }

    /**
     * sets the group view to the given type
     *
     * @param string $groupView the code of the group view to set
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.02.2011
     * @see self::$groupViews
     */
    public static function setGroupView($groupView) {
        if (array_key_exists($groupView, self::$groupViews)) {
            Session::set('SilvercartGroupView', $groupView);
            Session::save();
        }
    }

    /**
     * returns all group views
     *
     * @return string
     */
    public static function getGroupViews() {
        return self::$groupViews;
    }

    /**
     * returns the class name of a group view by its code
     *
     * @param string $code the code of the group view
     *
     * @return string
     */
    public static function getGroupView($code) {
        if (array_key_exists($code, self::$groupViews)) {
            return self::$groupViews[$code];
        }
        return false;
    }

    /**
     * return the actual group view
     *
     * @return string
     */
    public static function getActiveGroupView() {
        if (is_null(Session::get('SilvercartGroupView'))) {
            if (is_null(self::getDefaultGroupView())) {
                self::setDefaultGroupView();
            }
            self::setGroupView(self::getDefaultGroupView());
        }
        return Session::get('SilvercartGroupView');
    }

    /**
     * returns the actual group view type as UpperCamelCase
     *
     * @return string
     */
    public static function getActiveGroupViewAsUpperCamelCase() {
        return strtoupper(substr(self::getActiveGroupView(), 0, 1)) . strtolower(substr(self::getActiveGroupView(), 1));
    }
}