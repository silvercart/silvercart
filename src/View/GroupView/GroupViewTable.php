<?php

namespace SilverCart\View\GroupView;

use SilverCart\View\GroupView\GroupViewBase;

/**
 * Provides a listed group view for products and productgroups.
 *
 * @package SilverCart
 * @subpackage View\GroupView
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 *
 * @see SilverCart\View\GroupView\GroupViewBase (base class)
 * @see ProductGroupHolderTable.ss (template file)
 * @see ProductGroupPageTable.ss (template file)
 */
class GroupViewTable extends GroupViewBase
{
    /**
     * main preferences of the group view
     *
     * @return array
     */
    protected function preferences() : array
    {
        $preferences = parent::preferences();
        $preferences['code']            = 'table';
        $preferences['i18n_key']        = GroupViewBase::class . '.TABLE';
        $preferences['i18n_default']    = 'Table';
        return $preferences;
    }
}