<?php

namespace SilverCart\Model;

use SilverCart\Admin\Dev\ExampleData;
use SilverCart\Admin\Model\Config;
use SilverCart\Dev\Tools;
use SilverCart\Model\EmailAddress;
use SilverCart\Model\ShopEmailTranslation;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\ORM\DataObjectExtension;
use SilverCart\View\SCTemplateParser;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTP;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;
use SilverStripe\View\SSViewer_FromString;

/**
 * base class for emails.
 *
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ShopEmail extends DataObject {
    
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'TemplateName' => 'Varchar',
    ];

    /**
     * n:1 relations
     * 
     * @var type array
     */
    private static $has_many = [
        'ShopEmailTranslations' => ShopEmailTranslation::class,
    ];
    
    /**
     * n:m relations
     * 
     * @var type array
     */
    private static $many_many = [
        'AdditionalReceipients' => EmailAddress::class,
    ];
    
    /**
     * n:m relations
     *
     * @var array
     */
    private static $belongs_many_many = [
        'OrderStatus' => OrderStatus::class,
    ];

    /**
     * Casted properties
     *
     * @var array
     */
    private static $casting = [
        'Subject'                        => 'Text',
        'AdditionalRecipientsHtmlString' => 'HtmlText',
        'TemplateNameTitle'              => 'Text',
    ];

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartShopEmail';
    
    /**
     * Alternative email address to use as universal recipient in dev mode.
     * The original recipient address will be overwritten and added to the subject.
     *
     * @var string
     */
    private static $dev_email_recipient = '';
    
    /**
     * List of the email templates.
     *
     * @var array
     */
    private static $email_templates = [];
    
    /**
     * List of the registered email templates.
     *
     * @var array
     */
    private static $registered_email_templates = [];

    /**
     * Key value pair of CSS styles to use as inline styles in emails.
     *
     * @var array
     */
    protected static $style = [];
    
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
     * Returns the email subject and ID as title to show in backend.
     * 
     * @return string
     */
    public function getTitle() {
        return $this->Subject . ' (' . parent::getTitle() . ')';
    }

    /**
     * Get any user defined searchable fields labels that
     * exist. Allows overriding of default field names in the form
     * interface actually presented to the user.
     *
     * The reason for keeping this separate from searchable_fields,
     * which would be a logical place for this functionality, is to
     * avoid bloating and complicating the configuration array. Currently
     * much of this system is based on sensible defaults, and this property
     * would generally only be set in the case of more complex relationships
     * between data object being required in the search interface.
     *
     * Generates labels based on name of the field itself, if no static property
     * {@link self::field_labels} exists.
     *
     * @param boolean $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array|string Array of all element labels if no argument given, otherwise the label of the field
     *
     * @uses $field_labels
     * @uses FormField::name_to_label()
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            [
                'TemplateName'          => _t(ShopEmail::class . '.TemplateName', 'Template Name'),
                'Subject'               => _t(ShopEmail::class . '.SUBJECT', 'Subject'),
                'AdditionalReceipients' => _t(ShopEmail::class . '.ADDITIONALS_RECEIPIENTS', 'Additional recipients'),
                'AdditionalRecipients'  => _t(ShopEmail::class . '.ADDITIONALS_RECEIPIENTS', 'Additional recipients'),
                'Preview'               => _t(ShopEmail::class . '.Preview', 'Preview'),
                'ShopEmailTranslations' => _t(ShopEmailTranslation::class . '.PLURALNAME', 'Translations'),
                'OrderStatus'           => _t(OrderStatus::class . '.PLURALNAME', 'Order status'),
            ]
        );
        
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Get the default summary fields for this object.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function  summaryFields() {
        $summaryFields = [
            'TemplateNameTitle'              => $this->fieldLabel('TemplateName'),
            'Subject'                        => $this->fieldLabel('Subject'),
            'AdditionalRecipientsHtmlString' => $this->fieldLabel('AdditionalRecipients'),
        ];
        
        $this->extend('updateSummary', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * input fields for backend manipulation
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = DataObjectExtension::getCMSFields($this, 'TemplateName', true);
        
        $fields->removeByName('TemplateName');
        $templateNames     = self::get_email_templates();
        $templateNameField = new DropdownField('TemplateName', $this->fieldLabel('TemplateName'), $templateNames);
        $fields->insertBefore('Subject', $templateNameField);
        
        $exampleEmail = ExampleData::render_example_email($this->TemplateName);
        if (!empty($exampleEmail)) {
            $fields->findOrMakeTab('Root.Preview', $this->fieldLabel('Preview'));
            $frame = '<iframe class="full-height" src="' . Director::absoluteURL('example-data/renderemail/' . $this->TemplateName) . '"></iframe>';
            $fields->addFieldToTab('Root.Preview', new LiteralField('Preview', $frame));
        }
        
        return $fields;
    }
    
    /**
     * Requires the default records.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2018
     */
    public function requireDefaultRecords() {
        $emailTemplates = self::get_email_templates();
        foreach ($emailTemplates as $templateName => $templateTitle) {
            $email = ShopEmail::get()->filter('TemplateName', $templateName)->first();
            if (!($email instanceof ShopEmail) ||
                !$email->exists()) {
                $email = new ShopEmail();
                $email->TemplateName = $templateName;
                $email->Subject      = _t(ShopEmail::class . '.Subject_' . $templateName, $templateTitle);
                $email->write();
            }
        }
        
        $orderStatus   = OrderStatus::get()->filter('Code', 'shipped')->sort('ID')->first();
        $shippingEmail = ShopEmail::get()->filter('TemplateName', 'OrderShippedNotification')->sort('ID')->first();
        
        if ($orderStatus instanceof OrderStatus &&
            $shippingEmail instanceof ShopEmail &&
            !$orderStatus->ShopEmails()->find('ID', $shippingEmail->ID)) {
            $orderStatus->ShopEmails()->add($shippingEmail);
        }
    }
    
    /**
     * Returns the page with the given $identifierCode.
     * 
     * @param string $identifierCode Identifier code
     * 
     * @return \Page|null
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.05.2019
     */
    public function PageByIdentifierCode(string $identifierCode) : ?\Page
    {
        return Tools::PageByIdentifierCode($identifierCode);
    }

    /**
     * Returns the available email template names.
     * 
     * @return array
     */
    public static function get_email_templates() {
        if (empty(self::$email_templates)) {
            self::scan_email_templates();
            foreach (self::get_registered_email_templates() as $templateName => $templateNameTitle) {
                if ($templateNameTitle == $templateName) {
                    $templateNameTitle = self::get_template_name_title($templateName);
                }
                self::$email_templates[$templateName] = $templateNameTitle;
            };
            asort(self::$email_templates);
        }
        return self::$email_templates;
    }
    
    /**
     * Returns the registered email templates.
     * 
     * @return array
     */
    public static function get_registered_email_templates() {
        return self::$registered_email_templates;
    }

    /**
     * Registers an email template.
     * Templates have to be placed in /module-path/templates/SilverCart/Email/Layout/TemplateName.ss.
     * 
     * @param string $templateName      Name of the email template
     * @param string $templateNameTitle i18n title of the email template
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.04.2018
     */
    public static function register_email_template($templateName, $templateNameTitle = null) {
        if (is_null($templateNameTitle)) {
            $templateNameTitle = $templateName;
        }
        self::$registered_email_templates[$templateName] = $templateNameTitle;
    }
    
    /**
     * Registers an array of email templates.
     * <code>
     * [
     *     'EmailTemplateName' => 'EmailTemplateTitle'
     * ];
     * </code>
     * 
     * @param array $templateNames Email template names.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.04.2018
     */
    public static function register_email_templates(array $templateNames) {
        foreach ($templateNames as $templateName => $templateNameTitle) {
            if (is_numeric($templateName)) {
                $templateName      = $templateNameTitle;
                $templateNameTitle = null;
            }
            self::register_email_template($templateName, $templateNameTitle);
        }
    }

    /**
     * Scans the file system for default email templates.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.05.2019
     */
    public static function scan_email_templates() {
        self::$email_templates = [];
        
        $emailTemplatesPaths = [];
        $emailDirParts       = [
            'templates',
            'SilverCart',
            'Email',
            'Layout',
        ];
        $currentDirParts     = explode('/', __DIR__);
        array_pop($currentDirParts);
        array_pop($currentDirParts);
        $templateDirParts = array_merge(
                $currentDirParts,
                $emailDirParts
        );
        $emailTemplatesPaths[] = implode(DIRECTORY_SEPARATOR, $templateDirParts);
        $frontendThemes = SSViewer::config()->themes;
        foreach ($frontendThemes as $theme) {
            $path = THEMES_PATH . DIRECTORY_SEPARATOR . $theme;
            if (is_dir($path)) {
                $emailTemplatesPaths[] = $path . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $emailDirParts);
            }
        }
        foreach ($emailTemplatesPaths as $emailTemplatesPath) {
            if (is_dir($emailTemplatesPath)) {
                $handle = opendir($emailTemplatesPath);
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, -3) != '.ss') {
                        continue;
                    }

                    $templateName = substr($entry, 0, -3);
                    //$templateNames[$templateName] = _t(static::class . '.TemplateName_' . $templateName, ucfirst($templateName));
                    self::$email_templates[$templateName] = self::get_template_name_title($templateName);
                }
                closedir($handle);
            }
        }
        asort(self::$email_templates);
    }
    
    /**
     * Returns the Subject
     *
     * @return string
     */
    public function getSubject() {
        return $this->getTranslationFieldValue('Subject');
    }
    
    /**
     * Returns the additional email recipients as a html string
     * 
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getAdditionalRecipientsHtmlString() {
        $additionalRecipientsArray = [];
        if ($this->AdditionalReceipients()->exists()) {
            foreach ($this->AdditionalReceipients() as $additionalRecipient) {
                $additionalRecipientsArray[] = htmlentities($additionalRecipient->getEmailAddressWithName());
            }
        }
        return Tools::string2html(implode('<br/>', $additionalRecipientsArray));
    }
    
    /**
     * Returns the template name title (i18n).
     * 
     * @param string $templateName Optional template name
     * 
     * @return string
     */
    public function getTemplateNameTitle($templateName = null) {
        if (is_null($templateName)) {
            $templateName = $this->TemplateName;
        }
        return self::get_template_name_title($templateName);
    }
    
    /**
     * Returns the template name title (i18n).
     * 
     * @param string $templateName Optional template name
     * 
     * @return string
     */
    public static function get_template_name_title($templateName = null) {
        $templateNameTitle = '';
        if (!empty($templateName)) {
            $templateNameTitle = _t(static::class . '.TemplateName_' . $templateName, $templateName);
        }
        return $templateNameTitle;
    }

    /**
     * sends email to defined address
     *
     * @param string $identifier  identifier for email template
     * @param string $to          recipients email address
     * @param array  $variables   array with template variables that can be called in the template
     * @param array  $attachments absolute filename to an attachment file
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.06.2014
     */
    public static function send(string $identifier, string $to, array $variables = [], array $attachments = null, string $locale = null) : bool
    {
        $originalLocale = null;
        if ($locale !== null) {
            $originalLocale = Tools::current_locale();
            i18n::set_locale($locale);
            Tools::set_current_locale($locale);
        }
        $email = ShopEmail::get()->filter('TemplateName', $identifier)->first();

        if (!($email instanceof ShopEmail)
         || !$email->exists()
        ) {
            return false;
        }
        $rawSubject = trim($email->Subject);
        if (is_null($rawSubject)
         || empty($rawSubject)
        ) {
            return false;
        }
        if (!is_array($variables)) {
            $variables = [];
        }
        DataObject::reset();
        foreach ($variables as $variable) {
            if ($variable instanceof DataObject) {
                if ($variable->hasMethod('reset_field_labels')) {
                    $variable->reset_field_labels();
                    break;
                } elseif ($variable->hasMethod('reset')) {
                    $variable->reset();
                    break;
                }
            }
        }
        SCTemplateParser::config()->set('disable_field_label_cache', true);
        Requirements::clear();
        $frontendThemes = SSViewer::config()->themes;
        $adminThemes    = SSViewer::get_themes();
        SSViewer::set_themes($frontendThemes);
        $subject = HTTP::absoluteURLs(SSViewer_FromString::create($rawSubject)->process(ArrayData::create($variables)));
        $variables['ShopEmailSubject'] = $subject;
        $htmlText = $email->customise($variables)->renderWith(['SilverCart/Email/' . $identifier, 'SilverCart/Email/ShopEmail']);
        if (SSViewer::hasTemplate(['SilverCart/Email/Layout/' . $identifier . 'Plain'])) {
            $plainText = $email->customise($variables)->renderWith(['SilverCart/Email/' . $identifier . 'Plain', 'SilverCart/Email/ShopEmailPlain']);
        } else {
            $plainText = strip_tags($htmlText);
        }
        SSViewer::set_themes($adminThemes);
        Requirements::restore();
        SCTemplateParser::config()->set('disable_field_label_cache', false);
        
        if (empty($plainText)) {
            $plainText = strip_tags($htmlText);
        }
        
        $result = self::send_email($to, $subject, $htmlText, $attachments);
        
        if (Config::GlobalEmailRecipient() != '') {
            self::send_email(Config::GlobalEmailRecipient(), $subject, $htmlText);
        }

        //Send the email to additional standard receipients from the n:m
        //relation AdditionalReceipients;
        //Email address is validated.
        if ($email->AdditionalReceipients()->exists()) {
            foreach ($email->AdditionalReceipients() as $additionalReceipient) {
                if ($additionalReceipient->getEmailAddressWithName()
                 && Email::is_valid_address($additionalReceipient->Email)
                ) {
                    $to = [$additionalReceipient->Email => $additionalReceipient->Name];
                } elseif ($additionalReceipient->getEmailAddress()
                 && Email::is_valid_address($additionalReceipient->Email)
                ) {
                    $to = $additionalReceipient->getEmailAddress();
                } else {
                    continue;
                }
                self::send_email($to, $subject, $htmlText, $attachments);
            }
        }
        
        $additionalReceipients = [];
        ShopEmail::singleton()->extend('addAdditionalRecipients', $additionalReceipients);
        if (is_array($additionalReceipients)) {
            foreach ($additionalReceipients as $recipient) {
                self::send_email($recipient, $subject, $htmlText, $attachments);
            }
        }
        if ($originalLocale !== null) {
            i18n::set_locale($originalLocale);
            Tools::set_current_locale($originalLocale);
        }
        return $result;
    }
    
    /**
     * Sends the email with the given recipient, subject, content and attachments.
     * 
     * @param string $recipient   Recipient
     * @param string $subject     Subject
     * @param string $content     Content
     * @param array  $attachments Attachments
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.08.2018
     */
    public static function send_email($recipient, string $subject, string $content, array $attachments = null) : bool
    {
        if (Director::isDev()) {
            $devEmailRecipient = self::config()->get('dev_email_recipient');
            if (!empty($devEmailRecipient)) {
                $originalRecipient       = $recipient;
                $originalRecipientString = "";
                $recipient               = $devEmailRecipient;
                if (is_array($originalRecipient)) {
                    foreach ($originalRecipient as $emailAddress => $name) {
                        $originalRecipientString .= " \"{$name} <{$emailAddress}>\"";
                    }
                } else {
                    $originalRecipientString = " {$originalRecipient}";
                }
                $subject = "{$subject} [original recipient:{$originalRecipientString}]";
            }
        }
        if (!is_array($recipient)
         && !Email::is_valid_address($recipient)
        ) {
            return false;
        }
        $email = Email::create(
            Config::EmailSender(),
            $recipient,
            $subject,
            $content
        );
        $email->setFrom(Config::EmailSender(), Config::EmailSenderName());
        if (!is_null($attachments)) {
            self::attachFiles($email, $attachments);
        }
        return $email->send();
    }
    
    /**
     * Attaches the given files to the given email.
     *
     * @param Email $email       Email
     * @param array $attachments Attachments
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.07.2019
     */
    protected static function attachFiles(Email $email, $attachments) : void
    {
        if (!is_null($attachments)) {
            if (is_array($attachments)) {
                foreach ($attachments as $attachment) {
                    if (is_array($attachment)) {
                        $filename           = str_replace('//', '/', $attachment['filename']);
                        $attachedFilename   = array_key_exists('attachedFilename', $attachment) ? $attachment['attachedFilename'] : basename($filename);
                        $mimetype           = array_key_exists('mimetype', $attachment) ? $attachment['mimetype'] : null;
                    } else {
                        $filename           = str_replace('//', '/', $attachment);
                        $attachedFilename   = basename($attachment);
                        $mimetype           = null;
                    }
                    if (file_exists($filename)) {
                        $email->addAttachment($filename, $attachedFilename, $mimetype);
                    }
                }
            } else {
                $filename = str_replace('//', '/', $attachments);
                if (file_exists($filename)) {
                    $email->addAttachment($filename, basename($filename));
                }
            }
        }
    }

    /**
     * populates the template with the defined and called variables
     *
     * @param string $text      text with the template variables
     * @param array  $variables array with template variables
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2010
     */
    public static function populateTemplate($text, $variables) {

        if (!is_array($variables)) {
            return $text;
        }

        foreach ($variables as $placeholder => $value) {
            $text = str_replace('$' . $placeholder . '$', $value, $text);
        }

        return $text;
    }
    
    /**
     * The given $content will be parsed with a reduced variant of the SilverStripe
     * default template parsing engine to get localized email templates. Parsing 
     * the template like that keeps other template mechanisms like <% if ... %>
     * or <% control ... %> alive to be parsed on processing the real email 
     * sending.
     *
     * @param string $content The content to parse
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2011
     */
    public static function parse($content) {
        // i18n _t(...)
        $plainPattern = '<' . '% +_t\((\'([^\']*)\'|"([^"]*)")(([^)]|\)[^ ]|\) +[^% ])*)\) +%' . '>';
        $pattern = '/' . $plainPattern . '/';
        preg_match_all($pattern, $content, $matches);
        if (is_array($matches[0])) {
            foreach ($matches[0] as $index => $match) {
                $content = str_replace($match, _t($matches[2][$index]), $content);
            }
        }        
        return $content;
    }
    
    /**
     * Adds a key value pair of css inline styles to use in an email.
     * Replaces existing styles if the $name key already exists.
     * 
     * @param string $name  Name key
     * @param string $style CSS style
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.04.2018
     */
    public static function add_style($name, $style) {
        self::$style[$name] = $style;
    }
    
    /**
     * Adds a key value pair of css inline styles to use in an email.
     * Won't replace existing styles.
     * 
     * @param string $name  Name key
     * @param string $style CSS style
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.04.2018
     */
    public static function add_style_if_not_exists($name, $style) {
        if (!array_key_exists($name, self::$style)) {
            self::add_style($name, $style);
        }
    }
    
    /**
     * Returns the style with the given $name key.
     * $name can contain multiple key separated by a comma.
     * 
     * @param string $name Name key (optionally comma separated)
     * 
     * @return string
     */
    public function getStyle($name) {
        $finalStyle = '';
        if (strpos($name, ',') === false) {
            $styles = [$name];
        } else {
            $styles = explode(',', $name);
        }
        foreach ($styles as $style) {
            if (array_key_exists($style, self::$style)) {
                $finalStyle .= self::$style[$style];
            }
        }
        return $finalStyle;
    }
    
}