<?php

namespace SilverCart\Admin\Dev;

use SilverCart\Admin\Model\Config;
use SilverCart\Model\ContactMessage;
use SilverCart\Model\ShopEmail;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\NumberRange;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderPosition;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\Model\Payment\PaymentStatus;
use SilverCart\Model\Product\Product;
use SilverCart\Model\Product\Tax;
use SilverStripe\Control\Director;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\View\Requirements;

/**
 * Provides example data for documentation or example display purposes.
 *
 * @package SilverCart
 * @subpackage Admin_Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ExampleData
{
    use \SilverStripe\Core\Extensible;
    /**
     * Registerd email example data.
     * Use this to inject email example data from external modules.
     * <code>
     * [
     *     'TemplateName' => function() {
     *         //callback fucntion
     *         return [
     *             'EmailTemplateVariable' => 'EmailTemplateValue',
     *         ];
     *     }
     * ]
     * </code>
     *
     * @var array
     */
    protected static $registered_email_example_data = [];
    
    /**
     * Returns an example order.
     * 
     * @return Order
     */
    public static function get_order() : Order
    {
        $order                               = Order::singleton();
        $orderStatus                         = array_keys(OrderStatus::get()->map()->toArray());
        $orderStatusID                       = $orderStatus[rand(0, count($orderStatus) - 1)];
        $paymentStatus                       = array_keys(PaymentStatus::get()->map()->toArray());
        $paymentStatusID                     = $paymentStatus[rand(0, count($paymentStatus) - 1)];
        $order->CustomersEmail               = 'email@example.com';
        $order->Created                      = date('Y-m-d H:i:s');
        $order->OrderNumber                  = NumberRange::getByIdentifier('OrderNumber')->ActualNumber;
        $order->OrderStatusID                = $orderStatusID;
        $order->PaymentStatusID              = $paymentStatusID;
        $order->HandlingCostShipmentAmount   = 4.9;
        $order->HandlingCostShipmentCurrency = Config::DefaultCurrency();
        $order->TaxRateShipment              = 19;
        $order->TaxAmountShipment            = 0.78;
        $order->HandlingCostPaymentAmount    = 0;
        $order->HandlingCostPaymentCurrency  = Config::DefaultCurrency();
        $order->TaxRatePayment               = 19;
        $order->TaxAmountPayment             = 0;
        $order->PriceType                    = Customer::currentUser()->getPriceType();
        $order->TrackingCode                 = "ABCD-EFGH-0987-1234";
        $order->TrackingLink                 = Director::absoluteURL('shipment/track/ABCD-EFGH-0987-1234');
        self::get_address($order->ShippingAddress());
        self::get_address($order->InvoiceAddress());
        self::add_order_positions($order);
        self::add_shipping_method($order);
        $order->AmountTotalAmount            = $order->calculateAmountTotal();
        $order->AmountTotalCurreny           = Config::DefaultCurrency();
        return $order;
    }
    
    /**
     * Adds example positions to the given order.
     * 
     * @param Order $order Order
     * 
     * @return void
     */
    protected static function add_order_positions(Order $order) : void
    {
        $positions = [
            [
                'PriceAmount'                        => 9.99,
                'PriceCurrency'                      => Config::DefaultCurrency(),
                'PriceTotalAmount'                   => 39.96,
                'PriceTotalCurrency'                 => Config::DefaultCurrency(),
                'isChargeOrDiscount'                 => false,
                'isIncludedInTotal'                  => false,
                'chargeOrDiscountModificationImpact' => 'none',
                'Tax'                                => 1.6,
                'TaxTotal'                           => 6.4,
                'TaxRate'                            => 19,
                'ProductDescription'                 => 'Lorem Ipsum Dolor Sit Amet.',
                'Quantity'                           => 4,
                'Title'                              => 'Product Title',
                'ProductNumber'                      => 'NO-123-4567-89',
                'numberOfDecimalPlaces'              => 0,
                'IsNonTaxable'                       => false,
            ],
        ];
        foreach ($positions as $position) {
            $order->OrderPositions()->add(OrderPosition::create($position));
        }
    }
    
    /**
     * Returns an example product.
     * 
     * @return Product
     */
    public static function get_product() : Product
    {
        $product = Product::singleton();
        $product->isActive                    = true;
        $product->ProductNumberShop           = 'SKU-0123-ABC-45';
        $product->EANCode                     = '0123456789012';
        $product->StockQuantity               = 15;
        $product->PriceGrossAmount            = 9.95;
        $product->PriceGrossCurrency          = 'EUR';
        $product->PriceNetAmount              = 6.95;
        $product->PriceNetCurrency            = 'EUR';
        $product->MSRPriceAmount              = 12.95;
        $product->MSRPriceCurrency            = 'EUR';
        $productTranslation                   = $product->getTranslation(true);
        $productTranslation->Title            = 'Example Product';
        $productTranslation->ShortDescription = 'Lorem Ipsum Dolor Sit Amet.';
        $productTranslation->LongDescription  = '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p>'
                . '<p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi.</p>';
        return $product;
    }
    
    /**
     * Returns an example member.
     * 
     * @return Member
     */
    public static function get_member() : Member
    {
        $member = Member::singleton();
        $member->FirstName = 'John';
        $member->Surname   = 'Doe';
        $member->Email     = 'email@example.com';
        $member->RegistrationOptInConfirmationHash = $member->createOptInConfirmationHash();
        return $member;
    }
    
    /**
     * Adds an example shipping method to the given order.
     * 
     * @param Order $order Order
     * 
     * @return void
     */
    protected static function add_shipping_method(Order $order) : void
    {
        $shippingMethod = $order->ShippingMethod();
        $shippingMethod->isActive         = true;
        $shippingMethod->isPickup         = false;
        $shippingMethod->priority         = 1;
        $shippingMethod->DeliveryTimeMin  = 1;
        $shippingMethod->DeliveryTimeMax  = 3;
        $shippingMethod->DeliveryTimeText = '';
        $shippingMethodTranslation        = $shippingMethod->getTranslation(true);
        $shippingMethodTranslation->Title = 'Express Delivery';
        $carrierTranslation               = $shippingMethod->Carrier()->getTranslation(true);
        $carrierTranslation->Title        = 'Carrier';
    }
    
    /**
     * Returns an example tax rate.
     * 
     * @param Tax $tax Optional tax rate to fill with example data
     * 
     * @return Tax
     */
    public static function get_tax(Tax $tax = null) : Tax
    {
        if (is_null($tax)) {
            $tax = Tax::singleton();
        }
        $tax->Rate = 19;
        $taxTranslation = $tax->getTranslation(true);
        $taxTranslation->Title = '19%';
        return $tax;
    }
    
    /**
     * Returns an example address.
     * 
     * @param Address $address Optional address to fill with example data
     * 
     * @return Address
     */
    public static function get_address(Address $address = null) : Address
    {
        if (is_null($address)) {
            $address = Address::singleton();
        }
        $addressData = self::get_address_data();
        $address->Salutation    = $addressData['Salutation'];
        $address->AcademicTitle = $addressData['AcademicTitle'];
        $address->FirstName     = $addressData['FirstName'];
        $address->Surname       = $addressData['Surname'];
        $address->Street        = $addressData['Street'];
        $address->StreetNumber  = $addressData['StreetNumber'];
        $address->Postcode      = $addressData['Postcode'];
        $address->City          = $addressData['City'];
        $address->CountryID     = $addressData['CountryID'];
        $address->Phone         = $addressData['Phone'];
        $address->Fax           = $addressData['Fax'];
        $address->Email         = $addressData['Email'];
        return $address;
    }
    
    /**
     * Returns an example contact message.
     * 
     * @param ContactMessage $contactMessage Contact message
     * 
     * @return ContactMessage
     */
    public static function get_contact_message(ContactMessage $contactMessage = null) : ContactMessage
    {
        if (is_null($contactMessage)) {
            $contactMessage = ContactMessage::singleton();
        }
        $addressData = self::get_address_data();
        $contactMessage->Salutation    = $addressData['Salutation'];
        $contactMessage->FirstName     = $addressData['FirstName'];
        $contactMessage->Surname       = $addressData['Surname'];
        $contactMessage->Street        = $addressData['Street'];
        $contactMessage->StreetNumber  = $addressData['StreetNumber'];
        $contactMessage->Postcode      = $addressData['Postcode'];
        $contactMessage->City          = $addressData['City'];
        $contactMessage->CountryID     = $addressData['CountryID'];
        $contactMessage->Phone         = $addressData['Phone'];
        $contactMessage->Email         = $addressData['Email'];
        $contactMessage->Message       = self::get_text();
        return $contactMessage;
    }
    
    /**
     * Returns an example text.
     * 
     * @return string
     */
    public static function get_text() : string
    {
        return 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.<br/>' . PHP_EOL
                . '<br/>' . PHP_EOL
                . 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.';
    }

    /**
     * Returns example address data.
     * 
     * @return array
     */
    public static function get_address_data() : array
    {
        $addressData = [];
        switch (rand(0, 1)) {
            case 0:
                $addressData['Salutation'] = 'Herr';
                $addressData['FirstName']  = 'John';
                break;
            default:
                $addressData['Salutation'] = 'Frau';
                $addressData['FirstName']  = 'Jane';
                break;
        }
        switch (rand(0, 4)) {
            case 0:
                $addressData['AcademicTitle'] = 'Prof. Dr.';
                break;
            case 1:
                $addressData['AcademicTitle'] = 'Dipl. Ing.';
                break;
            case 2:
                $addressData['AcademicTitle'] = 'Dr. Med.';
                break;
            case 3:
                $addressData['AcademicTitle'] = '';
                break;
            default:
                $addressData['AcademicTitle'] = 'Dr.';
                break;
        }
        $addressData['Surname']       = 'Doe';
        $addressData['Street']        = 'Example Avenue';
        $addressData['StreetNumber']  = '1';
        $addressData['Postcode']      = '12345';
        $addressData['City']          = 'Example Town';
        $addressData['CountryID']     = Country::get()->sort('RAND()')->first()->ID;
        $addressData['Phone']         = '01234 5678-9';
        $addressData['Fax']           = '01234 5678-0';
        $addressData['Email']         = 'email@example.com';
        return $addressData;
    }
    
    /**
     * Returns example data to render an email preview.
     * 
     * @param string $templateName Template name
     * 
     * @return array
     */
    public static function get_email_example_data(string $templateName) : array
    {
        switch ($templateName) {
            case 'OrderTrackingNotification':
            case 'OrderShippedNotification':
            case 'OrderNotification':
            case 'OrderConfirmation':
                $order = static::get_order();
                $data  = [
                    'SalutationText' => $order->InvoiceAddress()->SalutationText,
                    'AcademicTitle'  => $order->InvoiceAddress()->AcademicTitle,
                    'FirstName'      => $order->InvoiceAddress()->FirstName,
                    'Surname'        => $order->InvoiceAddress()->Surname,
                    'Order'          => $order,
                ];
                break;
            case 'ContactMessage':
            case 'ContactMessageAcknowledgement':
                $contactMessage = static::get_contact_message();
                $data  = [
                    'Salutation'     => $contactMessage->Salutation,
                    'FirstName'      => $contactMessage->FirstName,
                    'Surname'        => $contactMessage->Surname,
                    'Street'         => $contactMessage->Street,
                    'StreetNumber'   => $contactMessage->StreetNumber,
                    'Postcode'       => $contactMessage->Postcode,
                    'City'           => $contactMessage->City,
                    'Country'        => $contactMessage->Country(),
                    'Phone'          => $contactMessage->Phone,
                    'Email'          => $contactMessage->Email,
                    'Message'        => $contactMessage->Message,
                    'ContactMessage' => $contactMessage,
                ];
                break;
            case 'NewsletterOptIn':
            case 'NewsletterOptInConfirmation':
                $data  = [
                    'ConfirmationLink' => '#',
                ];
                break;
            case 'RegistrationOptIn':
            case 'RegistrationOptInConfirmation':
            case 'RegistrationOptInNotification':
                $customer = Customer::currentRegisteredCustomer();
                if (!$customer) {
                    $customer = static::get_member();
                }
                $data  = [
                    'Customer' => $customer,
                ];
                break;
            case 'RevocationConfirmation':
            case 'RevocationNotification':
                $config  = Config::getConfig();
                $address = static::get_address();
                $data = [
                    'Email'               => $address->Email,
                    'Salutation'          => $address->SalutationText,
                    'FirstName'           => $address->FirstName,
                    'Surname'             => $address->Surname,
                    'Street'              => $address->Street,
                    'StreetNumber'        => $address->StreetNumber,
                    'Addition'            => $address->Addition,
                    'Postcode'            => $address->Postcode,
                    'City'                => $address->City,
                    'Country'             => $address->Country(),
                    'OrderDate'           => date('Y-m-d H:i:s'),
                    'OrderNumber'         => '123456789',
                    'RevocationOrderData' => static::get_text(),
                    'CurrentDate'         => date(_t(Tools::class . '.DATEFORMAT', 'm/d/Y')),
                    'ShopName'            => $config->ShopName,
                    'ShopStreet'          => $config->ShopStreet,
                    'ShopStreetNumber'    => $config->ShopStreetNumber,
                    'ShopPostcode'        => $config->ShopPostcode,
                    'ShopCity'            => $config->ShopCity,
                    'ShopCountry'         => $config->getShopCountry(),
                ];
                break;
            case 'ChangePassword':
            case 'ForgotPassword':
                $address = static::get_address();
                $data  = [
                    'SalutationText'    => $address->SalutationText,
                    'AcademicTitle'     => $address->AcademicTitle,
                    'FirstName'         => $address->FirstName,
                    'Surname'           => $address->Surname,
                    'PasswordResetLink' => Director::absoluteBaseURL(),
                ];
                break;
            default:
                $data = [];
                foreach (self::get_registered_email_example_data() as $registeredTemplateName => $registerdCallback) {
                    if ($registeredTemplateName == $templateName) {
                        $data = $registerdCallback();
                        break;
                    }
                }
                break;
        }
        return $data;
    }

    /**
     * Renders the example email with the given template name.
     * 
     * @param string $templateName Template name
     * 
     * @return DBHTMLText
     */
    public static function render_example_email(string $templateName) : DBHTMLText
    {
        $emailExampleData = static::get_email_example_data($templateName);
        $emailTemplatePreview = '';
        if (!empty($emailExampleData)) {
            Requirements::clear();
            $email = ShopEmail::get()->filter('TemplateName', $templateName)->first();
            if ($email === null) {
                $email                = ShopEmail::singleton();
                $email->TemplateName  = $templateName;
            }
            $emailTemplatePreview = $email
                    ->customise($emailExampleData)
                    ->renderWith(['SilverCart/Email/' . $templateName, 'SilverCart/Email/ShopEmail']);
            Requirements::restore();
        }
        return $emailTemplatePreview;
    }

    /**
     * Registers email example data.
     * Use this to inject email example data from external modules.
     * <code>
     * function() {
     *     //example callback fucntion
     *     return [
     *         'EmailTemplateVariable' => 'EmailTemplateValue',
     *     ];
     * }
     * </code>
     * 
     * @param string   $templateName Template name
     * @param function $callback     Callback function
     * 
     * @return void
     */
    public static function register_email_example_data(string $templateName, callable $callback) : void
    {
        self::$registered_email_example_data[$templateName] = $callback;
    }
    
    /**
     * Returns the registerd email example data.
     * 
     * @return array
     */
    public static function get_registered_email_example_data() : array
    {
        return (array) self::$registered_email_example_data;
    }
}