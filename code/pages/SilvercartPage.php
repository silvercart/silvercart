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
    public function extraStatics() {
        return array(
            'db' => array(
                'headerPicture' => 'Image'
            )
        );
    }

    /**
     * is the centerpiece of every data administration interface in Silverstripe
     *
     * @return FieldSet all related CMS fields
     * @author Jiri Ripa <jripa@pixeltricks.de>
     * @since 15.10.2010
     */
    public function updateCMSFields(FieldSet $fields) {
        $fields->addFieldToTab('Root.Content.Main', new FileIFrameField('headerPicture', _t('Page.HEADERPICTURE', 'header picture')));
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

    public function init() {
        Requirements::themedCSS('layout');
        Requirements::javascript("pixeltricks_module/script/jquery.js");
        Requirements::javascript("silvercart/js/startupScripts.js");
        Requirements::javascript("silvercart/js/jquery.pixeltricks.tools.js");

        $this->registerCustomHtmlForm('QuickSearch', new QuickSearchForm($this));
        $this->registerCustomHtmlForm('QuickLogin', new QuickLoginForm($this));
        
        parent::init();
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
     * form for incrementing the amount of a shopping cart position
     *
     * @return Form to increment a shopping cart position
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.09.2010
     */
    public function incrementAmountForm() {
        $fields = new FieldSet();
        $fields->push(new HiddenField('ShoppingCartPositionID', 'ShoppingCartPositionID'));
        $actions = new FieldSet();
        $actions->push(new FormAction('doIncrementAmount', '+'));
        $form = new Form($this, 'doIncrementAmount', $fields, $actions);
        return $form;
    }

    /**
     * action method for IncrementAmountForm
     *
     * @param array $data Array with Cartpositions
     * @param Form  $form Cart Form Object
     *
     * @return object Object CartPage_Controller
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.09.2010
     */
    public function doIncrementAmount($data, $form) {
        $shoppingCartPosition = DataObject::get_by_id('ShoppingCartPosition', $data['ShoppingCartPositionID']);
        if ($shoppingCartPosition) {
            if ($shoppingCartPosition->shoppingCartID == Member::currentUser()->shoppingCartID) {//make shure that a customer can delete only his own shoppingCartpositions, in your face, damn hackers!
                $shoppingCartPosition->Quantity++;
                $shoppingCartPosition->write();
            }
        }
        return $this;
    }

    /**
     * form for decrementing the amount of a shopping cart position
     *
     * @return Form
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.09.2010
     */
    public function decrementAmountForm() {
        $fields = new FieldSet();
        $fields->push(new HiddenField('ShoppingCartPositionID', 'ShoppingCartPositionID'));
        $actions = new FieldSet();
        $actions->push(new FormAction('doDecrementAmount', '-'));
        $form = new Form($this, 'decrementAmountForm', $fields, $actions);
        return $form;
    }

    /**
     * action method for DecrementAmountForm
     *
     * @param array $data Array with Cartpositions
     * @param Form  $form Cart Form Object
     *
     * @return bool
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.09.2010
     */
    public function doDecrementAmount($data, $form) {
        $shoppingCartPosition = DataObject::get_by_id('ShoppingCartPosition', $data['ShoppingCartPositionID']);
        // Zuweisung gewollt
        if ($shoppingCartPosition) {
            if ($shoppingCartPosition->Quantity > 1
                    && $shoppingCartPosition->shoppingCartID == Member::currentUser()->shoppingCartID) {
                $shoppingCartPosition->Quantity--;
                $shoppingCartPosition->write();
            } elseif ($shoppingCartPosition->Quantity == 1
                    && $shoppingCartPosition->shoppingCartID == Member::currentUser()->shoppingCartID) {
                $shoppingCartPosition->delete();
            }

            return $this;
        }
    }

    /**
     * form for deleting article from shopping cart
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return Form
     * @since 30.09.2010
     */
    public function removeFromCartForm() {
        $fields = new FieldSet();
        $fields->push(new HiddenField('ShoppingCartPositionID', 'ShoppingCartPositionID'));
        $actions = new FieldSet();
        $actions->push(new FormAction('doRemoveFromCart', _t('Page.REMOVE_FROM_CART', 'remove')));
        $form = new Form($this, 'removeFromCartForm', $fields, $actions);
        return $form;
    }

    /**
     * Action to remove article from shopping cart
     *
     * @param array $data Array with Cartpositions
     * @param Form  $form Cart Form Object
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return Bool
     * @since 30.09.2010
     */
    public function doRemoveFromCart($data, $form) {
        $shoppingCartPosition = DataObject::get_by_id('ShoppingCartPosition', $data['ShoppingCartPositionID']);
        if ($shoppingCartPosition) {
            if ($shoppingCartPosition->shoppingCartID == Member::currentUser()->shoppingCartID) {
                $shoppingCartPosition->delete();
            }
            return $this;
        }
    }

    /**
     * create form for flushing article from shopping cart
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return Form
     * @since 30.09.2010
     */
    public function flushCartForm() {
        $fields = new FieldSet();
        $fields->push(new HiddenField('cartID', 'cartID', Member::currentUser()->shoppingCartID));
        $actions = new FieldSet();
        $actions->push(new FormAction('doFlushCart', _t('Page.EMPTY_CART', 'empty')));
        $form = new Form($this, 'flushCartForm', $fields, $actions);
        return $form;
    }

    /**
     * action to flush article from shopping cart
     *
     * @param array $data Array with Cartpositions
     * @param Form  $form Cart Form Object
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @return Bool
     * @since 30.09.2010
     */
    public function doFlushCart($data, $form) {
        if ($data['cartID'] == Member::currentUser()->shoppingCartID) {
            $cartID = Member::currentUser()->shoppingCartID;
            $filter = sprintf("\"shoppingCartID\" = '%s'", $cartID);
            $shoppingCartPositions = DataObject::get('ShoppingCartPosition', $filter);
            foreach ($shoppingCartPositions as $shoppingCartPosition) {
                $shoppingCartPosition->delete();
            }
            return $this;
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
        $parts[] = $address->singular_name();

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
            if ($member->ClassName != "AnonymousCustomer") {
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

        if (Member::currentUser() && Member::currentUser()->ClassName != 'AnonymousCustomer') {
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
     * This function is used to return the Latest Blogentries
     *
     * @return DataObjectSet blog entries
     * @author Oliver <info@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public function LatestBlogEntry() {

        $blogEntry = DataObject::get("BlogEntry", "", "Created DESC", "", 1);

        return $blogEntry;
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