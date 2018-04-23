<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\LoginForm;
use SilverCart\Model\Pages\FrontPage;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataObject;

/**
 * MyAccountHolder Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MyAccountHolderController extends \PageController {

    /**
     * ID of the breadcrumb element
     *
     * @var int
     */
    protected $breadcrumbElementID;

    /**
     * statements to be called on object initialisation
     *
     * @return void
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.11.2010
     */
    public function init() {
        if (Config::EnableSSL()) {
            Director::forceSSL();
        }
        
        Tools::Session()->clear("redirect"); //if customer has been to the checkout yet this is set to direct him back to the checkout after address editing

        parent::init();
    }

    /**
     * Uses the children of MyAccountHolder to render a subnavigation
     * with the SilverCart/Model/Pages/Includes/SubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
        $elements = array(
            'SubElementsTitle'  => Tools::PageByIdentifierCode('SilvercartMyAccountHolder')->MenuTitle,
            'SubElements'       => Tools::PageByIdentifierCode('SilvercartMyAccountHolder')->Children(),
        );
        $this->extend('updateSubNavigation', $elements);
        $output = $this->customise($elements)->renderWith(
            array(
                'SilverCart/Model/Pages/Includes/SubNavigation',
            )
        );
        return Tools::string2html($output);
    }

    /**
     * template method for breadcrumbs
     * show breadcrumbs for pages which show a DataObject determined via URL parameter ID
     * see _config.php
     *
     * @return string
     */
    public function getBreadcrumbs() {
        $page = Tools::PageByIdentifierCode($this->IdentifierCode);

        return $this->ContextBreadcrumbs($page, 20, false, FrontPage::class, true);
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
     * @return string html for breadcrumbs
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 04.03.2014
     */
    public function ContextBreadcrumbs($context, $maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false) {
        $page = $context;
        $parts = array();

        $contextObject = DataObject::get($context->getSection())->byID($this->getBreadcrumbElementID());
        
        if ($contextObject) {
            $parts[] = $contextObject->Title;
        }

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

        return implode(" &raquo; ", array_reverse($parts));
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

    /**
     * returns the link to the order detail page (without orderID)
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2011
     */
    public function OrderDetailLink() {
        return Tools::PageByIdentifierCode('SilvercartOrderDetailPage')->Link() . 'detail/';
    }
    
    /**
     * Returns the LoginForm.
     * 
     * @return LoginForm
     */
    public function LoginForm() {
        $form = new LoginForm($this);
        return $form;
    }
    
}