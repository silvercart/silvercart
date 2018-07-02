<?php

namespace SilverCart\View\GroupView;

use SilverCart\Dev\Tools;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\DropdownField;

/**
 * Handles the base logic for product- and productgroup-visualisation.
 *
 * @package SilverCart
 * @subpackage View_GroupView
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GroupViewHandler {

    /**
     * a list of possible group view types
     *
     * @var array
     */
    protected static $groupViews = array();

    /**
     * a list of possible group view types
     *
     * @var array
     */
    protected static $groupHolderViews = array();

    /**
     * a list of removed group view types. It is implemented to provide the
     * configuration example in _config.php of silvercart.
     *
     * @var array
     */
    protected static $removedGroupViews = array();

    /**
     * a list of removed group view types. It is implemented to provide the
     * configuration example in _config.php of silvercart.
     *
     * @var array
     */
    protected static $removedGroupHolderViews = array();

    /**
     * the code of the group view which is choosen by default
     *
     * @var string
     */
    protected static $defaultGroupView = null;

    /**
     * the code of the group view which is choosen by default
     *
     * @var string
     */
    protected static $defaultGroupHolderView = null;

    /**
     * adds a new group view type for product lists to the handler.
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
     * adds a new group view type for product group lists to the handler.
     *
     * @param string $groupHolderView the class name of the group view to add
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function addGroupHolderView($groupHolderView) {
        if (in_array($groupHolderView, self::$removedGroupHolderViews)) {
            return;
        }
        if (class_exists($groupHolderView)) {
            $gv = new $groupHolderView();
            self::$groupHolderViews[$gv->getCode()] = $groupHolderView;
        }
    }

    /**
     * removes a group view for product lists from the handler
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
     * removes a group view for product group lists from the handler
     *
     * @param string $groupHolderView the class name of the group view to remove
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    public static function removeGroupHolderView($groupHolderView) {
        if (in_array($groupHolderView, self::$removedGroupHolderViews)) {
            return;
        }
        self::$removedGroupHolderViews[] = $groupHolderView;
        if (in_array($groupHolderView, self::$groupHolderViews)) {
            foreach (self::$groupHolderViews as $index => $value) {
                if ($groupHolderView == $value) {
                    unset (self::$groupHolderViews[$index]);
                }
            }
        }
    }

    /**
     * set the group view to use by default for product lists
     *
     * @param string $defaultGroupView the class name of the group view to use by default
     *
     * @return void
     */
    public static function setDefaultGroupView($defaultGroupView = null) {
        if (array_key_exists($defaultGroupView, self::$groupViews)) {
            self::$defaultGroupView = $defaultGroupView;
        } elseif (in_array($defaultGroupView, self::$groupViews)) {
            $tmp = array_flip(self::$groupViews);
            self::$defaultGroupView = $tmp[$defaultGroupView];
        } else {
            if (is_null($defaultGroupView)
             || !in_array($defaultGroupView, self::$groupViews)) {
                foreach (self::$groupViews as $code => $groupView) {
                    self::$defaultGroupView = $code;
                    return;
                }
                self::addGroupView($defaultGroupView);
            }
            $tmp = array_flip(self::$groupViews);
            self::$defaultGroupView = $tmp[$defaultGroupView];
        }
    }

    /**
     * set the group view to use by default for product group lists
     *
     * @param string $defaultGroupHolderView the class name of the group view to use by default
     *
     * @return void
     */
    public static function setDefaultGroupHolderView($defaultGroupHolderView = null) {
        if (is_null($defaultGroupHolderView)
         || !in_array($defaultGroupHolderView, self::$groupHolderViews)) {
            foreach (self::$groupHolderViews as $code => $groupHolderView) {
                self::$defaultGroupHolderView = $code;
                return;
            }
            self::addGroupHolderView($defaultGroupHolderView);
        }
        $tmp = array_flip(self::$groupHolderViews);
        self::$defaultGroupHolderView = $tmp[$defaultGroupHolderView];
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
     * returns the class name of the default group view
     *
     * @return string
     */
    public static function getDefaultGroupViewInherited() {
        $controller       = @Controller::curr();
        $defaultGroupView = self::$defaultGroupView;

        if ($controller instanceof Controller &&
            $controller->hasMethod('getDefaultGroupViewInherited')) {
            $defaultGroupView = $controller->getDefaultGroupViewInherited();
        }

        return $defaultGroupView;
    }

    /**
     * returns the class name of the default group view
     *
     * @return string
     */
    public static function getDefaultGroupHolderView() {
        return self::$defaultGroupHolderView;
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
            Tools::Session()->set('SilvercartGroupView', $groupView);
            Tools::saveSession();
        }
    }

    /**
     * sets the group view to the given type
     *
     * @param string $groupHolderView the code of the group view to set
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.02.2011
     * @see self::$groupHolderViews
     */
    public static function setGroupHolderView($groupHolderView) {
        if (array_key_exists($groupHolderView, self::$groupHolderViews)) {
            Tools::Session()->set('SilvercartGroupHolderView', $groupHolderView);
            Tools::saveSession();
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
     * returns all group views
     *
     * @return string
     */
    public static function getGroupHolderViews() {
        return self::$groupHolderViews;
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
     * returns the class name of a group view by its code
     *
     * @param string $code the code of the group view
     *
     * @return string
     */
    public static function getGroupHolderView($code) {
        if (array_key_exists($code, self::$groupHolderViews)) {
            return self::$groupHolderViews[$code];
        }
        return false;
    }

    /**
     * return the actual group view
     *
     * @return string
     */
    public static function getActiveGroupView() {
        $controller = @Controller::curr();

        if ($controller) {
            $isGroupViewAllowed = $controller->isGroupViewAllowed(Tools::Session()->get('SilvercartGroupView'));

            if (is_null(Tools::Session()->get('SilvercartGroupView')) ||
                !$isGroupViewAllowed) {

                if (is_null(self::getDefaultGroupViewInherited())) {
                    if ($isGroupViewAllowed) {
                        self::setDefaultGroupView();
                    } else {
                        self::setDefaultGroupView($controller->getDefaultGroupViewInherited());
                    }
                } else {
                    self::setDefaultGroupView(self::getDefaultGroupViewInherited());
                }
                self::setGroupView(self::getDefaultGroupView());
            }
        } else {
            self::setDefaultGroupView();
        }
        return Tools::Session()->get('SilvercartGroupView');
    }

    /**
     * return the actual group view
     *
     * @return string
     */
    public static function getActiveGroupHolderView() {
        if (is_null(Tools::Session()->get('SilvercartGroupHolderView'))) {
            if (is_null(self::getDefaultGroupHolderView())) {
                self::setDefaultGroupHolderView();
            }
            self::setGroupHolderView(self::getDefaultGroupHolderView());
        }
        return Tools::Session()->get('SilvercartGroupHolderView');
    }

    /**
     * returns the actual group view type as UpperCamelCase
     *
     * @return string
     */
    public static function getActiveGroupViewAsUpperCamelCase() {
        return strtoupper(substr(self::getActiveGroupView(), 0, 1)) . strtolower(substr(self::getActiveGroupView(), 1));
    }

    /**
     * returns the actual group view type as UpperCamelCase
     *
     * @return string
     */
    public static function getActiveGroupHolderViewAsUpperCamelCase() {
        return strtoupper(substr(self::getActiveGroupHolderView(), 0, 1)) . strtolower(substr(self::getActiveGroupHolderView(), 1));
    }
    
    /**
     * Creates and returns a dropdown field to choose an available GroupView
     *
     * @param string $name        Name of the field
     * @param string $title       Title of the field
     * @param string $value       Value of the field
     * @param string $emptyString String to use for the empty value
     * 
     * @return DropdownField 
     */
    public static function getGroupViewDropdownField($name, $title, $value = '', $emptyString = null) {
        $defaultGroupviewSource = array();
        $groupViews             = self::getGroupViews();
        if (!is_null($emptyString)) {
            $defaultGroupviewSource[''] = $emptyString;
        }
        foreach ($groupViews as $code => $classname) {
            $gv = new $classname();
            $defaultGroupviewSource[$code] = $gv->getLabel();
        }
        return new DropdownField($name, $title, $defaultGroupviewSource);
    }

    /**
     * returns the required template name
     * 
     * @param string $groupView    GroupView to get template name for
     * @param string $templateBase Base template name to use
     *
     * @return string
     */
    public static function getProductGroupPageTemplateNameFor($groupView, $templateBase = 'ProductGroupPage') {
        $productGroupPageTemplateName = 'SilverCart/View/GroupView/' . $templateBase . ucfirst(strtolower($groupView));
        return $productGroupPageTemplateName;
    }
}