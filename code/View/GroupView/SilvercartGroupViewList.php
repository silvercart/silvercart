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
 * Provides a listed group view for products and productgroups.
 *
 * @package Silvercart
 * @subpackage Groupview
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 14.02.2011
 * @license see license file in modules root directory
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
        $preferences['code']            = 'list';
        $preferences['i18n_key']        = 'SilvercartGroupView.LIST';
        $preferences['i18n_default']    = 'List';
        return $preferences;
    }
}