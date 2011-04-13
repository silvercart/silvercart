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
 * Standard Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Jiri Ripa <jripa@pixeltricks.de>
 * @since 20.09.2010
 * @copyright 2010 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPage extends SiteTree {

    /**
     * extends statics
     *
     * @return array configuration array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.02.11
     */
    public static $db = array(
        'IdentifierCode' => 'VarChar(50)'
    );
    public static $has_one = array(
        'HeaderPicture' => 'Image'
    );

    /**
     * is the centerpiece of every data administration interface in Silverstripe
     *
     * @return FieldSet all related CMS fields
     * @author Jiri Ripa <jripa@pixeltricks.de>
     * @since 15.10.2010
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Content.Main', new TextField('IdentifierCode', 'IdentifierCode'));
        $fields->addFieldToTab('Root.Content.Main', new LabelField('ForIdentifierCode', _t('SilvercartPage.DO_NOT_EDIT', 'Do not edit this field unless you know exectly what you are doing!')));

        return $fields;
    }
}

/**
 * Standard Controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Jiri Ripa <jripa@pixeltricks.de>
 * @since 20.09.2010
 * @copyright 2010 pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPage_Controller extends ContentController {

    /**
     * standard page controller
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.02.2011
     * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
     * @return void
     * @copyright 2010 pixeltricks GmbH
     */
    public function init() {
        if (SilvercartConfig::DefaultLayoutEnabled()) {
            // Require the default layout and its patches only if it is enabled
            Requirements::themedCSS('layout');
            Requirements::insertHeadTags('<!--[if lte IE 7]>');
            Requirements::insertHeadTags('<link href="/silvercart/css/patches/patch_layout.css" rel="stylesheet" type="text/css" />');
            Requirements::insertHeadTags('<![endif]-->');
        }
        Requirements::themedCSS('SilvercartGeneral');
        Requirements::themedCSS('SilvercartProductGroupHolder');
        Requirements::themedCSS('SilvercartProductGroupPage');
        Requirements::themedCSS('SilvercartProductPage');
        Requirements::themedCSS('SilvercartProductPagination');
        Requirements::themedCSS('SilvercartSideBarCart');
        Requirements::themedCSS('SilvercartShoppingCartFull');
        Requirements::javascript("customhtmlform/script/jquery.js");
        Requirements::javascript("silvercart/script/document.ready_scripts.js");
        Requirements::javascript("silvercart/script/jquery.pixeltricks.tools.js");

        $this->registerCustomHtmlForm('SilvercartQuickSearch', new SilvercartQuickSearchForm($this));
        $this->registerCustomHtmlForm('SilvercartQuickLogin', new SilvercartQuickLoginForm($this));

        // check the SilverCart configuration
        $checkConfiguration = true;
        if (array_key_exists('url', $_REQUEST)) {
            if ($_REQUEST['url'] == '/Security/login' || strpos($_REQUEST['url'], 'dev/build') !== false) {
                $checkConfiguration = false;
            }
        }
        if ($checkConfiguration) {
            SilvercartConfig::Check();
        }

        parent::init();
    }

    /**
     * Eigene Zugriffsberechtigungen definieren.
     *
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @return array configuration of API permissions
     * @since 12.10.2010
     */
    public function providePermissions() {
        return array(
            'API_VIEW' => _t('Page.API_VIEW', 'can read objects via the API'),
            'API_CREATE' => _t('Page.API_CREATE', 'can create objects via the API'),
            'API_EDIT' => _t('Page.API_EDIT', 'can edit objects via the API'),
            'API_DELETE' => _t('Page.API_DELETE', 'can delete objects via the API')
        );
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
        $page = DataObject::get_one(
                        'Page',
                        sprintf(
                                '"URLSegment" LIKE \'%s\'',
                                $this->urlParams['URLSegment']
                        )
        );

        return $this->ContextBreadcrumbs($page);
    }

    /**
     * pages with own url rewriting need their breadcrumbs created in a different way
     *
     * @param Controller $context        the current controller
     * @param int        $maxDepth       maximum levels
     * @param bool       $unlinked       link breadcrumbs elements
     * @param bool       $stopAtPageType ???
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
        $address = DataObject::get_by_id($context->getSection(), $this->urlParams['ID']);
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
     * Function similar to Member::currentUser(); Determins if we deal with a
     * registered customer who has opted in. Returns the member object or
     * false.
     *
     * @return mixed Member|boolean(false)
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.11.2010
     * @since 13.05.2011 - replaced logic with call to the appropriate method
     *                     in the SilvercartCustomerRole object (SK).
     */
    public function CurrentRegisteredCustomer() {
        return SilvercartCustomerRole::currentRegisteredCustomer();
    }

    /**
     * This function is replacing the default SilverStripe Logout Form. This form is used to logout the customer and direct
     * the user to the startpage
     *
     * @return null
     *
     * @author Oliver Scheer <oscheer@pixeltricks.de>
     * @since 11.11.2010
     */
    public function logOut() {
        Security::logout(false);
        $frontPage = SilvercartPage_Controller::PageByIdentifierCode();
        Director::redirect($frontPage->RelativeLink());
    }

    /**
     * This function is used to return the current count of shopping Cart positions
     *
     * @return Integer $shoppingCartPositions Anzahl der Positionen im Warenkorb
     *
     * @author Oliver Scheer <oscheer@pixeltricks.de>
     * @since 02.12.2010
     */
    public function getCount() {

        $memberID = Member::currentUserID();
        $member = DataObject::get_by_id("Member", $memberID);
        if ($member) {
            $shoppingCartPositions = DataObject::get("SilvercartShoppingCartPosition", sprintf("`SilvercartShoppingCartID` = '%s'", $member->SilvercartShoppingCartID));
            return Count($shoppingCartPositions);
        }
    }

    /**
     * returns a single page by IdentifierCode
     * used to retrieve links dynamically
     *
     * @param string $identifierCode the classes name
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.2.11
     * @return DataObject | false a single object of the site tree; without param the SilvercartFrontPage will be returned
     */
    public static function PageByIdentifierCode($identifierCode = "SilvercartFrontPage") {
        $page = DataObject::get_one(
            "SiteTree",
            sprintf(
                "`IdentifierCode` = '%s'",
                $identifierCode
            )
        );

        if ($page) {
            return $page;
        } else {
            return false;
        }
    }

    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.02.2011
     */
    public static function PageByIdentifierCodeLink($identifierCode = "SilvercartFrontPage") {
        $page = self::PageByIdentifierCode($identifierCode);
        if ($page === false) {
            return '';
        }
        return $page->Link();
    }

    /**
     * Uses the children of SilvercartProductGroupHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template. This is the default sub-
     * navigation.
     *
     * @param string $identifierCode The code of the parent page.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.03.2011
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
        $items              = array();
        $output             = '';
        $productGroupPage   = $this->PageByIdentifierCode($identifierCode);

        if ($productGroupPage) {
            foreach ($productGroupPage->Children() as $child) {
                if ($child->hasProductsOrChildren()) {
                    $items[] = $child;
                }
            }
            $elements = array(
                'SubElements' => new DataObjectSet($items),
            );
            $output = $this->customise($elements)->renderWith(
                array(
                    'SilvercartSubNavigation',
                )
            );
        }
        return $output;
    }

    /**
     * used to determine weather something should be shown on a template or not
     *
     * @return bool
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.3.2011
     */
    public function showPricesGross() {
        $pricetype = SilvercartConfig::Pricetype();
        if ($pricetype == "gross") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * used to determine weather something should be shown on a template or not
     *
     * @return bool
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.3.2011
     */
    public function showPricesNet() {
        $pricetype = SilvercartConfig::Pricetype();
        if ($pricetype == "net") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return the given number of topseller products as DataObjectSet.
     * 
     * We use caching here, so check the cache first if you don't get the
     * desired results.
     *
     * @param int $nrOfProducts The number of products to return
     *
     * @return mixed DataObjectSet|Boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function getTopsellerProducts($nrOfProducts = 5) {
        $cachekey = 'TopsellerProducts'.$nrOfProducts;
        $cache    = SS_Cache::factory($cachekey);
        $result   = $cache->load($cachekey);

        if ($result) {
            $result = unserialize($result);
        } else {
            $products   = array();
            $sqlQuery   = new SQLQuery();

            $sqlQuery->select = array(
                'SOP.SilvercartProductID',
                'SUM(SOP.Quantity) AS Quantity'
            );
            $sqlQuery->from = array(
                'SilvercartOrderPosition SOP',
                'LEFT JOIN SilvercartProduct SP on SP.ID = SOP.SilvercartProductID'
            );
            $sqlQuery->where = array(
                'SP.isActive = 1'
            );
            $sqlQuery->groupby = array(
                'SOP.SilvercartProductID'
            );
            $sqlQuery->orderby  = 'Quantity DESC';
            $sqlQuery->limit    = $nrOfProducts;

            $result = $sqlQuery->execute();

            foreach ($result as $row) {
                $products[] = DataObject::get_by_id(
                    'SilvercartProduct',
                    $row['SilvercartProductID']
                );
            }
            
            $result = new DataObjectSet($products);
        }

        return $result;
    }

    /**
     * We load the special offers productgroup page here.
     *
     * @param string $groupIdentifier Identifier of the product group
     * @param int    $nrOfProducts    The number of products to return
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.03.2011
     */
    public function getProductGroupItems($groupIdentifier = 'SilvercartOffers', $nrOfProducts = 4) {
        $products = array();

        $records = DB::query(
            sprintf(
                "
                SELECT
                    SilvercartProductID
                FROM
                    (
                        SELECT
                            SilvercartProduct.ID AS SilvercartProductID
                        FROM
                            SilvercartProduct
                        LEFT JOIN
                            SilvercartPage
                        ON
                            SilvercartPage.ID = SilvercartProduct.SilvercartProductGroupID
                        WHERE
                            SilvercartPage.IdentifierCode = '%s'
                    ) AS DirectRelations
                UNION SELECT
                    SilvercartProductID
                FROM
                    (
                        SELECT
                            SP_SPGMP.SilvercartProductID AS SilvercartProductID
                        FROM
                            SilvercartProduct_SilvercartProductGroupMirrorPages AS SP_SPGMP
                        LEFT JOIN
                            SilvercartPage
                        ON
                            SilvercartPage.ID = SP_SPGMP.SilvercartProductGroupPageID
                        WHERE
                            SilvercartPage.IdentifierCode = '%s'
                    ) AS MirrorRelations
                GROUP BY
                    SilvercartProductID
                ORDER BY
                    RAND()
                LIMIT
                    %d
                ",
                $groupIdentifier,
                $groupIdentifier,
                $nrOfProducts
            )
        );

        foreach ($records as $record) {
            $product = DataObject::get_by_id(
                'SilvercartProduct',
                $record['SilvercartProductID']
            );

            if ($product) {
                $products[] = $product;
            }
        }

        $result = new DataObjectSet($products);

        return $result;
    }
}
