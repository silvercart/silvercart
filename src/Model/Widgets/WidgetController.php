<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use WidgetSets\Controllers\WidgetSetWidgetController;

/**
 * Widget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetController extends WidgetSetWidgetController
{
    /**
     * returns a page by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function PageByIdentifierCode($identifierCode = "SilvercartFrontPage")
    {
        return Tools::PageByIdentifierCode($identifierCode);
    }
    
    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function PageByIdentifierCodeLink($identifierCode = "SilvercartFrontPage")
    {
        return Tools::PageByIdentifierCodeLink($identifierCode);
    }
}