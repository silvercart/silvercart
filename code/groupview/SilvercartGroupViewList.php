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
 * Provides a listed group view for products and productgroups.
 *
 * @package Silvercart
 * @subpackage Groupview
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 14.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 *
 * @see SilvercartGroupViewBase (base class)
 * @see ProductGroupHolderList.ss (template file)
 * @see ProductGroupPageList.ss (template file)
 */
class SilvercartGroupViewList extends SilvercartGroupViewBase {

    /**
     * main preferences of the group view
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.02.2011
     */
    protected function preferences() {
        $preferences = parent::preferences();
        $preferences['code']    = 'list';
        $preferences['label']   = _t('GroupView.LIST', 'List');
        return $preferences;
    }
}