<?php

/**
 * Provides a tiled group view for products and productgroups.
 *
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 14.02.2011
 * @license BSD
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
        $preferences['code']    = 'tile';
        $preferences['label']   = _t('GroupView.TILE', 'Tile');
        return $preferences;
    }
}