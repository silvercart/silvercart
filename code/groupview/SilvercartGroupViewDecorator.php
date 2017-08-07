<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Groupview
 */

/**
 * Decorator for PageTypes which have grouped views. Provides a group view
 * specific functionality to its decorated owner.
 *
 * @package Silvercart
 * @subpackage Groupview
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 14.02.2011
 * @license see license file in modules root directory
 */
class SilvercartGroupViewDecorator extends DataExtension {

    /**
     * add switchGroupView to allowed_actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'switchGroupView',
        'switchGroupHolderView',
    );

    /**
     * returns all group views
     *
     * @return ArrayList
     */
    public function getGroupViews() {
        $groupViewArray = array();
        foreach (SilvercartGroupViewHandler::getGroupViews() as $code => $groupView) {
            $groupViewArray[] = new $groupView();
        }
        return new ArrayList($groupViewArray);
    }

    /**
     * returns all group views
     *
     * @return ArrayList
     */
    public function getGroupHolderViews() {
        $groupViewArray = array();
        foreach (SilvercartGroupViewHandler::getGroupHolderViews() as $code => $groupView) {
            $groupViewArray[] = new $groupView();
        }
        return new ArrayList($groupViewArray);
    }

    /**
     * checkes, whether more than $count group views are existant.
     *
     * @param int $count count
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function hasMoreGroupViewsThan($count) {
        $hasMoreGroupViewsThan = false;
        if (!$this->owner->getUseOnlyDefaultGroupViewInherited()) {
            $hasMoreGroupViewsThan = count(SilvercartGroupViewHandler::getGroupViews()) > $count;
        }
        return $hasMoreGroupViewsThan;
    }

    /**
     * checkes, whether more than $count group views are existant.
     *
     * @param int $count count
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function hasMoreGroupHolderViewsThan($count) {
        $hasMoreGroupHolderViewsThan = false;
        if (!$this->owner->getUseOnlyDefaultGroupHolderViewInherited()) {
            $hasMoreGroupHolderViewsThan = count(SilvercartGroupViewHandler::getGroupHolderViews()) > $count;
        }
        return $hasMoreGroupHolderViewsThan;
    }

    /**
     * switches the group view to the via URL parameter 'ID' given type (if
     * existant)
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.02.2011
     * @see self::$productGroupViews
     */
    public function switchGroupView() {
        if (array_key_exists('ID', $this->owner->urlParams)) {
            SilvercartGroupViewHandler::setGroupView($this->owner->urlParams['ID']);
        }
        $this->owner->redirectBack();
    }

    /**
     * switches the group view to the via URL parameter 'ID' given type (if
     * existant)
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.02.2011
     * @see self::$productGroupHolderViews
     */
    public function switchGroupHolderView() {
        if (array_key_exists('ID', $this->owner->urlParams)) {
            SilvercartGroupViewHandler::setGroupHolderView($this->owner->urlParams['ID']);
        }
        $this->owner->redirectBack();
    }

    /**
     * returns the code of the active group view
     *
     * @return string
     */
    public function getActiveGroupView() {
        return SilvercartGroupViewHandler::getActiveGroupView();
    }

    /**
     * this is used to render the ProductGroupHolder template in dependence on
     * the active group view.
     *
     * @param string $templateBase Base name for the template to use.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function RenderProductGroupHolderGroupView($templateBase = 'SilvercartProductGroupHolder') {
        $elements = array(
            'Elements' => $this->owner->getViewableChildren(),
        );
        $output = $this->owner->customise($elements)->renderWith(
            array(
                $this->getProductGroupHolderTemplateName($templateBase),
            )
        );
        return $output;
    }

    /**
     * this is used to render the ProductGroupPage template in dependence on
     * the active group view.
     *
     * @param string $templateBase Base name for the template to use.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function RenderProductGroupPageGroupView($templateBase = 'SilvercartProductGroupPage') {
        $elements = array(
            'Elements' => $this->owner->getProducts(),
        );
        $output = $this->owner->customise($elements)->renderWith(
            array(
                $this->getProductGroupPageTemplateName($templateBase),
                'Includes/'.$this->getProductGroupPageTemplateName($templateBase)
            )
        );
        return $output;
    }

    /**
     * returns the required ProductGroupHolder template name required by the
     * decorators owner in dependence on the active group view.
     *
     * @param string $templateBase Base name for the template to use.
     * 
     * @return string
     */
    public function getProductGroupHolderTemplateName($templateBase = 'SilvercartProductGroupHolder') {
        $groupHolderView = SilvercartGroupViewHandler::getActiveGroupHolderView();
        if (!$this->owner->isGroupHolderViewAllowed($groupHolderView)) {
            $groupHolderView = $this->owner->getDefaultGroupHolderViewInherited();
        }
        if (empty($groupHolderView)) {
            $groupHolderView = SilvercartGroupViewHandler::getDefaultGroupHolderView();
        }
        return SilvercartGroupViewHandler::getProductGroupPageTemplateNameFor($groupHolderView, $templateBase);
    }

    /**
     * returns the required ProductGroupPage template name required by the
     * decorators owner in dependence on the active group view.
     *
     * @param string $templateBase Base name for the template to use.
     *
     * @return string
     */
    public function getProductGroupPageTemplateName($templateBase = 'SilvercartProductGroupPage') {
        $groupView = SilvercartGroupViewHandler::getActiveGroupView();
        if (!$this->owner->isGroupViewAllowed($groupView)) {
            $groupView = $this->owner->getDefaultGroupViewInherited();
        }
        if (empty($groupView)) {
            $groupView = SilvercartGroupViewHandler::getDefaultGroupViewInherited();
        }
        return SilvercartGroupViewHandler::getProductGroupPageTemplateNameFor($groupView, $templateBase);
    }

    /**
     * returns the required CartFormName required by the decorators owner in
     * dependence on the active group view.
     *
     * @return string
     */
    public function getCartFormName() {
        return SilvercartGroupViewHandler::getCartFormNameFor(SilvercartGroupViewHandler::getActiveGroupView());
    }
    
    /**
     * Checks whether the given group view is allowed to render for this group
     *
     * @param string $groupView GroupView code
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.06.2012
     */
    public function isGroupViewAllowed($groupView) {
        $groupViewAllowed = true;
        if ($this->getUseOnlyDefaultGroupViewInherited() &&
            $groupView != $this->getDefaultGroupViewInherited()) {
            $groupViewAllowed = false;
        }
        return $groupViewAllowed;
    }

    /**
     * Returns the inherited DefaultGroupView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getDefaultGroupViewInherited($context = null) {
        if (is_null($context)) {
            $context = $this->owner;
        }
        $defaultGroupView = $context->DefaultGroupView;
        if (empty($defaultGroupView) ||
            SilvercartGroupViewHandler::getGroupView($defaultGroupView) === false) {
            if ($context->Parent() instanceof SilvercartProductGroupPage) {
                $defaultGroupView = $this->getDefaultGroupViewInherited($context->Parent());
            } else if ($context->Parent() instanceof SilvercartProductGroupHolder) {
                $defaultGroupView = $this->getDefaultGroupViewInherited($context->Parent());
            } else {
                $defaultGroupView = SilvercartGroupViewHandler::getDefaultGroupView();
            }
        }
        return $defaultGroupView;
    }
    
    /**
     * Returns the inherited UseOnlyDefaultGroupView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getUseOnlyDefaultGroupViewInherited($context = null) {
        if (is_null($context)) {
            $context = $this->owner;
        }
        $useOnlyDefaultGroupView = $context->UseOnlyDefaultGroupView;
        if ($useOnlyDefaultGroupView == 'inherit') {
            if ($context->Parent() instanceof SilvercartProductGroupPage) {
                $useOnlyDefaultGroupView = $this->getUseOnlyDefaultGroupViewInherited($context->Parent());
            } else if ($context->Parent() instanceof SilvercartProductGroupHolder) {
                $useOnlyDefaultGroupView = $this->getUseOnlyDefaultGroupViewInherited($context->Parent());
            } else {
                $useOnlyDefaultGroupView = false;
            }
        } elseif ($useOnlyDefaultGroupView == 'yes') {
            $useOnlyDefaultGroupView = true;
        } else {
            $useOnlyDefaultGroupView = false;
        }
        return $useOnlyDefaultGroupView;
    }
    
    /**
     * Checks whether the given group view is allowed to render for this group
     *
     * @param string $groupHolderView GroupHolderView code
     * 
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.06.2012
     */
    public function isGroupHolderViewAllowed($groupHolderView) {
        $groupHolderViewAllowed = true;
        if ($this->getUseOnlyDefaultGroupHolderViewInherited() &&
            $groupHolderView != $this->getDefaultGroupHolderViewInherited()) {
            $groupHolderViewAllowed = false;
        }
        return $groupHolderViewAllowed;
    }

    /**
     * Returns the inherited DefaultGroupHolderView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getDefaultGroupHolderViewInherited($context = null) {
        if (is_null($context)) {
            $context = $this->owner;
        }
        $defaultGroupHolderView = $context->DefaultGroupHolderView;
        if (empty($defaultGroupHolderView) ||
            SilvercartGroupViewHandler::getGroupHolderView($defaultGroupHolderView) === false) {
            if ($context->Parent() instanceof SilvercartProductGroupPage ||
                $context->Parent() instanceof SilvercartProductGroupHolder) {
                $defaultGroupHolderView = $this->getDefaultGroupHolderViewInherited($context->Parent());
            } else {
                $defaultGroupHolderView = SilvercartGroupViewHandler::getDefaultGroupHolderView();
            }
        }
        return $defaultGroupHolderView;
    }
    
    /**
     * Returns the inherited UseOnlyDefaultGroupHolderView
     *
     * @param SilvercartProductGroupPage $context Context
     * 
     * @return string
     */
    public function getUseOnlyDefaultGroupHolderViewInherited($context = null) {
        if (is_null($context)) {
            $context = $this->owner;
        }
        $useOnlyDefaultGroupHolderView = $context->UseOnlyDefaultGroupHolderView;
        if ($useOnlyDefaultGroupHolderView == 'inherit') {
            if ($context->Parent() instanceof SilvercartProductGroupPage ||
                $context->Parent() instanceof SilvercartProductGroupHolder) {
                $useOnlyDefaultGroupHolderView = $this->getUseOnlyDefaultGroupHolderViewInherited($context->Parent());
            } else {
                $useOnlyDefaultGroupHolderView = false;
            }
        } elseif ($useOnlyDefaultGroupHolderView == 'yes') {
            $useOnlyDefaultGroupHolderView = true;
        } else {
            $useOnlyDefaultGroupHolderView = false;
        }
        return $useOnlyDefaultGroupHolderView;
    }
}
