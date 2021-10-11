<?php

namespace SilverCart\Model\Pages;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Dev\SeoTools;
use SilverCart\Forms\CustomRequiredFields;
use SilverCart\Forms\RegisterRegularCustomerForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Translation\TranslationTools;
use SilverStripe\Assets\Image;
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
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Security\Permission;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\ArrayData;
use SilverStripe\View\HTML;
use TractorCow\Fluent\Extension\FluentDirectorExtension;
use TractorCow\Fluent\State\FluentState;

/**
 * Standard Page.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property bool   $UseAsRootForMainNavigation Determines whether to use this page as root page for the main navigation
 * @property string $IdentifierCode             Page identifier code
 */
class Page extends SiteTree
{
    use \SilverCart\ORM\ExtensibleDataObject;
    
    const IDENTIFIER_ADDRESS_HOLDER              = 'SilvercartAddressHolder';
    const IDENTIFIER_CART_PAGE                   = 'SilvercartCartPage';
    const IDENTIFIER_CHECKOUT_PAGE               = 'SilvercartCheckoutStep';
    const IDENTIFIER_CONTACT_FORM_PAGE           = 'SilvercartContactFormPage';
    const IDENTIFIER_CUSTOMER_DATA_PAGE          = 'SilvercartCustomerDataPage';
    const IDENTIFIER_DATA_PRIVACY_PAGE           = 'DataPrivacyStatementPage';
    const IDENTIFIER_FRONT_PAGE                  = 'SilvercartFrontPage';
    const IDENTIFIER_IMPRINT_PAGE                = 'ImprintPage';
    const IDENTIFIER_META_ABOUT_HOLDER           = 'SilvercartMetaNavigationHolderAbout';
    const IDENTIFIER_META_LEGAL_HOLDER           = 'SilvercartMetaNavigationHolderLegal';
    const IDENTIFIER_META_SERVICE_HOLDER         = 'SilvercartMetaNavigationHolderService';
    const IDENTIFIER_META_SHOP_HOLDER            = 'SilvercartMetaNavigationHolderShop';
    const IDENTIFIER_MY_ACCOUNT_HOLDER           = 'SilvercartMyAccountHolder';
    const IDENTIFIER_NEWSLETTER_PAGE             = 'SilvercartNewsletterPage';
    const IDENTIFIER_ORDER_HOLDER                = 'SilvercartOrderHolder';
    const IDENTIFIER_PAYMENT_METHODS_PAGE        = 'SilvercartPaymentMethodsPage';
    const IDENTIFIER_PRODUCT_GROUP_HOLDER        = 'SilvercartProductGroupHolder';
    const IDENTIFIER_REGISTRATION_PAGE           = 'SilvercartRegistrationPage';
    const IDENTIFIER_REVOCATION_FORM_PAGE        = 'SilvercartRevocationFormPage';
    const IDENTIFIER_REVOCATION_INSTRUCTION_PAGE = 'SilvercartRevocationInstructionPage';
    const IDENTIFIER_SEARCH_RESULTS_PAGE         = 'SilvercartSearchResultsPage';
    const IDENTIFIER_SHIPPING_FEES_PAGE          = 'SilvercartShippingFeesPage';
    const IDENTIFIER_TERMS_OF_SERVICE_PAGE       = 'TermsOfServicePage';
    
    /**
     * The robots tag content for dev mode or sensible content.
     *
     * @var string
     */
    private static $robots_tag_noindex = 'noindex, nofollow';
    /**
     * The robots tag content for common content in live mode.
     *
     * @var string
     */
    private static $robots_tag_index = 'index, follow';
    /**
     * extends statics
     * 
     * @var array
     */
    private static $db = [
        'UseAsRootForMainNavigation' => 'Boolean(0)',
        'IdentifierCode'             => 'Varchar(50)',
    ];
    /**
     * Define indexes.
     *
     * @var array
     */
    private static $indexes = [
        'IdentifierCode' => '("IdentifierCode")'
    ];
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
     * Returns the robots tag depending on the current environment mode.
     * 
     * @return string
     */
    public static function getRobotsTag() : string
    {
        $robotsTag = self::config()->robots_tag_noindex;
        if (Director::isLive()) {
            $robotsTag = self::config()->robots_tag_index;
        }
        return $robotsTag;
    }
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name()
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name()
    {
        return Tools::plural_name_for($this); 
    }

    /**
     * Always enable translations for this page.
     *
     * @return bool
     */
    public function canTranslate()
    {
        return true;
    }

    /**
     * Define editing fields for the storeadmin.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function($fields) {
            if (Permission::check('ADMIN')) {
                $fields->addFieldToTab('Root.Main', TextField::create('IdentifierCode', 'IdentifierCode'));
                $fields->dataFieldByName('IdentifierCode')->setRightTitle($this->fieldLabel('DoNotEdit'));
            } else {
                $fields->addFieldToTab('Root.Main', HiddenField::create('IdentifierCode', 'IdentifierCode'));
            }
            $fields->addFieldToTab('Root.Main', CheckboxField::create('UseAsRootForMainNavigation', $this->fieldLabel('UseAsRootForMainNavigation')));
        });
        $this->getCMSFieldsIsCalled = true;
        return parent::getCMSFields();
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2018
     */
    public function fieldLabels($includerelations = true)
    {
        $this->beforeUpdateFieldLabels(function(&$labels) {
            $labels = array_merge(
                    $labels,
                    Tools::field_labels_for(self::class),
                    [
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
                    ]
            );
        });
        return parent::fieldLabels($includerelations);
    }
    
    /**
     * Handles the UseAsRootForMainNavigation property on before write.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        
        $request = Controller::curr()->getRequest();
        /* @var $request HTTPRequest */
        if ($request->postVar('ID') == $this->ID
         && $request->postVar('UseAsRootForMainNavigation') == '1'
        ) {
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
     * getter for the Title, looks for set translation
     * 
     * @return string
     */
    public function getTitle()
    {
        $title = $this->getField('Title');
        if (!$this->getCMSFieldsIsCalled) {
            $this->extend('updateTitle', $title);
        }
        return $title;
    }

    /**
     * Returns the original title without any extension mutations.
     * 
     * @return string
     */
    public function getPlainTitle() : string
    {
        return (string) $this->getField('Title');
    }
    
    /**
     * Returns the main navigation root page (set in backend).
     * 
     * @return Page
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.10.2014
     */
    public function MainNavigationRootPage()
    {
        $mainNavigationRootPage = null;
        $this->extend('alterMainNavigationRootPage', $mainNavigationRootPage);
        if ($mainNavigationRootPage instanceof SiteTree) {
            return $mainNavigationRootPage;
        }
        $list                   = Page::get()->filter('UseAsRootForMainNavigation', true);
        $dataQuery              = $list->dataQuery();
        $mainNavigationRootPage = $list->first();
        if (is_null($mainNavigationRootPage)) {
            $versionedMode          = $dataQuery->getQueryParam('Versioned.mode');
            $versionedStage         = $dataQuery->getQueryParam('Versioned.stage');
            $mainNavigationRootPage = Tools::PageByIdentifierCode(self::IDENTIFIER_PRODUCT_GROUP_HOLDER);
            if (is_null($mainNavigationRootPage)) {
                $mainNavigationRootPage = ProductGroupHolder::get()->first();
            }
            if (is_null($mainNavigationRootPage)) {
                $mainNavigationRootPage = ProductGroupHolder::create();
            }
            if ($mainNavigationRootPage instanceof Page
             && (is_null($versionedMode)
              || $versionedMode === false
              || ($versionedMode === 'stage'
               && $versionedStage === Versioned::LIVE))
            ) {
                $table = Page::config()->table_name;
                DB::query("UPDATE {$table} SET UseAsRootForMainNavigation = 1 WHERE ID = {$mainNavigationRootPage->ID}");
                DB::query("UPDATE {$table}_Live SET UseAsRootForMainNavigation = 1 WHERE ID = {$mainNavigationRootPage->ID}");
            }
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
    public function MainNavigationCacheKey()
    {
        $cacheKeyParts = [
            'Navigation',
            $this->ID,
            i18n::get_locale(),
            $this->MainNavigationRootPage()->stageChildren(false)->max('LastEdited'),
        ];
        $this->extend('updateMainNavigationCacheKeyParts', $cacheKeyParts);
        return implode('_', $cacheKeyParts);
    }
    
    /**
     * Returns the related groups as a cache key string.
     *
     * @return string
     */
    public function MemberGroupCacheKey()
    {
        $cacheKey = i18n::get_locale() . '_' . Customer::get_group_cache_key();
        if (Director::isDev()) {
            $cacheKey .= '_' . uniqid();
        }
        return $cacheKey;
    }
    
    /**
     * Dummy to provide enhanced product group functions.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.10.2014
     */
    public function hasProductsOrChildren()
    {
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
    public function SilvercartNoImage()
    {
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
    public function getSection()
    {
        return Address::class;
    }

    /**
     * getTreeTitle will return three <span> html DOM elements, an empty <span> with
     * the class 'jstree-pageicon' in front, following by a <span> wrapping around its
     * MenutTitle, then following by a <span> indicating its publication status. 
     *
     * @return string a html string ready to be directly used in a template
     */
    public function getTreeTitle()
    {
        $flags = $this->getStatusFlags();
        $treeTitle = sprintf(
            "<span class=\"jstree-pageicon\"></span>%s",
            Convert::raw2xml(str_replace(["\n","\r"],"",$this->MenuTitle))
        );
        foreach ($flags as $class => $data) {
            if (is_string($data)) {
                $data = ['text' => $data];
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
    public function MetaTags($includeTitle = true)
    {
        $originalTags = parent::MetaTags($includeTitle);
        $tags         = str_replace('SilverStripe - http://silverstripe.org', 'SilverCart - http://www.silvercart.org - SilverStripe - http://silverstripe.org', $originalTags);
        $tags        .= HTML::createTag('link', [
            'rel'  => 'canonical',
            'href' => $this->AbsoluteCanonicalLink(),
        ]) . PHP_EOL;
        $tags        .= HTML::createTag('meta', [
            'name'    => 'robots',
            'content' => self::getRobotsTag(),
        ]) . PHP_EOL;
        $tags        .= $this->OpenGraphMetaTags();
        return $tags;
    }
    
    /**
     * Takes a relativelink and returns an absolute link.
     * Meant to use in a template.
     * 
     * @param string $link Relative link
     * 
     * @return string
     */
    public function MakeAbsoluteLink($link)
    {
        return Director::absoluteURL($link);
    }
    
    /**
     * Returns the absolute canonical link.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.06.2017
     */
    public function AbsoluteCanonicalLink()
    {
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
    public function CanonicalLink()
    {
        return $this->Link();
    }

    /**
     * Alias for self::Link().
     *
     * @param string $action Action to call.
     *
     * @return string
     * 
     * @see self::Link()
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2018
     */
    public function OriginalLink($action = null)
    {
        return $this->Link($action);
    }

    /**
     * Same as \TractorCow\Fluent\Extension\FluentExtension::LocaleLink() but uses the method
     * self::OriginalLink() instead.
     * If the method self::OriginalLink() doesn't exists on the called page, self::LocaleLink() 
     * will be used instead.
     * 
     * @param string $locale Locale to get link for
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2018
     * @see \TractorCow\Fluent\Extension\FluentExtension::LocaleLink()
     */
    public function LocaleOriginalLink($locale)
    {
        // Skip dataobjects that do not have the Link method
        if (!$this->hasMethod('OriginalLink')) {
            return $this->LocaleLink($locale);
        }

        // Return locale root url if unable to view this item in this locale
        $defaultLink = $this->BaseURLForLocale($locale);
        if ($this->hasMethod('canViewInLocale')
         && !$this->canViewInLocale($locale)
        ) {
            return $defaultLink;
        }

        return FluentState::singleton()->withState(function (FluentState $newState) use ($locale, $defaultLink) {
            $newState->setLocale($locale);
            // Non-db records fall back to internal behaviour
            if (!$this->isInDB()) {
                return $this->OriginalLink();
            }

            // Reload this record in the correct locale
            $record = DataObject::get($this->ClassName)->byID($this->ID);
            if ($record) {
                return $record->OriginalLink();
            } else {
                // may not be published in this locale
                return $defaultLink;
            }
        });
    }
    
    /**
     * Returns all translated locales as a special ArrayList
     *
     * @return ArrayList 
     */
    public function getAllTranslations()
    {
        $currentLocale      = Tools::current_locale();
        $translations       = Tools::get_translations($this);
        $translationSource  = ArrayList::create();
        $currentItem        = null;
        if ($translations) {
            foreach ($translations as $translation) {
                $isCurrent = $translation->Locale === $currentLocale;
                $item      = ArrayData::create([
                    'Language'       => TranslationTools::get_display_language($translation->Locale, $currentLocale),
                    'NativeLanguage' => TranslationTools::get_display_language($translation->Locale, $translation->Locale),
                    'Name'           => TranslationTools::get_translation_name($translation->Locale, $currentLocale),
                    'NativeName'     => TranslationTools::get_translation_name($translation->Locale, $translation->Locale),
                    'Code'           => $this->getIso2($translation->Locale),
                    'LangCode'       => $this->getLangCode($translation->Locale),
                    'RFC1766'        => i18n::convert_rfc1766($translation->Locale),
                    'Link'           => $translation->LocaleLink($translation->Locale) . '?' . FluentDirectorExtension::config()->get('query_param') . '=' . urlencode($translation->Locale),
                    'IsCurrent'      => $isCurrent,
                ]);
                if ($isCurrent) {
                    $currentItem = $item;
                } else {
                    $translationSource->push($item);
                }
            }
            if (!is_null($currentItem)) {
                $translationSource->unshift($currentItem);
            }
            $translationSource->unshift(ArrayData::create([
                'Language'       => TranslationTools::get_display_language($currentLocale, $currentLocale),
                'NativeLanguage' => TranslationTools::get_display_language($currentLocale, $currentLocale),
                'Name'           => TranslationTools::get_translation_name($currentLocale, $currentLocale),
                'NativeName'     => TranslationTools::get_translation_name($currentLocale, $currentLocale),
                'Code'           => $this->getIso2($currentLocale),
                'LangCode'       => $this->getLangCode($currentLocale),
                'RFC1766'        => i18n::convert_rfc1766($currentLocale),
                'Link'           => $this->Link(),
                'IsCurrent'      => true,
            ]));
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
    public function getIso2($locale)
    {
        $parts = explode('_', $locale);
        return strtolower($parts[1]);
    }
    
    /**
     * Returns the ISO2 for the given locale
     *
     * @param string $locale Locale
     * 
     * @return string
     */
    public function getLangCode($locale)
    {
        $parts = explode('_', $locale);
        return strtolower($parts[0]);
    }
    
    /**
     * Adds a decorator hook and returns the Content.
     * 
     * @return string
     */
    public function getContent()
    {
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
    public function getMetaDescription()
    {
        $metaDescription = $this->getField('MetaDescription');
        if (!$this->getCMSFieldsIsCalled) {
            if (empty($metaDescription)) {
                $metaDescription = SeoTools::defaultMetaDescriptionFor($this);
            }
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
    public function String2urlSegment($string)
    {
        return Tools::string2urlSegment($string);
    }
    
    /**
     * Checks if the given link is the start page link.
     * 
     * @param string $link Link to check
     * 
     * @return bool
     */
    public function isStartPage($link = '')
    {
        if (empty($link)) {
            $link = $this->Link();
        }
        $relativeLink = Director::makeRelative($link);
        $plainLink    = str_replace('/', '', $relativeLink);
        return $plainLink == RootURLController::get_homepage_link();
    }

    /**
     * Adds the overwriteBreadcrumbs extension.
     *
     * @param int    $maxDepth       maximum depth level of shown pages in breadcrumbs
     * @param bool   $unlinked       true, if the breadcrumbs should be displayed without links
     * @param string $stopAtPageType name of pagetype to stop at
     * @param bool   $showHidden     true, if hidden pages should be displayed in breadcrumbs
     * @param string $delimiter      Delimiter to use
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.04.2018
     */
    public function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false, $delimiter = '&raquo;')
    {
        $breadcrumbs = null;
        $this->extend('overwriteBreadcrumbs', $breadcrumbs);
        if (is_null($breadcrumbs)) {
            $breadcrumbs = parent::Breadcrumbs($maxDepth, $unlinked, $stopAtPageType, $showHidden, $delimiter);
        }
        return $breadcrumbs;
    }
    
    /**
     * Allows user code to hook into DataObject::getBreadcrumbItems prior to 
     * updateBreadcrumbItems being called on extensions
     *
     * @param callable $callback The callback to execute
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.09.2018
     */
    public function beforeUpdateBreadcrumbItems($callback)
    {
        $this->beforeExtending('updateBreadcrumbItems', $callback);
    }
    
    /**
     * Returns a list of breadcrumbs for the current page.
     * Adds the extension hook updateBreadcrumbItems.
     *
     * @param int            $maxDepth       The maximum depth to traverse.
     * @param boolean|string $stopAtPageType ClassName of a page to stop the upwards traversal.
     * @param boolean        $showHidden     Include pages marked with the attribute ShowInMenus = 0
     *
     * @return ArrayList
     */
    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false)
    {
        $breadcrumbItems = parent::getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        $this->extend('updateBreadcrumbItems', $breadcrumbItems);
        return $breadcrumbItems;
    }
    
    /**
     * Returns some additional content to insert to the header navigation right 
     * before the translation select item is rendered.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.08.2018
     */
    public function HeaderNavBeforeTranslationSelectContent()
    {
        $content = '';
        $this->extend('updateHeaderNavBeforeTranslationSelectContent', $content);
        return Tools::string2html($content);
    }
    
    /**
     * Returns some additional content to insert to the header navigation right 
     * before the account select item is rendered.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.08.2018
     */
    public function HeaderNavBeforeAccountSelectContent()
    {
        $content = '';
        $this->extend('updateHeaderNavBeforeAccountSelectContent', $content);
        return Tools::string2html($content);
    }
    
    /**
     * Returns some additional content to insert to the header navigation right 
     * before the cart select item is rendered.
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.08.2018
     */
    public function HeaderNavBeforeCartSelectContent()
    {
        $content = '';
        $this->extend('updateHeaderNavBeforeCartSelectContent', $content);
        return Tools::string2html($content);
    }

    /**
     * Replace a "[searchresults_link,id=n,q=s,c=m]" shortcode with a link to 
     * the search results page with the corresponding ID.
     *
     * @param array           $arguments Arguments
     * @param string          $content   Content
     * @param ShortcodeParser $parser    Parser
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.10.2018
     */
    public static function link_shortcode_handler($arguments, $content = null, $parser = null)
    {
        if (!isset($arguments['id'])
         || !is_numeric($arguments['id'])
        ) {
            return null;
        }
        if (!isset($arguments['q'])
         && !isset($arguments['c'])
        ) {
            return null;
        }

        /** @var SiteTree $page */
        if (!($page = DataObject::get_by_id(self::class, $arguments['id']))         // Get the current page by ID.
         && !($page = Versioned::get_latest_version(self::class, $arguments['id'])) // Attempt link to old version.
        ) {
            return null; // There were no suitable matches at all.
        }

        /* @var $page SiteTree */
        if ($page->hasMethod('PlainLink')) {
            $link = $page->PlainLink();
        } else {
            $link = $page->Link();
        }
        if (strpos($link, '?') === false) {
            $link = "{$link}?";
        } else {
            $link = "{$link}&";
        }
        if (isset($arguments['q'])
         && isset($arguments['c'])
        ) {
            $link = "{$link}q={$arguments['q']}&c={$arguments['c']}";
        } elseif (isset($arguments['q'])) {
            $link = "{$link}q={$arguments['q']}";
        } elseif (isset($arguments['c'])) {
            $link = "{$link}c={$arguments['c']}";
        }
        $link = Convert::raw2att($link);

        if ($content) {
            return sprintf('<a href="%s">%s</a>', $link, $parser->parse($content));
        } else {
            return $link;
        }
    }
    /**
     * Meta Tags for Social Sharing Pages and Products
     * 
     * @return string
     * 
     * @author Jiri Ripa <jripa@pixeltricks.de>
     * @since 08.05.2020
     */
    public function OpenGraphMetaTags() : string
    {
        $tags       = "";
        $siteConfig = SiteConfig::current_site_config();
        $ctrl       = Controller::curr();
        $image      = null;
        $ogType     = "website";
        $tags      .= HTML::createTag('meta', [
            'property' => 'og:site_name',
            'content'  => $siteConfig->Title,
        ]) . PHP_EOL;
        $tags      .= HTML::createTag('meta', [
            'property' => 'og:url',
            'content'  => Director::absoluteURL($this->Link()),
        ]) . PHP_EOL;
        $metaTitle  = $this->MetaTitle ? $this->MetaTitle : $this->getTitle() ;
        $tags      .= HTML::createTag('meta', [
            'property' => 'og:title',
            'content'  => $metaTitle,
        ]) . PHP_EOL;
        $tags      .= HTML::createTag('meta', [
            'property' => 'og:description',
            'content'  => $this->MetaDescription,
        ]) . PHP_EOL;
        if ($ctrl instanceof ProductGroupPageController 
         && $ctrl->isProductDetailView()
        ) {
            $ogType  = "product:item";
            $product = $ctrl->getDetailViewProduct();
            $image   = $product->getListImage();
        } elseif ($this instanceof ProductGroupPage) {
            $ogType  = "product:group";
            $image   = $this->GroupPicture();
            if ((!($image instanceof Image)
              || !$image->exists())
             && $this->getProductsToDisplay()->exists()
            ) {
                $image = $this->getProductsToDisplay()->first()->getListImage();
            }
        }
        if (!($image instanceof Image)
         || !$image->exists()
        ) {
            $image = $siteConfig->MobileTouchIcon();
            if (!($image instanceof Image)
             || !$image->exists()
            ) {
                $image = $siteConfig->ShopLogo();
            }
        }
        if ($image instanceof Image 
         && $image->exists()
        ) {
            $tags  .= HTML::createTag('meta', [
                'property' => 'og:image',
                'content'  => $image->Pad(250,250)->getAbsoluteURL(),
            ]) . PHP_EOL;
        }
            $tags  .= HTML::createTag('meta', [
                'property' => 'og:type',
                'content'  => $ogType,
        ]) . PHP_EOL;
        return $tags;        
    }
    
    /**
     * Returns the current date.
     * 
     * @return string
     */
    public function getCurrentDate()
    {
        $date = DBDatetime::create();
        $date->setValue(date('Y-m-d H:i:s'));
        return $date;
    }
    
    /**
     * Returns the password validation hint.
     * 
     * @return string
     */
    public function PasswordHint() : string
    {
        return _t(RegisterRegularCustomerForm::class . '.PasswordHint', 'Create a password for your login. Your password needs at least {minlength} characters and contain at least 1 capital letter, 1 small letter and 1 number.', [
            'minlength' => CustomRequiredFields::config()->password_minlength,
        ]);
    }
}