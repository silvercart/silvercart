<?php

namespace SilverCart\View\GroupView;

use SilverCart\View\GroupView\GroupViewBase;

/**
 * Provides a tiled group view for products and productgroups.
 *
 * @package SilverCart
 * @subpackage View_GroupView
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 *
 * @see SilverCart\View\GroupView\GroupViewBase (base class)
 * @see ProductGroupHolderTile.ss (template file)
 * @see ProductGroupPageTile.ss (template file)
 */
class GroupViewTile extends GroupViewBase {

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
        $preferences['code']            = 'tile';
        $preferences['i18n_key']        = GroupViewBase::class . '.TILE';
        $preferences['i18n_default']    = 'Tile';
        return $preferences;
    }
}