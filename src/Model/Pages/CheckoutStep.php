<?php

namespace SilverCart\Model\Pages;

use Page;
use SilverCart\Dev\Tools;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Pages\AddressHolder;
use SilverCart\Model\Pages\Page as SilverCartPage;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Model\Shipment\ShippingMethod;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Checkout step page.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $TermsAndConditionsText Terms And Conditions Text
 * @property string $ContentStep1           Content Step 1
 * @property string $ContentStep2           Content Step 2
 * @property string $ContentStep3           Content Step 3
 * @property string $ContentStep4           Content Step 4
 * @property string $ContentStep5           Content Step 5
 * @property string $ContentStep6           Content Step 6
 * @property string $TitleStep6             Title Step 6
 */
class CheckoutStep extends Page
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'ContentStep1'           => 'HTMLText',
        'ContentStep2'           => 'HTMLText',
        'ContentStep3'           => 'HTMLText',
        'ContentStep4'           => 'HTMLText',
        'ContentStep5'           => 'HTMLText',
        'NoPaymentMethodText'    => 'HTMLText',
        'NoShippingMethodText'   => 'HTMLText',
        'TermsAndConditionsText' => 'HTMLText',
        'TitleStep6'             => 'Varchar',
        'ContentStep6'           => 'HTMLText',
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartCheckoutStep';
    /**
     * icon for site tree
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/checkout_page-file.gif";
    
    /**
     * Field labels
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'StepContent'         => _t(CheckoutStep::class . '.StepContent', 'Content for single steps'),
            'Forward'             => _t(CheckoutStep::class . '.FORWARD', 'Next'),
            'OrderNow'            => _t(CheckoutStep::class . '.ORDER_NOW', 'Order now'),
            'ChosenPayment'       => _t(CheckoutStep::class . '.CHOSEN_PAYMENT', 'chosen payment method'),
            'ChosenShipping'      => _t(CheckoutStep::class . '.CHOSEN_SHIPPING', 'chosen shipping method'),
            'SubscribeNewsletter' => _t(CheckoutStep::class . '.I_SUBSCRIBE_NEWSLETTER', 'I subscribe to the newsletter'),
            'ThanksForYourOrder'  => _t(SilverCartPage::class . '.ORDER_THANKS', 'Many thanks for your order'),
            'Register'            => _t(SilverCartPage::class . '.REGISTER', 'Register'),
        ]);
    }

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }
    
    /**
     * CMS fields
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $this->getCMSFieldsIsCalled = true;
            $titleStep6Default = _t(SilverCartPage::class . '.ORDER_COMPLETED', 'Your order is completed');
            $noPaymentMethodTextDefault  = _t('SilverCart.DefaultIfEmpty', 'Default if empty: "{default}"', ['default' => $this->getDefaultNoPaymentMethodText()]);
            $noShippingMethodTextDefault = _t('SilverCart.DefaultIfEmpty', 'Default if empty: "{default}"', ['default' => $this->getDefaultNoShippingMethodText()]);
            $fields->findOrMakeTab('Root.StepContent', $this->fieldLabel('StepContent'));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep1', $this->fieldLabel('ContentStep1'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep2', $this->fieldLabel('ContentStep2'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep3', $this->fieldLabel('ContentStep3'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('NoShippingMethodText', $this->fieldLabel('NoShippingMethodText'))->setDescription($noShippingMethodTextDefault)->addExtraClass('stacked')->setRows(3));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep4', $this->fieldLabel('ContentStep4'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('NoPaymentMethodText', $this->fieldLabel('NoPaymentMethodText'))->setDescription($noPaymentMethodTextDefault)->addExtraClass('stacked')->setRows(3));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep5', $this->fieldLabel('ContentStep5'))->addExtraClass('stacked')->setRows(8));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('TermsAndConditionsText', $this->fieldLabel('TermsAndConditionsText'))->addExtraClass('stacked')->setRows(6)->setDescription($this->getDefaultTermsAndConditionsText()));
            $fields->addFieldToTab('Root.StepContent', TextField::create('TitleStep6', $this->fieldLabel('TitleStep6'))->setAttribute('placeholder', $titleStep6Default)->setDescription(_t(self::class . '.TitleStep6Info', 'Alternative title to display on the order confirmation page (default: "{default}").', ['default' => $titleStep6Default])));
            $fields->addFieldToTab('Root.StepContent', HTMLEditorField::create('ContentStep6', $this->fieldLabel('ContentStep6'))->addExtraClass('stacked')->setRows(8));
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the NoPaymentMethodText.
     * 
     * @return DBHTMLText
     */
    public function getNoPaymentMethodText() : DBHTMLText
    {
        $text = $this->getField('NoPaymentMethodText');
        if (!$this->getCMSFieldsIsCalled) {
            if ($text === null) {
                $text = DBHTMLText::create()->setValue($this->getDefaultNoPaymentMethodText());
            }
            if (!($text instanceof DBHTMLText)) {
                $text = DBHTMLText::create()->setValue($text);
            }
            $this->extend('updateNoPaymentMethodText', $text);
        }
        if (!($text instanceof DBHTMLText)) {
            $text = DBHTMLText::create()->setValue($text);
        }
        $text->setProcessShortcodes(true);
        return $text;
    }
    
    /**
     * Returns the NoShippingMethodText.
     * 
     * @return DBHTMLText
     */
    public function getNoShippingMethodText() : DBHTMLText
    {
        $text = $this->getField('NoShippingMethodText');
        if (!$this->getCMSFieldsIsCalled) {
            if ($text === null) {
                $text = DBHTMLText::create()->setValue($this->getDefaultNoShippingMethodText());
            }
            if (!($text instanceof DBHTMLText)) {
                $text = DBHTMLText::create()->setValue($text);
            }
            $this->extend('updateNoShippingMethodText', $text);
        }
        if (!($text instanceof DBHTMLText)) {
            $text = DBHTMLText::create()->setValue($text);
        }
        $text->setProcessShortcodes(true);
        return $text;
    }
    
    /**
     * Returns the terms and condition text.
     * 
     * @return DBHTMLText
     */
    public function getTermsAndConditionsText() : DBHTMLText
    {
        $text = $this->getField('TermsAndConditionsText');
        if (!$this->getCMSFieldsIsCalled) {
            if ($text === null) {
                $text = DBHTMLText::create()->setValue($this->getDefaultTermsAndConditionsText());
            }
            if (!($text instanceof DBHTMLText)) {
                $text = DBHTMLText::create()->setValue($text);
            }
            $this->extend('updateTermsAndConditionsText', $text);
        }
        if (!($text instanceof DBHTMLText)) {
            $text = DBHTMLText::create()->setValue($text);
        }
        $text->setProcessShortcodes(true);
        return $text;
    }
    
    /**
     * Returns the default NoPaymentMethodText.
     * 
     * @return string
     */
    public function getDefaultNoPaymentMethodText() : string
    {
        return _t(PaymentMethod::class . '.NO_PAYMENT_METHOD_AVAILABLE', 'No payment method available.');
    }
    
    /**
     * Returns the default NoShippingMethodText.
     * 
     * @return string
     */
    public function getDefaultNoShippingMethodText() : string
    {
        return _t(ShippingMethod::class . '.NO_SHIPPING_METHOD_AVAILABLE', 'No shipping method available.');
    }
    
    /**
     * Returns the default TermsAndConditionsText.
     * 
     * @return string
     */
    public function getDefaultTermsAndConditionsText() : string
    {
        return Tools::string2html(_t(CheckoutStep::class . '.AcceptTermsAndConditionsText',
                    'With your order you agree with our <a class="text-primary font-weight-bold" href="{termsAndConditionsLink}" target="blank">terms and conditions</a>. Please read and take note of our <a class="text-primary font-weight-bold" href="{privacyLink}" target="blank">data privacy statement</a> and <a class="text-primary font-weight-bold" href="{revocationLink}" target="blank">revocation instructions</a>',
                    [
                        'termsAndConditionsLink' => Tools::PageByIdentifierCodeLink(SilverCartPage::IDENTIFIER_TERMS_OF_SERVICE_PAGE),
                        'privacyLink'            => Tools::PageByIdentifierCodeLink(SilverCartPage::IDENTIFIER_DATA_PRIVACY_PAGE),
                        'revocationLink'         => Tools::PageByIdentifierCodeLink(SilverCartPage::IDENTIFIER_REVOCATION_INSTRUCTION_PAGE),
                    ]
        ));
    }
    
    /**
     * Returns the title for the checkout step 6.
     * 
     * @return string
     */
    public function getTitleStep6() : string
    {
        $title = $this->getField('TitleStep6');
        if (empty($title)
         && !$this->getCMSFieldsIsCalled
        ) {
            $title = _t(SilverCartPage::class . '.ORDER_COMPLETED', 'Your order is completed');
        }
        return (string) $title;
    }
    
    /**
     * Returns the meta title for the current checkout context.
     * 
     * @return string
     */
    public function getMetaTitle()
    {
        $metaTitle = $this->Title;
        $ctrl      = Controller::curr();
        $action    = $ctrl->getAction();
        /* @var $ctrl CheckoutStepController */
        switch ($action) {
            case 'step':
                $step = $ctrl->getCheckout()->getCurrentStep();
                if ($step->hasMethod('ShowRegistrationForm')
                 && $step->ShowRegistrationForm()) {
                    $stepTitle = $this->fieldLabel('Register');
                } else {
                    $stepTitle = $step->StepTitle();
                }
                $metaTitle = "{$stepTitle} - {$metaTitle}";
                break;
            case 'editAddress':
                $editAddress = Address::singleton()->fieldLabel('EditAddress');
                $metaTitle   = "{$editAddress} - {$metaTitle}";
                break;
            case 'addNewAddress':
                $addNewAddress = AddressHolder::singleton()->fieldLabel('AddNewAddress');
                $metaTitle   = "{$addNewAddress} - {$metaTitle}";
                break;
            case 'thanks':
                $metaTitle   = "{$this->fieldLabel('ThanksForYourOrder')} - {$metaTitle}";
                break;
            default:
                $metaTitle = $this->Title;
        }
        return $metaTitle;
    }
}