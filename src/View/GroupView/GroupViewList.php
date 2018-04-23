<?php

namespace SilverCart\View\GroupView;

use SilverCart\View\GroupView\GroupViewBase;

/**
 * Provides a listed group view for products and productgroups.
 *
 * @package SilverCart
 * @subpackage View_GroupView
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 *
 * @see SilverCart\View\GroupView\GroupViewBase (base class)
 * @see ProductGroupHolderList.ss (template file)
 * @see ProductGroupPageList.ss (template file)
 */
class GroupViewList extends GroupViewBase {

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
        $preferences['i18n_key']        = GroupViewBase::class . '.LIST';
        $preferences['i18n_default']    = 'List';
        return $preferences;
    }
}