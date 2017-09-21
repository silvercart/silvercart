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
 * Provides a tiled group view for products and productgroups.
 *
 * @package Silvercart
 * @subpackage Groupview
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 14.02.2011
 * @license see license file in modules root directory
 *
 * @see SilvercartGroupViewBase (base class)
 * @see ProductGroupHolderTile.ss (template file)
 * @see ProductGroupPageTile.ss (template file)
 */
class SilvercartGroupViewTile extends SilvercartGroupViewBase {

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
        $preferences['i18n_key']        = 'SilvercartGroupView.TILE';
        $preferences['i18n_default']    = 'Tile';
        return $preferences;
    }
}