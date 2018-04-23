<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\WidgetController;
use SilverStripe\i18n\i18n;

/**
 * PageListWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PageListWidgetController extends WidgetController {

    /**
     * Returns the attributed pages as DataList
     * 
     * @param int $start Limit start
     * @param int $end   Limit end
     *
     * @return ArrayList
     */
    public function getPages($start = null, $end = null) {
        $limit = '';
        if (is_numeric($start) &&
            is_null($end)) {
            $limit = $start . ',999';
        } elseif (is_numeric($start) &&
                  is_numeric($end)) {
            $limit = $start . ',' . $end;
        } elseif (is_null($start) &&
                  is_numeric($end)) {
            $limit = '0,' . $end;
        }
        $pages = $this->getWidget()->Pages(null, 'widgetPriority ASC', '', $limit);
        
        return $pages;
    }

    /**
     * Creates the cache key for this widget.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function WidgetCacheKey() {
        $key = i18n::get_locale();

        if ((int) $key > 0) {
            $permissions = $this->getWidget()->Pages()->map('ID', 'CanView');

            foreach ($permissions as $pageID => $permission) {
                $key .= '_' . $pageID . '-' . ((string) $permission);
            }

            $key .= $this->getWidget()->Pages()->Aggregate('MAX(Last_Edited)');
        }

        return $key;
    }
}