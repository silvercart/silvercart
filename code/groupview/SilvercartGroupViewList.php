<?php

/**
 * Provides a listed group view for products and productgroups.
 *
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 14.02.2011
 * @license BSD
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