<?php

/**
 * Standard Controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Jiri Ripa <jripa@pixeltricks.de>
 * @since 20.09.2010
 * @copyright 2010 pixeltricks GmbH
 * @license BSD
 */
class SilvercartPage extends SiteTree {

    /**
     * extends statics
     *
     * @return array configuration array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.02.11
     */
    public function extraStatics() {
        return array(
            'db' => array(
                'HeaderPicture' => 'Image'
            )
        );
    }

    /**
     * is the centerpiece of every data administration interface in Silverstripe
     *
     * @param FieldSet $fields cms fields fieldset
     *
     * @return FieldSet all related CMS fields
     * @author Jiri Ripa <jripa@pixeltricks.de>
     * @since 15.10.2010
     */
    public function updateCMSFields(FieldSet $fields) {
        $fields->addFieldToTab('Root.Content.Main', new FileIFrameField('HeaderPicture', _t('SilvercartPage.HEADERPICTURE', 'header picture')));
    }

}

/**
 * Standard Controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>, Jiri Ripa <jripa@pixeltricks.de>
 * @since 20.09.2010
 * @copyright 2010 pixeltricks GmbH
 * @license BSD
 */
class SilvercartPage_Controller extends ContentController {

    /**
     * standard page controller
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.02.2011
     * @license LGPL
     * @return void
     * @copyright 2010 pixeltricks GmbH
     */
    public function init() {
        Requirements::themedCSS('layout');
        Requirements::themedCSS('SilvercartProductGroupHolder');
        Requirements::themedCSS('SilvercartProductGroupPage');
        Requirements::themedCSS('SilvercartProductPage');
        Requirements::themedCSS('SilvercartSideBarCart');
        Requirements::javascript("pixeltricks_module/script/jquery.js");
        Requirements::javascript("silvercart/script/document.ready_scripts.js");
        Requirements::javascript("silvercart/script/jquery.pixeltricks.tools.js");

        $this->registerCustomHtmlForm('SilvercartQuickSearch', new SilvercartQuickSearchForm($this));
        $this->registerCustomHtmlForm('SilvercartQuickLogin', new SilvercartQuickLoginForm($this));

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

            $this->Content = str_replace(
                            array(
                                '__EMAIL__'
                            ),
                            array(
                                $email
                            ),
                            $this->Content
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
            if ($member->ClassName != "SilvercartAnonymousCustomer") {
                return $member;
            }
        } else {
            return false;
        }
    }

    /**
     * Liefert true oder false, abhängig vom eingeloggten Benutzer
     *
     * @return Boolean true/false Je nach aktuellem Benutzer
     *
     * @author Oliver Scheer <oscheer@pixeltricks.de>
     * @since 01.12.2010
     * @copyright 2010 pixeltricks GmbH
     * @license BSD
     */
    public function MemberInformation() {

        if (Member::currentUser() && Member::currentUser()->ClassName != 'SilvercartAnonymousCustomer') {
            return true;
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
            $shoppingCartPositions = DataObject::get("SilvercartShoppingCartPosition", sprintf("`shoppingCartID` = '%s'",$member->SilvercartShoppingCartID));
            return Count($shoppingCartPositions);
        }
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

        if ($customer && $customer->hasMethod('SilvercartShoppingCart') && $customer->SilvercartShoppingCart()->SilvercartShoppingCartPosition()->Count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * returns a single page by its class name
     * used to retrieve links dynamically
     *
     * @param string $className the classes name
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.2.11
     * @return DataObject | false a single object of the site tree
     */
    public function PageByClassName($className) {
        $page = DataObject::get_one($className, "`Status` = 'Published'");
        if ($page) {
            return $page;
        } else {
            return false;
        }
    }

}
