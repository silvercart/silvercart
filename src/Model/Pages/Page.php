<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\TextField;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Permission;
use Translatable;

/**
 * Standard Page.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Page extends SiteTree {

    /**
     * extends statics
     * 
     * @var array
     */
    private static $db = array(
        'UseAsRootForMainNavigation' => 'Boolean(0)',
        'IdentifierCode'             => 'Varchar(50)',
    );

    /**
     * Define indexes.
     *
     * @var array
     */
    private static $indexes = array(
        'IdentifierCode' => '("IdentifierCode")'
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPage';
    
    /**
     * Indicator to check whether getCMSFields is called
     *
     * @var boolean
     */
    protected $getCMSFieldsIsCalled = false;
    
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
     * Always enable translations for this page.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 12.11.2012
     */
    public function canTranslate() {
        return true;
    }

    /**
     * Define editing fields for the storeadmin.
     *
     * @return FieldList all related CMS fields
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Jiri Ripa <jripa@pixeltricks.de>
     * @since 06.10.2014
     */
    public function getCMSFields() {
        $this->getCMSFieldsIsCalled = true;
        $fields = parent::getCMSFields();

        if (Permission::check('ADMIN')) {
            $fields->addFieldToTab('Root.Main', new TextField('IdentifierCode', 'IdentifierCode'));
            $fields->dataFieldByName('IdentifierCode')->setRightTitle($this->fieldLabel('DoNotEdit'));
        } else {
            $fields->addFieldToTab('Root.Main', new HiddenField('IdentifierCode', 'IdentifierCode'));
        }
        $fields->addFieldToTab('Root.Main', new CheckboxField('UseAsRootForMainNavigation', $this->fieldLabel('UseAsRootForMainNavigation')));
        
        return $fields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.10.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'InvoiceAddress'             => _t(Page::class . '.BILLING_ADDRESS', 'Invoice address'),
                    'OrderDate'                  => _t(Page::class . '.ORDER_DATE', 'Order date'),
                    'Title'                      => _t(Page::class . '.TITLE', 'Title'),
                    'Logo'                       => _t(Page::class . '.LOGO', 'Logo'),
                    'Login'                      => _t(Page::class . '.LOGIN', 'Login'),
                    'EmailAddress'               => _t(Page::class . '.EMAIL_ADDRESS', 'Email address'),
                    'Password'                   => _t(Page::class . '.PASSWORD', 'Password'),
                    'PasswordCheck'              => _t(Page::class . '.PASSWORD_CHECK', 'Password check'),
                    'ShippingAddress'            => _t(Page::class . '.SHIPPING_ADDRESS', 'Shipping address'),
                    'UseAsRootForMainNavigation' => _t(Page::class . '.UseAsRootForMainNavigation', 'Use children of this page as main navigation menu'),
                    'ValueOfGoods'               => _t(Page::class . '.VALUE_OF_GOODS', 'Value of goods'),
                    'APICreate'                  => _t(Page::class . '.API_CREATE', 'Can create objects via the API'),
                    'APIDelete'                  => _t(Page::class . '.API_DELETE', 'Can delete objects via the API'),
                    'APIEdit'                    => _t(Page::class . '.API_EDIT', 'Can edit objects via the API'),
                    'APIView'                    => _t(Page::class . '.API_VIEW', 'Can read objects via the API'),
                    'CredentialsWrong'           => _t(Page::class . '.CREDENTIALS_WRONG', 'Your credentials are incorrect.'),
                    'EmailAlreadyRegisterd'      => _t(Page::class . '.EMAIL_ALREADY_REGISTERED', 'This Email address is already registered'),
                    'EmailNotFound'              => _t(Page::class . '.EMAIL_NOT_FOUND', 'This Email address could not be found.'),
                    'EmailWrong'                 => _t(Page::class . '.USER_NOT_EXISTING', 'This user does not exist.'),
                    'PasswordWrong'              => _t(Page::class . '.PASSWORD_WRONG', 'This user does not exist.'),
                    'Save'                       => _t(Page::class . '.SAVE', 'save'),
                    'Submit'                     => _t(Page::class . '.SUBMIT', 'Submit'),
                    'YourRemarks'                => _t(Page::class . '.YOUR_REMARKS', 'Your remarks'),
                    'Message'                    => _t(Page::class . '.MESSAGE', 'message'),
                    'SubmitMessage'              => _t(Page::class . '.SUBMIT_MESSAGE', 'submit message'),
                    'DecreaseQuantity'           => _t(Page::class . '.DECREMENT_POSITION', 'Decrease quantity'),
                    'IncreaseQuantity'           => _t(Page::class . '.INCREMENT_POSITION', 'Increase quantity'),
                    'Birthday'                   => _t(Page::class . '.BIRTHDAY', 'Birthday'),
                    'Day'                        => _t(Page::class . '.DAY', 'Day'),
                    'Month'                      => _t(Page::class . '.MONTH', 'Month'),
                    'Year'                       => _t(Page::class . '.YEAR', 'Year'),
                    'January'                    => _t(Page::class . '.JANUARY', 'January'),
                    'February'                   => _t(Page::class . '.FEBRUARY', 'February'),
                    'March'                      => _t(Page::class . '.MARCH', 'March'),
                    'April'                      => _t(Page::class . '.APRIL', 'April'),
                    'May'                        => _t(Page::class . '.MAY', 'May'),
                    'June'                       => _t(Page::class . '.JUNE', 'June'),
                    'July'                       => _t(Page::class . '.JULY', 'July'),
                    'August'                     => _t(Page::class . '.AUGUST', 'August'),
                    'September'                  => _t(Page::class . '.SEPTEMBER', 'September'),
                    'October'                    => _t(Page::class . '.OCTOBER', 'October'),
                    'November'                   => _t(Page::class . '.NOVEMBER', 'November'),
                    'December'                   => _t(Page::class . '.DECEMBER', 'December'),
                    'RemoveFromCart'             => _t(Page::class . '.REMOVE_FROM_CART', 'Remove'),
                    'PreviousPage'               => _t(Page::class . '.BACK_TO_DEFAULT', 'previous page'),
                    'DoNotEdit'                  => _t(Page::class . '.DO_NOT_EDIT', 'Do not edit this field unless you know exectly what you are doing!'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Handles the UseAsRootForMainNavigation property on before write.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        
        $request = Controller::curr()->getRequest();
        /* @var $request HTTPRequest */
        if ($request->postVar('ID') == $this->ID &&
            $request->postVar('UseAsRootForMainNavigation') == '1') {
            $this->UseAsRootForMainNavigation = true;
        }
        
        if ($this->isChanged('UseAsRootForMainNavigation')) {
            $changed = $this->getChangedFields(false, 1);
            $ch      = $changed['UseAsRootForMainNavigation'];
            if ($this->UseAsRootForMainNavigation) {
                $table = Tools::get_table_name(Page::class);
                DB::query('UPDATE ' . $table . ' SET UseAsRootForMainNavigation = 0 WHERE ID != ' . $this->ID);
            } elseif ($ch['before'] != $ch['after']) {
                $this->UseAsRootForMainNavigation = true;
            }
        }
    }
    
    /**
     * Returns the main navigation root page (set in backend).
     * 
     * @return Page
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.10.2014
     */
    public function MainNavigationRootPage() {
        $mainNavigationRootPage = Page::get()->filter('UseAsRootForMainNavigation', true)->first();
        if (is_null($mainNavigationRootPage)) {
            $mainNavigationRootPage = Tools::PageByIdentifierCode('SilvercartProductGroupHolder');
            $table = Tools::get_table_name(Page::class);
            DB::query('UPDATE ' . $table . ' SET UseAsRootForMainNavigation = 1 WHERE ID = ' . $mainNavigationRootPage->ID);
            DB::query('UPDATE ' . $table . '_Live SET UseAsRootForMainNavigation = 1 WHERE ID = ' . $mainNavigationRootPage->ID);
        }
        return $mainNavigationRootPage;
    }
    
    /**
     * Returns the main navigation cache key.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    public function MainNavigationCacheKey() {
        $cacheKeyParts = array(
            'Navigation',
            $this->ID,
            i18n::get_locale(),
            $this->MainNavigationRootPage()->stageChildren(false)->max('LastEdited'),
        );
        $this->extend('updateMainNavigationCacheKeyParts', $cacheKeyParts);
        return implode('_', $cacheKeyParts);
    }
    
    /**
     * Returns the related groups as a cache key string.
     *
     * @return string
     */
    public function MemberGroupCacheKey() {
        return i18n::get_locale() . '_' . Customer::get_group_cache_key();
    }
    
    /**
     * Dummy to provide enhanced product group functions.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    public function hasProductsOrChildren() {
        return true;
    }
    
    /**
     * Returns the generic image for products without an own image. If none is
     * defined, boolean false is returned.
     *
     * @return mixed Image|bool false
     * 
     * @author Sascha koehler <skoehler@pixeltricks.de>
     * @since 27.06.2011
     */
    public function SilvercartNoImage() {
        $noImageObj = Config::getNoImage();
        
        if ($noImageObj) {
            return $noImageObj;
        }
        
        return false;
    }

    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs. This is used as fall back.
     *
     * @return string
     */
    public function getSection() {
        return Address::class;
    }

    /**
     * getTreeTitle will return three <span> html DOM elements, an empty <span> with
     * the class 'jstree-pageicon' in front, following by a <span> wrapping around its
     * MenutTitle, then following by a <span> indicating its publication status. 
     *
     * @return string a html string ready to be directly used in a template
     */
    public function getTreeTitle() {
        $flags = $this->getStatusFlags();
        $treeTitle = sprintf(
            "<span class=\"jstree-pageicon\"></span>%s",
            Convert::raw2xml(str_replace(array("\n","\r"),"",$this->MenuTitle))
        );
        foreach ($flags as $class => $data) {
            if (is_string($data)) {
                $data = array('text' => $data);
            }
            $treeTitle .= sprintf(
                "<span class=\"badge %s\"%s>%s</span>",
                'status-' . Convert::raw2xml($class),
                (isset($data['title'])) ? sprintf(' title="%s"', Convert::raw2xml($data['title'])) : '',
                Convert::raw2xml($data['text'])
            );
        }

        return $treeTitle;
    }
    
    /**
     * Return the title, description, keywords and language metatags.
     * 
     * @param bool $includeTitle Show default <title>-tag, set to false for custom templating
     * 
     * @return string The XHTML metatags
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.06.2017
     */
    public function MetaTags($includeTitle = true) {
        $tags = parent::MetaTags($includeTitle);
        $tags = str_replace('SilverStripe - http://silverstripe.org', 'SilverCart - http://www.silvercart.org - SilverStripe - http://silverstripe.org', $tags);
        $tags .= '<link rel="canonical" href="' . $this->AbsoluteCanonicalLink() . '" />' . PHP_EOL;
        return $tags;
    }
    
    /**
     * Returns the absolute canonical link.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.06.2017
     */
    public function AbsoluteCanonicalLink() {
        return Director::absoluteURL($this->CanonicalLink());
    }
    
    /**
     * Returns the relative canonical link.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.06.2017
     */
    public function CanonicalLink() {
        return $this->Link();
    }
    
    /**
     * Returns all translated locales as a special ArrayList
     *
     * @return ArrayList 
     */
    public function getAllTranslations() {
        $currentLocale      = Translatable::get_current_locale();
        $translations       = $this->getTranslations();
        $translationSource  = new ArrayList();
        if ($translations) {
            $translationSource->push(new DataObject(
                array(
                    'Name'          => TranslationTools::get_translation_name($currentLocale, $currentLocale),
                    'NativeName'    => TranslationTools::get_translation_name($currentLocale, $currentLocale),
                    'Code'          => $this->getIso2($currentLocale),
                    'RFC1766'       => i18n::convert_rfc1766($currentLocale),
                    'Link'          => $this->Link(),
                )
            ));
            foreach ($translations as $translation) {
                $translationSource->push(new DataObject(
                    array(
                        'Name'          => TranslationTools::get_translation_name($translation->Locale, $currentLocale),
                        'NativeName'    => TranslationTools::get_translation_name($translation->Locale, $translation->Locale),
                        'Code'          => $this->getIso2($translation->Locale),
                        'RFC1766'       => i18n::convert_rfc1766($translation->Locale),
                        'Link'          => $translation->Link(),
                    )
                ));
            }
        }
        return $translationSource;
    }
    
    /**
     * Returns the ISO2 for the given locale
     *
     * @param string $locale Locale
     * 
     * @return string
     */
    public function getIso2($locale) {
        $parts = explode('_', $locale);
        return strtolower($parts[1]);
    }
    
    /**
     * Adds a decorator hook and returns the Content.
     * 
     * @return string
     */
    public function getContent() {
        $content = $this->getField('Content');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateContent', $content);
        }
        return $content;
    }
    
    /**
     * Adds a decorator hook and returns the MetaDescription.
     * 
     * @return string
     */
    public function getMetaDescription() {
        $metaDescription = $this->getField('MetaDescription');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateMetaDescription', $metaDescription);
        }
        return $metaDescription;
    }
    
    /**
     * Returns the given string to use as a valid URL segment.
     * 
     * @param string $string String to convert
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.07.2017
     */
    public function String2urlSegment($string) {
        return Tools::string2urlSegment($string);
    }
    
    /**
     * Checks if the given link is the start page link.
     * 
     * @param string $link Link to check
     * 
     * @return bool
     */
    public function isStartPage($link = '') {
        if (empty($link)) {
            $link = $this->Link();
        }
        $relativeLink = Director::makeRelative($link);
        $plainLink    = str_replace('/', '', $relativeLink);
        return $plainLink == RootURLController::config()->get('default_homepage_link');
    }
}