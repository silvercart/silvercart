<?php

namespace SilverCart\Admin\Model;

use SilverCart\Admin\Dev\Install\RequireDefaultRecords;
use SilverCart\Admin\Forms\AlertInfoField;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Forms\FormFields\MoneyField;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Forms\FormFields\TextareaField;
use SilverCart\Model\Content\LinkableItem;
use SilverCart\Model\CookieConsent\ExternalResource;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\ProductCondition;
use SilverCart\Model\Translation\TranslationTools;
use SilverCart\ORM\Connect\DBMigration;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;

/**
 * This class is used to add SilverCart configuration options 
 * and a translation section to the original SiteConfig 
 * object in the cms section.
 *
 * @package SilverCart
 * @subpackage Admin_Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SiteConfigExtension extends DataExtension
{
    use \SilverCart\Core\ExtensibleExtension;
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'ShopName'         => 'Varchar(256)',
        'ShopStreet'       => 'Varchar(256)',
        'ShopStreetNumber' => 'Varchar(6)',
        'ShopPostcode'     => 'Varchar(32)',
        'ShopCity'         => 'Varchar(256)',
        'ShopPhone'        => 'Varchar(256)',
        'ShopEmail'        => 'Varchar(256)',
        'ShopOpeningHours' => 'Text',
        'ShopAdditionalInfo'                    => 'Text',
        'ShopAdditionalInfo2'                   => 'Text',
        'SilvercartVersion'                     => 'Varchar(16)',
        'SilvercartMinorVersion'                => 'Varchar(16)',
        'DefaultCurrency'                       => 'Varchar(16)',
        'DefaultPriceType'                      => 'Enum("gross,net","gross")',
        'EmailSenderName'                       => 'Varchar(255)',
        'EmailSender'                           => 'Varchar(255)',
        'GlobalEmailRecipient'                  => 'Varchar(255)',
        'DefaultMailRecipient'                  => 'Varchar(255)',
        'DefaultMailOrderNotificationRecipient' => 'Varchar(255)',
        'DefaultMailRevocationRecipient'        => 'Varchar(255)',
        'DefaultMailRegistrationRecipient'      => 'Varchar(255)',
        'DefaultContactMessageRecipient'        => 'Varchar(255)',
        'enableSSL'                             => 'Boolean(0)',
        'productsPerPage'                       => 'Int',
        'productGroupsPerPage'                  => 'Int',
        'displayedPaginationPages'              => 'Int',
        'minimumOrderValue'                     => \SilverCart\ORM\FieldType\DBMoney::class,
        'useMinimumOrderValue'                  => 'Boolean(0)',
        'freeOfShippingCostsFrom'               => \SilverCart\ORM\FieldType\DBMoney::class,
        'useFreeOfShippingCostsFrom'            => 'Boolean(0)',
        'enableBusinessCustomers'               => 'Boolean(0)',
        'enablePackstation'                     => 'Boolean(0)',
        'enableStockManagement'                 => 'Boolean(0)',
        'isStockManagementOverbookable'         => 'Boolean(0)',
        'SkipPaymentStepIfUnique'               => 'Boolean(0)',
        'SkipShippingStepIfUnique'              => 'Boolean(0)',
        'InvoiceAddressIsAlwaysShippingAddress' => 'Boolean(0)',
        'redirectToCartAfterAddToCart'          => 'Boolean(0)',
        'redirectToCheckoutWhenInCart'          => 'Boolean(0)',
        'WeightUnit'                            => Config::ENUMERATION_WEIGHT_UNIT,
        'DimensionUnit'                         => Config::ENUMERATION_DIMENSION_UNIT,
        'DisplayWeightsInKilogram'              => 'Boolean(1)',
        'demandBirthdayDateOnRegistration'      => 'Boolean(0)',
        'UseMinimumAgeToOrder'                  => 'Boolean(0)',
        'MinimumAgeToOrder'                     => 'Varchar(3)',
        'addToCartMaxQuantity'                  => 'Int(999)',
        'DefaultLocale'                         => 'DBLocale',
        'useDefaultLanguageAsFallback'          => 'Boolean(1)',
        'ShowTaxAndDutyHint'                    => 'Boolean(0)',
        'productDescriptionFieldForCart'        => 'Enum("ShortDescription,LongDescription","ShortDescription")',
        'useProductDescriptionFieldForCart'     => 'Boolean(1)',
        'DisableProductLinkInCart'              => 'Boolean(0)',
        'useStrictSearchRelevance'              => 'Boolean(0)',
        'ShowPaymentMethodsInFooter'            => 'Boolean(1)',
        'ShowShippingMethodsInFooter'           => 'Boolean(1)',
        'userAgentBlacklist'                    => 'Text',
        'ColorScheme'                           => 'Varchar(256)',
        'MaintenanceMode'               => 'Boolean(0)',
        'MaintenanceStart'              => 'Datetime',
        'MaintenanceEnd'                => 'Datetime',
    ];
    /**
     * Has-one relationships.
     *
     * @var array
     */
    private static $has_one = [
        'ShopLogo'                  => Image::class,
        'SilvercartNoImage'         => Image::class,
        'Favicon'                   => Image::class,
        'MobileTouchIcon'           => Image::class,
        'StandardProductCondition'  => ProductCondition::class,
        'ShopCountry'               => Country::class,
    ];
    /**
     * Has many relations.
     *
     * @var string[]
     */
    private static $has_many = [
        'ExternalLinks' => LinkableItem::class,
    ];
    /**
     * Defaults for empty fields.
     *
     * @var array
     */
    private static $defaults = [
        'SilvercartVersion'             => '4.3',
        'SilvercartMinorVersion'        => '0',
        'DefaultPriceType'              => Config::PRICE_TYPE_GROSS,
        'productsPerPage'               => 18,
        'productGroupsPerPage'          => 6,
        'displayedPaginationPages'      => 4,
        'addToCartMaxQuantity'          => 999,
        'DefaultLocale'                 => 'de_DE',
        'userAgentBlacklist'            => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
        'ColorScheme'                   => 'blue',
        'ShowPaymentMethodsInFooter'    => true,
        'ShowShippingMethodsInFooter'   => true,
    ];
    /**
     * Indicator to check whether getCMSFields is called
     *
     * @var boolean
     */
    protected $getCMSFieldsIsCalled = false;
    /**
     * Will hold the Locale of the SiteConfig object to be written.
     *
     * @var string
     */
    private static $duplicate_config_locale = null;
    /**
     * Disables the maintenance mode
     * 
     * @var bool
     */
    protected static $maintenanceModeIsDisabled = false;
    
    /**
     * Workaround to add a custom callback method for ShopState if possible.
     * 
     * @param \SilverStripe\View\ViewableData $owner Owner
     */
    public function setOwner($owner) : void
    {
        parent::setOwner($owner);
        $this->customAddCallbackMethod('ShopCountry');
    }
    
    /**
     * There is only one config object which is created on installation.
     * This method disables creation of config objects in the modeladmin.
     * 
     * @param Member $member Member to check permission for
     *
     * @return false 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 09.02.2013
     */
    public function canCreate($member = null, $context = [])
    {
        return false;
    }

    /**
     * Remove permission to delete for all members.
     *
     * @param Member $member Member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    public function canDelete($member = null)
    {
        return false;
    }

    /**
     * Indicates that the config is translatable
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.03.2012
     */
    public function canTranslate()
    {
        return true;
    }
    
    /**
     * Updates the fields labels
     *
     * @param array &$labels Labels to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     */
    public function updateFieldLabels(&$labels)
    {
        $labels = array_merge(
                $labels,
                Tools::field_labels_for(self::class),
                [
                    'ShopData'                              => _t(Config::class . '.ShopData', 'Shop data'),
                    'ShopName'                              => _t(Config::class . '.ShopName', 'Shop name'),
                    'ShopStreet'                            => _t(Config::class . '.ShopStreet', 'Street'),
                    'ShopStreetNumber'                      => _t(Config::class . '.ShopStreetNumber', 'Street number'),
                    'ShopPostcode'                          => _t(Config::class . '.ShopPostcode', 'Postcode'),
                    'ShopCity'                              => _t(Config::class . '.ShopCity', 'City'),
                    'ShopCountry'                           => _t(Config::class . '.ShopCountry', 'Country'),
                    'ShopPhone'                             => _t(Config::class . '.ShopPhone', 'Phone'),
                    'ShopEmail'                             => _t(Config::class . '.ShopEmail', 'Email'),
                    'ShopOpeningHours'                      => _t(Config::class . '.ShopOpeningHours', 'Opening hours'),
                    'ShopAdditionalInfo'                    => _t(Config::class . '.ShopAdditionalInfo', 'Additional Information'),
                    'ShopAdditionalInfoDesc'                => _t(Config::class . '.ShopAdditionalInfoDesc', 'Additional Information'),
                    'ShopAdditionalInfo2'                   => _t(Config::class . '.ShopAdditional2Info', 'Additional Information 2'),
                    'ShopAdditionalInfo2Desc'               => _t(Config::class . '.ShopAdditionalInfo2Desc', 'Additional Information 2'),
                    'addToCartMaxQuantity'                  => _t(Config::class . '.ADDTOCARTMAXQUANTITY', 'Maximum allowed quantity of a single product in the shopping cart'),
                    'DefaultCurrency'                       => _t(Config::class . '.DEFAULTCURRENCY', 'Default currency'),
                    'DefaultPriceType'                      => _t(Config::class . '.DEFAULTPRICETYPE', 'Default price type'),
                    'EmailSenderName'                       => _t(Config::class . '.EmailSenderName', 'Email sender name'),
                    'EmailSenderNameExample'                => _t(Config::class . '.EmailSenderNameExample', 'e.g. "Your Shop Name"'),
                    'EmailSender'                           => _t(Config::class . '.EmailSender', 'Email sender address'),
                    'EmailSenderExample'                    => _t(Config::class . '.EmailSenderExample', 'e.g. "noreply@example.com"'),
                    'GlobalEmailRecipient'                  => _t(Config::class . '.GLOBALEMAILRECIPIENT', 'Global email recipient'),
                    'enableBusinessCustomers'               => _t(Config::class . '.ENABLEBUSINESSCUSTOMERS', 'Enable business customers'),
                    'enablePackstation'                     => _t(Config::class . '.ENABLEPACKSTATION', 'Enable address input fields for PACKSTATION'),
                    'enableSSL'                             => _t(Config::class . '.ENABLESSL', 'Enable SSL'),
                    'enableStockManagement'                 => _t(Config::class . '.ENABLESTOCKMANAGEMENT', 'enable stock management'),
                    'minimumOrderValue'                     => _t(Config::class . '.MINIMUMORDERVALUE', 'Minimum order value'),
                    'useMinimumOrderValue'                  => _t(Config::class . '.USEMINIMUMORDERVALUE', 'Use minimum order value'),
                    'useFreeOfShippingCostsFrom'            => _t(Config::class . '.USEFREEOFSHIPPINGCOSTSFROM', 'Use settings for "free of shipping costs"'),
                    'freeOfShippingCostsFrom'               => _t(Config::class . '.FREEOFSHIPPINGCOSTSFROM', 'Free of shipping costs from'),
                    'productsPerPage'                       => _t(Config::class . '.PRODUCTSPERPAGE', 'Products per page'),
                    'productGroupsPerPage'                  => _t(Config::class . '.PRODUCTGROUPSPERPAGE', 'Product groups per page'),
                    'isStockManagementOverbookable'         => _t(Config::class . '.QUANTITY_OVERBOOKABLE', 'Is the stock quantity of a product generally overbookable?'),
                    'demandBirthdayDateOnRegistration'      => _t(Config::class . '.DEMAND_BIRTHDAY_DATE_ON_REGISTRATION', 'Demand birthday date on registration?'),
                    'UseMinimumAgeToOrder'                  => _t(Config::class . '.UseMinimumAgeToOrder', 'Use minimum age to order?'),
                    'MinimumAgeToOrder'                     => _t(Config::class . '.MinimumAgeToOrder', 'Minimum age to order'),
                    'DefaultLocale'                         => _t(Config::class . '.DEFAULT_LANGUAGE', 'default language'),
                    'useDefaultLanguageAsFallback'          => _t(Config::class . '.USE_DEFAULT_LANGUAGE', 'Use default language if no translation is found?'),
                    'productDescriptionFieldForCart'        => _t(Config::class . '.PRODUCT_DESCRIPTION_FIELD_FOR_CART', 'Field for product description in the shopping cart'),
                    'useProductDescriptionFieldForCart'     => _t(Config::class . '.USE_PRODUCT_DESCRIPTION_FIELD_FOR_CART', 'Display product description in shopping cart'),
                    'useStrictSearchRelevance'              => _t(Config::class . '.USE_STRICT_SEARCH_RELEVANCE', 'Use strict search. Shows only exact matches.'),
                    'DefaultMailRecipient'                  => _t(Config::class . '.DEFAULT_MAIL_RECIPIENT', 'Default Email Recipient (general)'),
                    'DefaultMailOrderNotificationRecipient' => _t(Config::class . '.DEFAULT_MAIL_ORDER_NOTIFICATION_RECIPIENT', 'Default email recipient for order notifications'),
                    'DefaultContactMessageRecipient'        => _t(Config::class . '.DEFAULT_CONTACT_MESSAGE_RECIPIENT', 'Default email recipient for contact messages'),
                    'userAgentBlacklist'                    => _t(Config::class . '.USER_AGENT_BLACKLIST', 'UserAgent blacklist (one UserAgent per line)'),
                    'redirectToCartAfterAddToCart'          => _t(Config::class . '.REDIRECTTOCARTAFTERADDTOCART', 'Redirect customer to cart after "add to cart" action'),
                    'redirectToCheckoutWhenInCart'          => _t(Config::class . '.redirectToCheckoutWhenInCart', 'Redirect customer to checkout when entering shopping cart'),
                    'addExampleData'                        => _t(Config::class . '.ADD_EXAMPLE_DATA', 'Add example data'),
                    'addExampleDataDesc'                    => _t(Config::class . '.ADD_EXAMPLE_DATA_DESCRIPTION', 'Add example data'),
                    'addExampleConfig'                      => _t(Config::class . '.ADD_EXAMPLE_CONFIGURATION', 'Add example configuration'),
                    'addExampleConfigDesc'                  => _t(Config::class . '.ADD_EXAMPLE_CONFIGURATION_DESCRIPTION', 'Add example configuration'),
                    'displayedPaginationPages'              => _t(Config::class . '.DISPLAYEDPAGINATION', 'number of simultaneously shown page numbers'),
                    'SilvercartNoImage'                     => _t(Config::class . '.DEFAULT_IMAGE', 'Default product image'),
                    'StandardProductConditionID'            => _t(ProductCondition::class . '.USE_AS_STANDARD_CONDITION', 'Use as default condition'),
                    'StandardProductConditionEmptyString'   => _t(ProductCondition::class . '.PLEASECHOOSE', 'Please choose'),
                    'GeneralConfiguration'                  => _t(Config::class . '.GeneralConfiguration', 'General Configuration'),
                    'EmailConfiguration'                    => _t(Config::class . '.EmailConfiguration', 'Email Configuration'),
                    'CustomerConfiguration'                 => _t(Config::class . '.CustomerConfiguration', 'Customer Configuration'),
                    'ProductConfiguration'                  => _t(Config::class . '.ProductConfiguration', 'Product Configuration'),
                    'CheckoutConfiguration'                 => _t(Config::class . '.CheckoutConfiguration', 'Checkout Configuration'),
                    'ShopDataConfiguration'                 => _t(Config::class . '.ShopData', 'Shop Data Configuration'),
                    'SecurityConfiguration'                 => _t(Config::class . '.SecurityConfiguration', 'Security Configuration'),
                    'MaintenanceConfiguration'              => _t(self::class . '.MaintenanceConfiguration', 'Maintenance Configuration'),
                    'MaintenanceInfo'                       => _t(self::class . '.MaintenanceInfo', 'To run the maintenance mode, the creation of a status 503 error page in CMS is required.'),
                    'SkipPaymentStepIfUnique'               => _t(Config::class . '.SKIP_PAYMENT_STEP_IF_UNIQUE', 'Skip payment step if there is only one selection.'),
                    'SkipShippingStepIfUnique'              => _t(Config::class . '.SKIP_SHIPPING_STEP_IF_UNIQUE', 'Skip shipping step if there is only one selection.'),
                    'InvoiceAddressIsAlwaysShippingAddress' => _t(Config::class . '.InvoiceAddressIsAlwaysShippingAddress', 'Invoice address is always shipping address'),
                    'DisplayWeightsInKilogram'              => _t(Config::class . '.DISPLAY_WEIGHTS_IN_KILOGRAM', 'Display weights in kilogram (kg)'),
                    'ShowTaxAndDutyHint'                    => _t(Config::class . '.ShowTaxAndDutyHint', 'Show hint for additional taxes and duty for non EU countries.'),
                    
                    'EmailSenderRightTitle'                             => _t(Config::class . '.EmailSenderDesc', 'The email sender will be the sender address of all emails sent by SilverCart.'),
                    'EmailSenderNameDesc'                               => _t(Config::class . '.EmailSenderNameDesc', 'The email sender will be the sender address of all emails sent by SilverCart.'),
                    'GlobalEmailRecipientRightTitle'                    => _t(Config::class . '.GLOBALEMAILRECIPIENT_INFO', 'The global email recipient can be set optionally. The global email recipient will get ALL emails sent by SilverCart (order notifications, contact emails, etc.). The recipients set directly at the email templates will not be replaced, but extended.'),
                    'DefaultMailRecipientRightTitle'                    => _t(Config::class . '.DEFAULT_MAIL_RECIPIENT_INFO', 'Emails which are directed to shop operator will be sent to this address.'),
                    'DefaultMailOrderNotificationRecipientRightTitle'   => _t(Config::class . '.DEFAULT_MAIL_ORDER_NOTIFICATION_RECIPIENT_INFO', 'Order notifications will be sent to this address (no more to Default Email Recipient).'),
                    'DefaultContactMessageRecipientRightTitle'          => _t(Config::class . '.DEFAULT_CONTACT_MESSAGE_RECIPIENT_INFO', 'Contact messages will be sent to this address (no more to Default Email Recipient).'),
                    'userAgentBlacklistRightTitle'                      => _t(Config::class . '.USER_AGENT_BLACKLIST_INFO', 'Set one UserAgent per line.<br/>If a visitors UserAgent matches one out of this list, the request will be blocked to prevent spam bot attacks onto input forms.<br/><strong>Caution: Every visitor has an UserAgent. Only add a UserAgent to this list when you are sure that it will only match spam bots.</strong>.'),
                    
                    'GoogleplusLink'                => _t(SiteConfigExtension::class . '.GoogleplusLink', 'Google Plus Link'),
                    'FacebookLink'                  => _t(SiteConfigExtension::class . '.FACEBOOK_LINK', 'Facebook Link'),
                    'TwitterLink'                   => _t(SiteConfigExtension::class . '.TWITTER_LINK', 'Twitter Link'),
                    'XingLink'                      => _t(SiteConfigExtension::class . '.XING_LINK', 'Xing Link'),
                    'InstagramLink'                 => _t(SiteConfigExtension::class . '.InstagramLink', 'Instagram Link'),
                    'BloglovinLink'                 => _t(SiteConfigExtension::class . '.BloglovinLink', 'Bloglovin Link'),
                    'PinterestLink'                 => _t(SiteConfigExtension::class . '.PinterestLink', 'Pinterest Link'),
                    'YouTubeLink'                   => _t(SiteConfigExtension::class . '.YouTubeLink', 'YouTube Link'),
                    'TumblrLink'                    => _t(SiteConfigExtension::class . '.TumblrLink', 'Tumblr Link'),
                    'RSSLink'                       => _t(SiteConfigExtension::class . '.RSSLink', 'RSS Link'),
                    'EmailLink'                     => _t(SiteConfigExtension::class . '.EmailLink', 'Contact Email Address'),
                    'SeoTab'                        => _t(Config::class . '.SEO', 'SEO'),
                    'TranslationsTab'               => _t(TranslationTools::class . '.TRANSLATIONS', 'Translations'),
                    'CreateTransHeader'             => _t(TranslationTools::class . '.CREATE', 'Create new translation'),
                    'CreateTransDescription'        => _t(TranslationTools::class . '.CREATE_TRANSLATION_DESC', 'New translations will be created for all pages of the SiteTree (unpublished). Every page will be created as a translation template and will be filled with the chosen languages default content (if exists). If no default content is available for the chosen language, the content of the current language will be preset.'),
                    'NewTransLang'                  => _t(TranslationTools::class . '.NEWLANGUAGE', 'New language'),
                    'publishsitetree'               => _t(TranslationTools::class . '.PUBLISHBUTTON', 'Publish all pages of this translation'),
                    'ExistingTransHeader'           => _t(TranslationTools::class . '.EXISTING', 'Existing translations:'),
                    'CurrentLocale'                 => _t(TranslationTools::class . '.CURRENTLOCALE', 'Current Locale'),

                    'ShopLogo'                 => _t(Config::class . '.ShopLogo', 'Logo'),
                    'ShopLogoDesc'             => _t(Config::class . '.ShopLogoDesc', 'Will be displayed inside the shops head area.'),
                    'Favicon'                  => _t(Config::class . '.Favicon', 'Favicon'),
                    'FaviconDesc'              => _t(Config::class . '.FaviconDesc', 'The favicon is an image which will be shown in the address bar of your browser, on the left side of the URL. Some browsers also show it right at the opened tab. The favicon should be quadratic with dimensions of 32x32 pixels.'),
                    'MobileTouchIcon'          => _t(Config::class . '.MobileTouchIcon', 'Mobile Touch Icon'),
                    'MobileTouchIconDesc'      => _t(Config::class . '.MobileTouchIconDesc', 'The mobile touch icon will be used if a visitor saves your shop as a shourtcut on the homescreen of a smartphone or tablet. The icon should have dimensions of 192x192 pixels.'),
                    'ColorScheme'              => _t(Config::class . '.ColorScheme', 'Color scheme'),
                    'ColorSchemeTab'           => _t(Config::class . '.ColorSchemeTab', 'Color scheme'),
                    'ColorSchemeConfiguration' => _t(Config::class . '.ColorSchemeConfiguration', 'Title & color scheme'),
                    'ShowPaymentMethodsInFooter'  => _t(Config::class . '.ShowPaymentMethodsInFooter', 'Show payment methods (logos) in footer'),
                    'ShowShippingMethodsInFooter' => _t(Config::class . '.ShowShippingMethodsInFooter', 'Show shipping methods (logos) in footer'),
                ]
        );
    }
    
    /**
     * Adds a translation section
     *
     * @param FieldList $fields The FieldList
     * 
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        $this->getCMSFieldsIsCalled = true;
        $fields->findOrMakeTab('Root.ExternalLinks')->setTitle($this->owner->fieldLabel('ExternalLinks'));
        $fields->addFieldToTab('Root.ExternalLinks', GridField::create('ExternalLinks', $this->owner->fieldLabel('ExternalLinks'), $this->owner->ExternalLinks(), GridFieldConfig_RelationEditor::create()));
        
        $fields->findOrMakeTab('Root.ExternalResources', ExternalResource::singleton()->i18n_plural_name());
        $fields->addFieldToTab('Root.ExternalResources', GridField::create('ExternalResources', ExternalResource::singleton()->i18n_plural_name(), ExternalResource::get(), GridFieldConfig_RecordEditor::create()));
        
        $this->getCMSFieldsForSilvercart($fields);
    }
    
    /**
     * Returns the ExternalLinks with icons.
     * 
     * @return \SilverStripe\ORM\DataList
     */
    public function ExternalLinksWithIcon() : DataList
    {
        return $this->owner->ExternalLinks()->exclude([
            'CustomIconHTML'  => ['', null],
            'FontAwesomeIcon' => ['', null],
        ]);
    }
    
    /**
     * Builds and returns the CMS fields.
     *
     * @return FieldList the CMS tabs and fields
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.02.2013
     */
    public function getCMSFieldsForSilvercart(FieldList $fields)
    {
        // Build general toggle group
        $generalConfigurationField = ToggleCompositeField::create(
                'GeneralConfiguration',
                $this->owner->fieldLabel('GeneralConfiguration'),
                [
                    TranslationTools::prepare_translation_dropdown_field($this->owner, SiteTree::class, 'DefaultLocale'),
                    CheckboxField::create('useDefaultLanguageAsFallback', $this->owner->fieldLabel('useDefaultLanguageAsFallback')),
                    TextField::create('DefaultCurrency',                  $this->owner->fieldLabel('DefaultCurrency')),
                    DropdownField::create('DefaultPriceType',             $this->owner->fieldLabel('DefaultPriceType')),
                ]
        )->setHeadingLevel(4)->setStartClosed(false);
        $this->owner->extend('updateCMSFieldsGeneralConfiguration', $generalConfigurationField);

        // Build email toggle group
        $emailConfigurationField = ToggleCompositeField::create(
                'EmailConfiguration',
                $this->owner->fieldLabel('EmailConfiguration'),
                [
                    TextField::create('EmailSenderName',                       $this->owner->fieldLabel('EmailSenderName')),
                    TextField::create('EmailSender',                           $this->owner->fieldLabel('EmailSender')),
                    TextField::create('GlobalEmailRecipient',                  $this->owner->fieldLabel('GlobalEmailRecipient')),
                    TextField::create('DefaultMailRecipient',                  $this->owner->fieldLabel('DefaultMailRecipient')),
                    TextField::create('DefaultMailOrderNotificationRecipient', $this->owner->fieldLabel('DefaultMailOrderNotificationRecipient')),
                    TextField::create('DefaultMailRevocationRecipient',        $this->owner->fieldLabel('DefaultMailRevocationRecipient')),
                    TextField::create('DefaultMailRegistrationRecipient',      $this->owner->fieldLabel('DefaultMailRegistrationRecipient')),
                    TextField::create('DefaultContactMessageRecipient',        $this->owner->fieldLabel('DefaultContactMessageRecipient'))
                ]
        )->setHeadingLevel(4);
        $this->owner->extend('updateCMSFieldsEmailConfiguration', $emailConfigurationField);

        // Build customer toggle group
        $customerConfigurationField = ToggleCompositeField::create(
                'CustomerConfiguration',
                $this->owner->fieldLabel('CustomerConfiguration'),
                [
                    CheckboxField::create('enableBusinessCustomers',          $this->owner->fieldLabel('enableBusinessCustomers')),
                    CheckboxField::create('enablePackstation',                $this->owner->fieldLabel('enablePackstation')),
                    CheckboxField::create('demandBirthdayDateOnRegistration', $this->owner->fieldLabel('demandBirthdayDateOnRegistration')),
                ]
        )->setHeadingLevel(4);
        $this->owner->extend('updateCMSFieldsCustomerConfiguration', $customerConfigurationField);

        // Build product toggle group
        $productConfigurationField = ToggleCompositeField::create(
                'ProductConfiguration',
                $this->owner->fieldLabel('ProductConfiguration'),
                [
                    CheckboxField::create('enableStockManagement',         $this->owner->fieldLabel('enableStockManagement')),
                    CheckboxField::create('isStockManagementOverbookable', $this->owner->fieldLabel('isStockManagementOverbookable')),
                    TextField::create('productsPerPage',                   $this->owner->fieldLabel('productsPerPage')),
                    TextField::create('productGroupsPerPage',              $this->owner->fieldLabel('productGroupsPerPage')),
                    TextField::create('displayedPaginationPages',          $this->owner->fieldLabel('displayedPaginationPages')),
                    UploadField::create('SilvercartNoImage',               $this->owner->fieldLabel('SilvercartNoImage')),
                    CheckboxField::create('useStrictSearchRelevance',      $this->owner->fieldLabel('useStrictSearchRelevance')),
                    DropdownField::create('StandardProductConditionID',    $this->owner->fieldLabel('StandardProductConditionID')),
                ]
        )->setHeadingLevel(4);
        $this->owner->extend('updateCMSFieldsProductConfiguration', $productConfigurationField);

        // Build checkout toggle group
        $checkoutConfigurationField = ToggleCompositeField::create(
                'CheckoutConfiguration',
                $this->owner->fieldLabel('CheckoutConfiguration'),
                [
                    CheckboxField::create('enableSSL',                             $this->owner->fieldLabel('enableSSL')),
                    CheckboxField::create('redirectToCartAfterAddToCart',          $this->owner->fieldLabel('redirectToCartAfterAddToCart')),
                    CheckboxField::create('redirectToCheckoutWhenInCart',          $this->owner->fieldLabel('redirectToCheckoutWhenInCart')),
                    CheckboxField::create('DisableProductLinkInCart',              $this->owner->fieldLabel('DisableProductLinkInCart')),
                    CheckboxField::create('useProductDescriptionFieldForCart',     $this->owner->fieldLabel('useProductDescriptionFieldForCart')),
                    DropdownField::create('productDescriptionFieldForCart',        $this->owner->fieldLabel('productDescriptionFieldForCart')),
                    TextField::create('addToCartMaxQuantity',                      $this->owner->fieldLabel('addToCartMaxQuantity')),
                    CheckboxField::create('useMinimumOrderValue',                  $this->owner->fieldLabel('useMinimumOrderValue')),
                    MoneyField::create('minimumOrderValue',                        $this->owner->fieldLabel('minimumOrderValue')),
                    CheckboxField::create('useFreeOfShippingCostsFrom',            $this->owner->fieldLabel('useFreeOfShippingCostsFrom')),
                    MoneyField::create('freeOfShippingCostsFrom',                  $this->owner->fieldLabel('freeOfShippingCostsFrom')),
                    CheckboxField::create('SkipShippingStepIfUnique',              $this->owner->fieldLabel('SkipShippingStepIfUnique')),
                    CheckboxField::create('SkipPaymentStepIfUnique',               $this->owner->fieldLabel('SkipPaymentStepIfUnique')),
                    CheckboxField::create('DisplayWeightsInKilogram',              $this->owner->fieldLabel('DisplayWeightsInKilogram')),
                    DropdownField::create('WeightUnit',                            $this->owner->fieldLabel('WeightUnit')),
                    DropdownField::create('DimensionUnit',                         $this->owner->fieldLabel('DimensionUnit')),
                    CheckboxField::create('ShowTaxAndDutyHint',                    $this->owner->fieldLabel('ShowTaxAndDutyHint')),
                    CheckboxField::create('InvoiceAddressIsAlwaysShippingAddress', $this->owner->fieldLabel('InvoiceAddressIsAlwaysShippingAddress')),
                ]
        )->setHeadingLevel(4);
        $this->owner->extend('updateCMSFieldsCheckoutConfiguration', $checkoutConfigurationField);
        
        // Build shop data toggle group
        $shopDataConfigurationField = ToggleCompositeField::create(
                'ShopDataConfiguration',
                $this->owner->fieldLabel('ShopDataConfiguration'),
                [
                    TextField::create('ShopName',                $this->owner->fieldLabel('ShopName')),
                    TextField::create('ShopStreet',              $this->owner->fieldLabel('ShopStreet')),
                    TextField::create('ShopStreetNumber',        $this->owner->fieldLabel('ShopStreetNumber')),
                    TextField::create('ShopPostcode',            $this->owner->fieldLabel('ShopPostcode')),
                    TextField::create('ShopCity',                $this->owner->fieldLabel('ShopCity')),
                    DropdownField::create('ShopCountryID',       $this->owner->fieldLabel('ShopCountry'), Country::getPrioritiveDropdownMap()),
                    TextField::create('ShopPhone',               $this->owner->fieldLabel('ShopPhone')),
                    TextField::create('ShopEmail',               $this->owner->fieldLabel('ShopEmail')),
                    TextareaField::create('ShopOpeningHours',    $this->owner->fieldLabel('ShopOpeningHours')),
                    TextareaField::create('ShopAdditionalInfo',  $this->owner->fieldLabel('ShopAdditionalInfo')),
                    TextareaField::create('ShopAdditionalInfo2', $this->owner->fieldLabel('ShopAdditionalInfo2')),
                ]
        )->setHeadingLevel(4);
        $this->owner->extend('updateCMSFieldsShopDataConfiguration', $shopDataConfigurationField);

        // Build security toggle group
        $securityConfigurationField = ToggleCompositeField::create(
                'SecurityConfiguration',
                $this->owner->fieldLabel('SecurityConfiguration'),
                [
                    TextareaField::create('userAgentBlacklist', $this->owner->fieldLabel('userAgentBlacklist')),
                ]
        )->setHeadingLevel(4);
        $this->owner->extend('updateCMSFieldsSecurityConfiguration', $securityConfigurationField);
        
        // Build maintenance toggle group
        $maintenanceConfigurationField = ToggleCompositeField::create(
                'MaintenanceConfiguration',
                $this->owner->fieldLabel('MaintenanceConfiguration'),
                [
                    AlertInfoField::create('MaintenanceInfo', $this->owner->fieldLabel('MaintenanceInfo')),//503
                    CheckboxField::create('MaintenanceMode', $this->owner->fieldLabel('MaintenanceMode'))->setDescription($this->owner->fieldLabel('MaintenanceModeDesc')),
                    DatetimeField::create('MaintenanceStart', $this->owner->fieldLabel('MaintenanceStart'))->setDescription($this->owner->fieldLabel('MaintenanceStartDesc')),
                    DatetimeField::create('MaintenanceEnd', $this->owner->fieldLabel('MaintenanceEnd'))->setDescription($this->owner->fieldLabel('MaintenanceEndDesc')),
                ]
        )->setHeadingLevel(4);
        $this->owner->extend('updateCMSFieldsMaintenanceConfiguration', $maintenanceConfigurationField);

        // Build example data toggle group
        $addExampleDataButton   = FormAction::create('add_example_data',   $this->owner->fieldLabel('addExampleData'));
        $addExampleConfigButton = FormAction::create('add_example_config', $this->owner->fieldLabel('addExampleConfig'));
        $exampleDataField       = ToggleCompositeField::create(
                'ExampleData',
                $this->owner->fieldLabel('addExampleData'),
                [
                    $addExampleDataButton,
                    $addExampleConfigButton,
                ]
        )->setHeadingLevel(4);
        $this->owner->extend('updateCMSFieldsExampleData', $exampleDataField);
        
        $addExampleDataButton->setRightTitle($this->owner->fieldLabel('addExampleDataDesc'));
        $addExampleDataButton->setAttribute('data-icon', 'addpage');
        $addExampleConfigButton->setRightTitle($this->owner->fieldLabel('addExampleConfigDesc'));
        $addExampleConfigButton->setAttribute('data-icon', 'addpage');

        // Add groups to Root.Main
        $fields->addFieldToTab('Root.Main', $generalConfigurationField);
        $fields->addFieldToTab('Root.Main', $emailConfigurationField);
        $fields->addFieldToTab('Root.Main', $customerConfigurationField);
        $fields->addFieldToTab('Root.Main', $productConfigurationField);
        $fields->addFieldToTab('Root.Main', $checkoutConfigurationField);
        $fields->addFieldToTab('Root.Main', $shopDataConfigurationField);
        $fields->addFieldToTab('Root.Main', $securityConfigurationField);
        $fields->addFieldToTab('Root.Main', $maintenanceConfigurationField);
        $fields->addFieldToTab('Root.Main', $exampleDataField);

        // Modify field data
        $fields->dataFieldByName('DefaultLocale')                           ->setTitle($this->owner->fieldLabel('DefaultLocale'));
        $fields->dataFieldByName('EmailSender')                             ->setRightTitle($this->owner->fieldLabel('EmailSenderRightTitle'));
        $fields->dataFieldByName('EmailSender')                             ->setPlaceholder($this->owner->fieldLabel('EmailSenderExample'));
        $fields->dataFieldByName('EmailSenderName')                         ->setRightTitle($this->owner->fieldLabel('EmailSenderNameDesc'));
        $fields->dataFieldByName('EmailSenderName')                         ->setPlaceholder($this->owner->fieldLabel('EmailSenderNameExample'));
        $fields->dataFieldByName('GlobalEmailRecipient')                    ->setRightTitle($this->owner->fieldLabel('GlobalEmailRecipientRightTitle'));
        $fields->dataFieldByName('DefaultMailRecipient')                    ->setRightTitle($this->owner->fieldLabel('DefaultMailRecipientRightTitle'));
        $fields->dataFieldByName('DefaultMailOrderNotificationRecipient')   ->setRightTitle($this->owner->fieldLabel('DefaultMailOrderNotificationRecipientRightTitle'));
        $fields->dataFieldByName('DefaultContactMessageRecipient')          ->setRightTitle($this->owner->fieldLabel('DefaultContactMessageRecipientRightTitle'));
        $fields->dataFieldByName('userAgentBlacklist')                      ->setRightTitle($this->owner->fieldLabel('userAgentBlacklistRightTitle'));
        $fields->dataFieldByName('ShopAdditionalInfo')                      ->setDescription($this->owner->fieldLabel('ShopAdditionalInfoDesc'));
        $fields->dataFieldByName('ShopAdditionalInfo2')                     ->setDescription($this->owner->fieldLabel('ShopAdditionalInfo2Desc'));

        // Add i18n to DefaultPriceType source
        $i18nForDefaultPriceTypeField = [];
        foreach ($this->owner->dbObject('DefaultPriceType')->enumValues() as $value => $label) {
            $i18nForDefaultPriceTypeField[$value] = _t(Customer::class . '.' . strtoupper($label), $label);
        }
        $fields->dataFieldByName('DefaultPriceType')->setSource($i18nForDefaultPriceTypeField);

        // Add i18n to productDescriptionFieldForCart source
        $i18nForProductDescriptionField = [];
        foreach ($this->owner->dbObject('productDescriptionFieldForCart')->enumValues() as $productDescriptionField) {
            $i18nForProductDescriptionField[$productDescriptionField] = Product::singleton()->fieldLabel($productDescriptionField);
        }
        $fields->dataFieldByName('productDescriptionFieldForCart')->setSource($i18nForProductDescriptionField);
        $i18nWeightUnitField = [];
        foreach ($this->owner->dbObject('WeightUnit')->enumValues() as $weightUnit) {
            $i18nWeightUnitField[$weightUnit] = _t(self::class . ".WeightUnit_{$weightUnit}", $weightUnit);
        }
        $fields->dataFieldByName('WeightUnit')->setSource($i18nWeightUnitField);
        $i18nDimensionUnitField = [];
        foreach ($this->owner->dbObject('DimensionUnit')->enumValues() as $weightUnit) {
            $i18nDimensionUnitField[$weightUnit] = _t(self::class . ".DimensionUnit_{$weightUnit}", $weightUnit);
        }
        $fields->dataFieldByName('DimensionUnit')->setSource($i18nDimensionUnitField);

        $fields->dataFieldByName('StandardProductConditionID')->setEmptyString($this->owner->fieldLabel('StandardProductConditionEmptyString'));

        $this->getCMSFieldsForColorScheme($fields);
        
        $this->owner->extend('updateSilverCartCMSFields', $fields);
        return $fields;
    }
    
    /**
     * Adds the CMS fields for the ColorScheme setting.
     * 
     * @param FieldList $fields Fields to update
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.09.2014
     */
    public function getCMSFieldsForColorScheme(FieldList $fields)
    {
        $colorSchemePath = Director::publicFolder() . '/resources/vendor/silvercart/silvercart/client/css';
        if (is_dir($colorSchemePath)) {
            if ($handle = opendir($colorSchemePath)) {
                $colorSchemes = ArrayList::create();
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, -4) != '.css') {
                        continue;
                    }
                    if (substr($entry, 0, 6) != 'color_') {
                        continue;
                    }
                    $colorSchemeName  = substr($entry, 6, -4);
                    $colorSchemeFile  = $colorSchemePath . '/' . $entry;
                    $lines            = file($colorSchemeFile);
                    $backgroundColors = [];
                    $fontColors       = [];
                    foreach ($lines as $line) {
                        if (strpos(strtolower($line), 'background-color') !== false
                         && preg_match('/#[a-z|A-Z|0-9]{3,6}/', $line, $matches)
                        ) {
                            $backgroundColors[$matches[0]] = ArrayData::create(['Color' => $matches[0]]);
                        } elseif (strpos(strtolower(trim($line)), 'color') === 0
                         && preg_match('/#[a-z|A-Z|0-9]{3,6}/', $line, $matches)
                        ) {
                            $fontColors[$matches[0]] = ArrayData::create(['Color' => $matches[0]]);
                        }
                    }
                    $colorSchemes->push(ArrayData::create([
                                'Name'             => $colorSchemeName,
                                'Title'            => _t(Config::class . '.ColorScheme_' . $colorSchemeName, ucfirst($colorSchemeName)),
                                'BackgroundColors' => ArrayList::create($backgroundColors),
                                'FontColors'       => ArrayList::create($fontColors),
                                'IsActive'         => $this->owner->ColorScheme == $colorSchemeName,
                    ]));
                }
                closedir($handle);
            }
            $colorSchemes->sort('Title');
            $fields->removeByName('ColorScheme');
            $showPaymentMethodsInFooterField  = CheckboxField::create('ShowPaymentMethodsInFooter', $this->owner->fieldLabel('ShowPaymentMethodsInFooter'));
            $showShippingMethodsInFooterField = CheckboxField::create('ShowShippingMethodsInFooter', $this->owner->fieldLabel('ShowShippingMethodsInFooter'));
            $logoField                        = UploadField::create('ShopLogo',        $this->owner->fieldLabel('ShopLogo'));
            $faviconField                     = UploadField::create('Favicon',         $this->owner->fieldLabel('Favicon'));
            $touchIconField                   = UploadField::create('MobileTouchIcon', $this->owner->fieldLabel('MobileTouchIcon'));
            $logoField->setDescription($this->owner->fieldLabel('ShopLogoDesc'));
            $faviconField->setDescription($this->owner->fieldLabel('FaviconDesc'));
            $touchIconField->setDescription($this->owner->fieldLabel('MobileTouchIconDesc'));
            // Build color scheme toggle group
            $colorSchemeConfigurationField = ToggleCompositeField::create(
                    'ColorSchemeConfiguration',
                    $this->owner->fieldLabel('ColorSchemeConfiguration'),
                    [
                        $fields->dataFieldByName('Title'),
                        $fields->dataFieldByName('Tagline'),
                        $showPaymentMethodsInFooterField,
                        $showShippingMethodsInFooterField,
                        $logoField,
                        $faviconField,
                        $touchIconField,
                        LiteralField::create('ColorScheme', $this->owner->customise(['ColorSchemes' => $colorSchemes])->renderWith('SilverCart/Admin/Forms/ColorSchemeField'))
                    ]
            )->setHeadingLevel(4)->setStartClosed(true);
            $this->owner->extend('updateCMSFieldsColorSchemeConfiguration', $colorSchemeConfigurationField);
            
            $fields->removeByName('Title');
            $fields->removeByName('Tagline');
            
            $fields->addFieldToTab('Root.Main', $colorSchemeConfigurationField);
        } else {
            $fields->removeByName('ColorScheme');
        }
    }
    
    /**
     * Sets the ColorScheme.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.02.2016
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $request     = Controller::curr()->getRequest();
        $colorScheme = $request->postVar('ColorScheme');
        if (is_string($colorScheme)) {
            $this->owner->ColorScheme = $colorScheme;
        }
    }

    /**
     * Duplicates the SilverCart based config into the translations.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     * @deprecated
     */
    public function onAfterWrite_deprecated()
    {
        parent::onAfterWrite();
        if (is_null(self::$duplicate_config_locale)) {
            self::$duplicate_config_locale = $this->owner->Locale;
            $changedFields = $this->owner->getChangedFields();
            $translations  = Tools::get_translations($this->owner);
            $dbAttributes  = array_keys(SiteConfig::config()->get('db'));
            foreach ($translations as $translation) {
                foreach ($changedFields as $changedFieldName => $changedFieldData) {
                    if (!in_array($changedFieldName, $dbAttributes)) {
                        continue;
                    }
                    $translation->{$changedFieldName} = $changedFieldData['after'];
                }
                $translation->write();
            }
        }
    }
    
    /**
     * Restores the config parameters out of the old SilvercartConfig object.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.12.2015
     */
    public function requireDefaultRecords()
    {
        $config = Config::getConfig();
        RequireDefaultRecords::require_default_records();
        $result = DB::query('SHOW TABLES LIKE \'SilvercartConfig\'');
        if ($result->numRecords() > 0) {
            $skipFields       = ['ID', 'ClassName', 'Created', 'LastEdited'];
            $silvercartConfig = DB::query('SELECT * FROM SilvercartConfig;');
            foreach ($silvercartConfig as $row) {
                foreach ($row as $fieldName => $fieldValue) {
                    if (in_array($fieldName, $skipFields)) {
                        continue;
                    } elseif ($fieldName == 'Locale') {
                        $config->DefaultLocale = $fieldValue;
                    } else {
                        $config->{$fieldName} = $fieldValue;
                    }
                }
                $config->write();
            }
            DB::query('DROP TABLE SilvercartConfig');
        }
        // Remove deprecated social media fields and add ExternalItems instead.
        $externalLinkFields = [
            'GoogleplusLink',
            'FacebookLink',
            'TwitterLink',
            'XingLink',
            'InstagramLink',
            'BloglovinLink',
            'PinterestLink',
            'YouTubeLink',
            'TumblrLink',
            'RSSLink',
            'EmailLink',
        ];
        $sort = 0;
        foreach ($externalLinkFields as $externalLinkField) {
            $externalLink = DBMigration::get_field_value_and_remove_field($config, $externalLinkField);
            if (empty($externalLink)) {
                continue;
            }
            $item               = LinkableItem::create();
            $item->Title        = $config->fieldLabel($externalLinkField);
            $item->Sort         = $sort++;
            $item->SiteConfigID = $config->ID;
            if ($externalLinkField === 'EmailLink') {
                $item->addEmailLink($externalLink);
            } else {
                $item->addExternalLink($externalLink);
            }
        }
    }

    /**
     * Returns whether the maintenance mode is enabled.
     * 
     * @param bool $disable Disable maintenance  mode flag to prevent infinite loops when triggering the HTTP error?
     * 
     * @return bool
     */
    public function MaintenanceModeIsEnabled(bool $disable = false) : bool
    {
        if (Controller::curr() instanceof Security) {
            return false;
        }
        $customer = Security::getCurrentUser();
        if ($customer instanceof Member
         && $customer->isAdmin()
        ) {
            return false;
        }
        if (self::$maintenanceModeIsDisabled) {
            return false;
        }
        if ($disable) {
            self::$maintenanceModeIsDisabled = true;
        }
        $enabled = false;
        if ($this->owner->MaintenanceMode) {
            if ($this->owner->MaintenanceStart === null
             || strtotime($this->owner->MaintenanceStart) < time()
            ) {
                $enabled = true;
            }
            if ($this->owner->MaintenanceEnd !== null
             && strtotime($this->owner->MaintenanceEnd) < time()
            ) {
                $enabled = false;
            }
        }
        return $enabled;
    }
    
    /**
     * Returns the i18n weight unit.
     * 
     * @return string
     */
    public function getWeightUnitNice() : string
    {
        $default = empty($this->owner->WeightUnit) ? '---' : $this->owner->WeightUnit;
        return _t(self::class . ".WeightUnit_{$this->owner->WeightUnit}", $default);
    }
    
    /**
     * Returns the i18n dimension unit.
     * 
     * @return string
     */
    public function getDimensionUnitNice() : string
    {
        $default = empty($this->owner->DimensionUnit) ? '---' : $this->owner->DimensionUnit;
        return _t(self::class . ".DimensionUnit_{$this->owner->DimensionUnit}", $default);
    }
    
    /**
     * Returns whether to enable SSL.
     * 
     * @return bool
     */
    public function getEnableSSL()
    {
        $enableSSL = $this->owner->getField('EnableSSL');
        if (!$this->getCMSFieldsIsCalled) {
            $this->owner->extend('updateEnableSSL', $enableSSL);
        }
        return $enableSSL;
    }
    
    // Put Config::Check() Methods here
    
    /**
     * Checks, whether an activated country exists or not.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.04.2011
     */
    public function checkActiveCountries()
    {
        $hasActiveCountries = false;
        /*
         * We have to bypass DataObject::get_one() because it would ignore active
         * countries without a translation of the current locale
         */
        $items = Country::get()->filter(["Active" => 1]);
        if ($items->count() > 0) {
            $hasActiveCountries = true;
        } else {
            RequireDefaultRecords::require_default_countries();
            $items = Country::get()->filter(["Active" => 1]);
            if ($items->count() > 0) {
                $hasActiveCountries = true;
            }
        }
        return [
            'status'  => $hasActiveCountries,
            'message' => _t(Config::class . '.ERROR_MESSAGE_NO_ACTIVATED_COUNTRY',
                    'No activated country found. Please <a href="{baseURL}admin/settings/">log in</a> and choose "Handling -> Countries" to activate a country.',
                    [
                        'baseURL' => Director::baseURL(),
                    ]
            )
        ];
    }
    
    /**
     * Returns the updated field value for the given $fieldName.
     * 
     * @return string|null
     */
    public function getUpdatedField(string $fieldName) : ?string
    {
        $value = $this->owner->getField($fieldName);
        if (!$this->getCMSFieldsIsCalled) {
            $this->owner->extend("update{$fieldName}", $value);
        }
        return $value;
    }
    
    /**
     * Returns the ShopName.
     * 
     * @return string|null
     */
    public function getShopName() : ?string
    {
        return $this->getUpdatedField('ShopName');
    }
    
    /**
     * Returns the ShopStreet.
     * 
     * @return string|null
     */
    public function getShopStreet() : ?string
    {
        return $this->getUpdatedField('ShopStreet');
    }
    
    /**
     * Returns the ShopStreetNumber.
     * 
     * @return string|null
     */
    public function getShopStreetNumber() : ?string
    {
        return $this->getUpdatedField('ShopStreetNumber');
    }
    
    /**
     * Returns the ShopPostcode.
     * 
     * @return string|null
     */
    public function getShopPostcode() : ?string
    {
        return $this->getUpdatedField('ShopPostcode');
    }
    
    /**
     * Returns the ShopCity.
     * 
     * @return string|null
     */
    public function getShopCity() : ?string
    {
        return $this->getUpdatedField('ShopCity');
    }
    
    /**
     * Returns the ShopCountry with extension support.
     * 
     * @return Country
     */
    public function ShopCountry() : Country
    {
        return $this->getShopCountry();
    }
    
    /**
     * Returns the ShopCountry with extension support.
     * 
     * @return Country
     */
    public function getShopCountry() : Country
    {
        $value = $this->owner->getComponent('ShopCountry');
        if (!$this->getCMSFieldsIsCalled) {
            $this->owner->extend("updateShopCountry", $value);
        }
        return $value;
    }
    
    /**
     * Returns the ShopPhone.
     * 
     * @return string|null
     */
    public function getShopPhone() : ?string
    {
        return $this->getUpdatedField('ShopPhone');
    }
    
    /**
     * Returns the ShopEmail.
     * 
     * @return string|null
     */
    public function getShopEmail() : ?string
    {
        return $this->getUpdatedField('ShopEmail');
    }
    
    /**
     * Returns the ShopOpeningHours.
     * 
     * @return string|null
     */
    public function getShopOpeningHours() : ?string
    {
        return $this->getUpdatedField('ShopOpeningHours');
    }
    
    /**
     * Returns the ShopAdditionalInfo.
     * 
     * @return string|null
     */
    public function getShopAdditionalInfo() : ?string
    {
        return $this->getUpdatedField('ShopAdditionalInfo');
    }
    
    /**
     * Returns the ShopAdditionalInfo2.
     * 
     * @return string|null
     */
    public function getShopAdditionalInfo2() : ?string
    {
        return $this->getUpdatedField('ShopAdditionalInfo2');
    }
    
    /**
     * Returns the DefaultMailOrderNotificationRecipient.
     * 
     * @return string|null
     */
    public function getDefaultMailOrderNotificationRecipient() : ?string
    {
        return $this->getUpdatedField('DefaultMailOrderNotificationRecipient');
    }
    
    /**
     * Returns the DefaultMailRevocationRecipient.
     * 
     * @return string|null
     */
    public function getDefaultMailRevocationRecipient() : ?string
    {
        return $this->getUpdatedField('DefaultMailRevocationRecipient');
    }
    
    /**
     * Returns the DefaultMailRegistrationRecipient.
     * 
     * @return string|null
     */
    public function getDefaultMailRegistrationRecipient() : ?string
    {
        return $this->getUpdatedField('DefaultMailRegistrationRecipient');
    }
    
    /**
     * Returns the DefaultContactMessageRecipient.
     * 
     * @return string|null
     */
    public function getDefaultContactMessageRecipient() : ?string
    {
        return $this->getUpdatedField('DefaultContactMessageRecipient');
    }
}