<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * holder for customers private area
 * 
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartMyAccountHolder extends Page {

    public static $singular_name = "Account holder";
    public static $allowed_children = array(
        "SilvercartDataPage",
        "SilvercartOrderHolder",
        "SilvercartAddressHolder"
    );

    /**
     * manipulates the Breadcrumbs
     *
     * @param int  $maxDepth       maximum levels
     * @param bool $unlinked       link breadcrumbs elements
     * @param bool $stopAtPageType name of PageType to stop at
     * @param bool $showHidden     show pages that will not show in menus
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2011
     */
    public function  Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        return parent::Breadcrumbs($maxDepth, $unlinked, 'SilvercartFrontPage', true);
    }

}

/**
 * correlating controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartMyAccountHolder_Controller extends Page_Controller {

    protected $breadcrumbElementID;

    /**
     * statements to be called on object initialisation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.11.2010
     * @return void
     */
    public function init() {
        Session::clear("redirect"); //if customer has been to the checkout yet this is set to direct him back to the checkout after address editing

        parent::init();

        $this->registerCustomHtmlForm('SilvercartLoginForm', new SilvercartQuickLoginForm($this));
    }

    /**
     * Uses the children of SilvercartMyAccountHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template.
     *
     * @return string
     */
    public function getSubNavigation() {
        $elements = array(
            'SubElements' => $this->PageByIdentifierCode('SilvercartMyAccountHolder')->Children(),
        );
        $output = $this->customise($elements)->renderWith(
            array(
                'SilvercartSubNavigation',
            )
        );
        return $output;
    }

    /**
     * template method for breadcrumbs
     * show breadcrumbs for pages which show a DataObject determined via URL parameter ID
     * see _config.php
     *
     * @return string html for breadcrumbs
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getBreadcrumbs() {
        $page = $this->PageByIdentifierCode($this->IdentifierCode);

        return $this->ContextBreadcrumbs($page, 20, false, 'SilvercartFrontPage', true);
    }

    /**
     * pages with own url rewriting need their breadcrumbs created in a different way
     *
     * @param Controller $context        the current controller
     * @param int        $maxDepth       maximum levels
     * @param bool       $unlinked       link breadcrumbs elements
     * @param bool       $stopAtPageType name of PageType to stop at
     * @param bool       $showHidden     show pages that will not show in menus
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     * @return string html for breadcrumbs
     */
    public function ContextBreadcrumbs($context, $maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $page = $context;
        $parts = array();

        // Get address type
        $address = DataObject::get_by_id($context->getSection(), $this->getBreadcrumbElementID());
        $parts[] = $address->i18n_singular_name();

        $i = 0;
        while (
            $page
            && (!$maxDepth || sizeof($parts) < $maxDepth)
            && (!$stopAtPageType || $page->ClassName != $stopAtPageType)
        ) {
            if ($showHidden || $page->ShowInMenus || ($page->ID == $this->ID)) {
                if ($page->URLSegment == 'home') {
                    $hasHome = true;
                }
                if (($page->ID == $this->ID) || $unlinked) {
                    $parts[] = Convert::raw2xml($page->Title);
                } else {
                    $parts[] = ("<a href=\"" . $page->Link() . "\">" . Convert::raw2xml($page->Title) . "</a>");
                }
            }
            $page = $page->Parent;
        }

        return implode(SiteTree::$breadcrumbs_delimiter, array_reverse($parts));
    }

    /**
     * returns the BreadcrumbElementID
     *
     * @return int
     */
    public function getBreadcrumbElementID() {
        return $this->breadcrumbElementID;
    }

    /**
     * sets the BreadcrumbElementID
     *
     * @param int $breadcrumbElementID BreadcrumbElementID
     *
     * @return void
     */
    public function setBreadcrumbElementID($breadcrumbElementID) {
        $this->breadcrumbElementID = $breadcrumbElementID;
    }
}
