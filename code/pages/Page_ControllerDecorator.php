<?php

/**
 * adds functionality to the Page_Controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 08.02.2011
 * @license BSD
 */
class Page_ControllerDecorator extends DataObjectDecorator {

    /**
     * extends init method
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.02.11
     * @return void
     */
    public function init() {
        $this->owner->registerCustomHtmlForm('QuickSearch', new QuickSearchForm($this->owner));
        $this->owner->registerCustomHtmlForm('QuickLogin', new QuickLoginForm($this->owner));
        Requirements::themedCSS('layout');
        Requirements::javascript("pixeltricks_module/script/jquery.js");
        Requirements::javascript("silvercart/js/startupScripts.js");
        Requirements::javascript("silvercart/js/jquery.pixeltricks.tools.js");
    }

    /**
     * determin weather a cart is filled or empty; usefull for template conditional
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 1.11.2010
     * @return boolean is cart filled?
     */
    public function isFilledCart() {
        $customer = Member::currentUser();

        if ($customer && $customer->hasMethod('shoppingCart') && $customer->shoppingCart()->positions()->Count() > 0) {
            return true;
        } else {
            return false;
        }
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
                                $this->owner->urlParams['URLSegment']
                        )
        );

        return $this->owner->ContextBreadcrumbs($page);
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
        $address = DataObject::get_by_id($context->getSection(), $this->owner->urlParams['ID']);
        $parts[] = $address->singular_name();

        $i = 0;
        while (
        $page
        && (!$maxDepth || sizeof($parts) < $maxDepth)
        && (!$stopAtPageType || $page->ClassName != $stopAtPageType)
        ) {
            if ($showHidden || $page->ShowInMenus || ($page->ID == $this->owner->ID)) {
                if ($page->URLSegment == 'home') {
                    $hasHome = true;
                }
                if (($page->ID == $this->owner->ID) || $unlinked) {
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
     * replace Page contentwith Array values
     *
     * @return bool
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @since  01.10.2010
     */
    protected function replaceContent() {
        $member = Member::currentUser();
        if ($member) {
            $email = $member->Email;

            $this->owner->Content = str_replace(
                            array(
                                '__EMAIL__'
                            ),
                            array(
                                $email
                            ),
                            $this->owner->Content
            );
        }
    }

    /**
     * a template method
     * Function similar to Member::currentUser(); Determins if we deal with a registered customer
     *
     * @return Member|false Costomer-Object or false
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 9.11.10
     */
    public function CurrentRegisteredCustomer() {
        $member = Member::currentUser();
        if ($member) {
            if ($member->ClassName != "AnonymousCustomer") {
                return $member;
            }
        } else {
            return false;
        }
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
        Director::redirect("home/");
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
            $shoppingCartPositions = DataObject::get("ShoppingCartPosition", "\"shoppingCartID\" = '$member->shoppingCartID'");
            return Count($shoppingCartPositions);
        }
    }
}

