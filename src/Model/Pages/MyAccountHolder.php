<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\FrontPage;

/**
 * holder for customers private area.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MyAccountHolder extends \Page {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartMyAccountHolder';
    
    /**
     * Icon to display in CMS site tree
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/my_account_holder-file.gif";
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return Tools::plural_name_for($this); 
    }

    /**
     * manipulates the Breadcrumbs
     *
     * @param int    $maxDepth       maximum levels
     * @param bool   $unlinked       link breadcrumbs elements
     * @param bool   $stopAtPageType name of PageType to stop at
     * @param bool   $showHidden     show pages that will not show in menus
     * @param string $delimiter      delimiter string to use
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2011
     */
    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false, $delimiter = '&raquo;') {
        if ($stopAtPageType == false) {
            $stopAtPageType = FrontPage::class;
        }
        return parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden, $delimiter);
    }

}