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
 * Decorator for PageTypes which have grouped views. Provides a group view
 * specific functionality to its decorated owner.
 *
 * @package Silvercart
 * @subpackage Groupview
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 14.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartGroupViewExtension extends DataExtension {

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
     * @return DataObjectSet
     */
    public function getGroupViews() {
        $groupViewArray = array();
        foreach (SilvercartGroupViewHandler::getGroupViews() as $code => $groupView) {
            $groupViewArray[] = new $groupView();
        }
        return new DataObjectSet($groupViewArray);
    }

    /**
     * returns all group views
     *
     * @return DataObjectSet
     */
    public function getGroupHolderViews() {
        $groupViewArray = array();
        foreach (SilvercartGroupViewHandler::getGroupHolderViews() as $code => $groupView) {
            $groupViewArray[] = new $groupView();
        }
        return new DataObjectSet($groupViewArray);
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
     * @return string
     */
    protected function getProductGroupHolderTemplateName() {
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
     * @return string
     */
    protected function getProductGroupPageTemplateName() {
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
