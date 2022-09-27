<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Model\BlacklistEntry;
use SilverCart\Model\ContactMessage;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\View\ArrayData;

/**
 * ModelAdmin for ContactMessages
 * 
 * @package SilverCart
 * @subpackage Admin\Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ContactMessageAdmin extends ModelAdmin
{
    const SESSION_KEY     = 'SilverCart.ContactMessageAdmin';
    const SESSION_KEY_TAB = 'SilverCart.ContactMessageAdmin.Tab';

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'customer';
    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 20;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-contact-messages';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Contact Messages';
    /**
     * Menu icon
     * 
     * @var string
     */
    private static $menu_icon = null;
    /**
     * Menu icon CSS class
     * 
     * @var string
     */
    private static $menu_icon_class = 'font-icon-comment';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = [
        ContactMessage::class,
        BlacklistEntry::class,
    ];
    /**
     * Current tab
     *
     * @var string
     */
    protected $currentTab = null;
    
    /**
     * Returns the current model context list.
     * Adds a filter dependent on the given tab.
     * 
     * @return \SilverCart\ORM\DataList
     */
    public function getList() : DataList
    {
        return $this->getTabbedList($this->getCurrentTab());
    }
    
    /**
     * Adds a filter dependent on the given tab.
     * 
     * @param string $tab        Tab
     * @param string $modelClass Model class
     * 
     * @return \SilverCart\ORM\DataList
     * 
     * 
     */
    protected function getTabbedList(string $tab, string $modelClass = null) : DataList
    {
        $restoreModelClass = null;
        if (class_exists($modelClass)) {
            $restoreModelClass = $this->modelClass;
            $this->modelClass  = $modelClass;
        }
        if ($this->modelClass === ContactMessage::class) {
            $list = parent::getList()->filter('IsSpam', $tab === 'spam');
        } else {
            $list = parent::getList();
        }
        if (class_exists($restoreModelClass)) {
            $this->modelClass  = $restoreModelClass;
        }
        return $list;
    }
    
    /**
     * Adds some additional tabs.
     * 
     * @return \SilverStripe\ORM\ArrayList
     */
    protected function getManagedModelTabs() : ArrayList
    {
        $forms = parent::getManagedModelTabs();
        if (ContactMessage::config()->store_spam_in_database) {
            $tabs  = ['spam'];
            $link  = $this->Link($this->sanitiseClassName(ContactMessage::class));
            if (strpos($link, '?') === false) {
                $link = "{$link}?tab=";
            } else {
                $link = "{$link}&tab=";
            }

            foreach ($forms as $form) {
                if ($form->ClassName === ContactMessage::class) {
                    $form->Link          = $link . 'all';
                    $form->LinkOrCurrent = (ContactMessage::class == $this->modelClass && $this->getCurrentTab() === 'all') ? 'current' : 'link';
                }
            }
            foreach ($tabs as $tab) {
                $forms->push(ArrayData::create([
                            'Title'         => _t(ContactMessage::class . '.ModelAdminTab' . ucfirst($tab), ucfirst($tab)) . " ({$this->getTabbedList($tab, ContactMessage::class)->count()})",
                            'ClassName'     => ContactMessage::class,
                            'Link'          => $link . $tab,
                            'LinkOrCurrent' => (ContactMessage::class == $this->modelClass && $this->getCurrentTab() === $tab) ? 'current' : 'link'
                ]));
            }
        }
        return $forms;
    }
    
    /**
     * Returns the current (tab stored in session).
     * If given by HTTP GET parameter, the current tab will be updated.
     * 
     * @return string
     */
    protected function getCurrentTab() : string
    {
        if (is_null($this->currentTab)) {
            $this->currentTab = $this->getRequest()->getVar('tab');
            if (!is_null($this->currentTab)) {
                Tools::Session()->set(self::SESSION_KEY_TAB, $this->currentTab);
                Tools::saveSession();
            } else {
                $this->currentTab = Tools::Session()->get(self::SESSION_KEY_TAB);
            }
        }
        if (is_null($this->currentTab)) {
            $this->currentTab = 'all';
        }
        return $this->currentTab;
    }
}

