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
        $this->owner->redirect($this->owner->Link());
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
        $this->owner->redirect($this->owner->Link());
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
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function RenderProductGroupHolderGroupView() {
        $elements = array(
            'Elements' => $this->owner->getViewableChildren(),
        );
        $output = $this->owner->customise($elements)->renderWith(
            array(
                $this->getProductGroupHolderTemplateName(),
            )
        );
        return $output;
    }

    /**
     * this is used to render the ProductGroupPage template in dependence on
     * the active group view.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function RenderProductGroupPageGroupView() {
        $elements = array(
            'Elements' => $this->owner->getProducts(),
        );
        $output = $this->owner->customise($elements)->renderWith(
            array(
                $this->getProductGroupPageTemplateName(),
                'Includes/'.$this->getProductGroupPageTemplateName()
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
        return SilvercartGroupViewHandler::getProductGroupPageTemplateNameFor($groupHolderView, 'SilvercartProductGroupHolder');
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
            $groupView = SilvercartGroupViewHandler::getDefaultGroupView();
        }
        return SilvercartGroupViewHandler::getProductGroupPageTemplateNameFor($groupView);
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
}
