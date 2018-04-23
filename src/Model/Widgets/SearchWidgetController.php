<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\WidgetController;
use SilverCart\Model\Widgets\SearchWidgetForm;

/**
 * SearchWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchWidgetController extends WidgetController {
    
    /**
     * Returns the SearchWidgetForm.
     * 
     * @return SearchWidgetForm
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.11.2017
     */
    public function SearchWidgetForm() {
        $form = new SearchWidgetForm($this);
        return $form;
    }

}