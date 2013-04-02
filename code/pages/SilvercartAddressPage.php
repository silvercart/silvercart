<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Child of AddressHolder, CRUD a single address
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 16.02.2011
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartAddressPage extends SilvercartMyAccountHolder {
    
    /**
     * Indicates whether this page type can be root
     *
     * @var bool
     */
    public static $can_be_root = false;
    
    /**
     * The icon to use for this page in the storeadmin sitetree.
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/my_account_holder";

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
        return SilvercartTools::singular_name_for($this);
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
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getSection() {
        return 'SilvercartAddress';
    }

    /**
     * Returns the link to this detail page.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2012
     */
    public function Link() {
        $controller = Controller::curr();
        $link       = parent::Link();

        if ($controller instanceof SilvercartAddressPage_Controller) {
            $link .= $controller->getAddressID();
        }

        return $link;
    }
}

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 19.10.2010
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartAddressPage_Controller extends SilvercartMyAccountHolder_Controller {

    /**
     * ID of the requested address
     *
     * @var int 
     */
    protected $addressID;

    /**
     * statements to be called on instanciation
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2011
     */
    public function init() {
        $addressID = false;
        
        if (isset($_POST['addressID'])) {
            $addressID = Convert::raw2sql($_POST['addressID']);
        } else {
            $addressID = $this->urlParams['Action'];
        }
        $this->setAddressID($addressID);
        $this->setBreadcrumbElementID($addressID);

        // get the address to check whether it is related to the actual customer or not.
        $address = DataObject::get_by_id('SilvercartAddress', $addressID);
        
        if ($address->MemberID > 0) {
            if ($address->Member()->ID != Member::currentUserID()) {
                // the address is not related to the customer, redirect elsewhere...
                $this->redirect($this->PageByIdentifierCode()->Link());
            }
        }

        $this->registerCustomHtmlForm('SilvercartEditAddressForm', new SilvercartEditAddressForm($this, array('addressID' => $addressID)));
        
        parent::init();
    }

    /**
     * handles the given action. If the action is a numeric value, an address is
     * requested. This method manipulates the handling for this case.
     *
     * @param SS_HTTPRequest $request Request
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function handleAction($request) {
        if (!$this->hasMethod($this->urlParams['Action'])) {
            $secondaryAction = $this->urlParams['ID'];
            if ($this->hasMethod($secondaryAction) &&
                $this->hasAction($secondaryAction)) {
                $result = $this->{$secondaryAction}($request);
                if (is_array($result)) {
                    return $this->getViewer($this->action)->process($this->customise($result));
                } else {
                    return $result;
                }
            } elseif (is_numeric($this->urlParams['Action'])) {
                return $this->getViewer('index')->process($this);
            }
        }
        return parent::handleAction($request);
    }
    
    /**
     * Returns the cancel link.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.04.2011
     */
    public function CancelLink() {
        return $this->Parent()->Link();
    }

    /**
     * returns the id of the address requested by the Action.
     *
     * @return int
     */
    public function getAddressID() {
        return $this->addressID;
    }

    /**
     * sets the id of the address requested by the Action.
     *
     * @param int $addressID addressID
     *
     * @return void
     */
    public function setAddressID($addressID) {
        $this->addressID = $addressID;
    }
    
}
