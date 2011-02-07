<?php
/**
 * abstract for an order
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license none
 */
class Order extends DataObject {

    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $singular_name = "Bestellung";

    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $plural_name = "Bestellungen";

    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'AmountTotal'                   => 'Money', // Wert aller Artikel
        'AmountGrossTotal'              => 'Money', // Wert aller Artikel + Transaktionskosten
        'HandlingCostPayment'           => 'Money',
        'HandlingCostShipment'          => 'Money',
        'TaxRatePayment'                => 'Int',
        'TaxRateShipment'               => 'Int',
        'TaxAmountPayment'              => 'Float',
        'TaxAmountShipment'             => 'Float',
        'Note'                          => 'Text',
        'isConfirmed'                   => 'Boolean',
        'WeightTotal'                   => 'Int', //unit is gramm
        'CarrierAndShippingMethodTitle' => 'VarChar(100)',
        'PaymentMethodTitle'            => 'VarChar(100)',
        'CustomersEmail'                => 'VarChar(60)'
    );

    /**
     * Summaryfields for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $summary_fields = array(
        'CreatedNice'               => 'Datum',
        'ID'                        => 'Bestellnummer',
        'ShippingAddressSummary'    => 'Lieferadresse',
        'InvoiceAddressSummary'     => 'Rechnungsadresse',
        'AmountGrossTotalNice'      => 'Bestellwert',
    );

    /**
     * Casting.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $casting = array(
        'Created'                   => 'Date',
        'CreatedNice'               => 'VarChar',
        'ShippingAddressSummary'    => 'VarChar',
        'InvoiceAddressSummary'     => 'VarChar',
        'AmountGrossTotalNice'      => 'VarChar',
    );

    /**
     * Field labels for display in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $field_labels = array(
        'ID'                => 'Bestellnummer',
        'Created'           => 'Datum',
        'ShippingRate'      => 'Versandkosten',
        'Note'              => 'Kundenbemerkungen',
        'isConfirmed'       => 'bestätigt?',
        'customer'          => 'Kunde',
        'shippingAddress'   => 'Versandadresse',
        'invoiceAddress'    => 'Rechnungsadresse',
        'status'            => 'Bestellstatus'
    );

    /**
     * Default sort direction in tables.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $default_sort = "Created DESC";

    /**
     * Searchable fields
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $searchable_fields = array(
        'Created',
        'customer.FirstName',
        'customer.Surname',

    );

    /**
     * 1:1 relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_one = array(
        'shippingAddress'   => 'OrderShippingAddress',
        'invoiceAddress'    => 'OrderInvoiceAddress',
        'payment'           => 'PaymentMethod',
        'shippingMethod'    => 'ShippingMethod',
        'status'            => 'OrderStatus',
        'customer'          => 'Member',
        'shippingFee'       => 'ShippingFee'
    );

    /**
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_many = array(
        'orderPositions' => 'OrderPosition'
    );

    /**
     * m:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $many_many = array(
        'articles' => 'Article'
    );

    /**
     * register extensions
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    static $extensions = array(
        "Versioned('Live')",
    );

    /**
     * returns the orders creation date formated: dd.mm.yyyy hh:mm
     *
     * @return string
     */
    public function getCreatedNice() {
        return date('d.m.Y H:i', strtotime($this->Created)) . ' Uhr';
    }

    /**
     * return the orders shipping address as complete string.
     *
     * @return string
     */
    public function getShippingAddressSummary() {
        $shippingAddressSummary = '';
        $shippingAddressSummary .= $this->shippingAddress()->FirstName . ' ' . $this->shippingAddress()->Surname . "\n";
        $shippingAddressSummary .= $this->shippingAddress()->Street . ' ' . $this->shippingAddress()->StreetNumber . "\n";
        $shippingAddressSummary .= $this->shippingAddress()->Addition == '' ? '' : $this->shippingAddress()->Addition . "\n";
        $shippingAddressSummary .= strtoupper($this->shippingAddress()->country()->ISO2) . '-' . $this->shippingAddress()->Postcode . ' ' . $this->shippingAddress()->City . "\n";
        return $shippingAddressSummary;
    }

    /**
     * return the orders invoice address as complete string.
     *
     * @return string
     */
    public function getInvoiceAddressSummary() {
        $invoiceAddressSummary = '';
        $invoiceAddressSummary .= $this->invoiceAddress()->FirstName . ' ' . $this->invoiceAddress()->Surname . "\n";
        $invoiceAddressSummary .= $this->invoiceAddress()->Street . ' ' . $this->invoiceAddress()->StreetNumber . "\n";
        $invoiceAddressSummary .= $this->invoiceAddress()->Addition == '' ? '' : $this->invoiceAddress()->Addition . "\n";
        $invoiceAddressSummary .= strtoupper($this->invoiceAddress()->country()->ISO2) . '-' . $this->invoiceAddress()->Postcode . ' ' . $this->invoiceAddress()->City . "\n";
        return $invoiceAddressSummary;
    }

    /**
     * returns the orders total amount as string incl. currency.
     *
     * @return string
     */
    public function getAmountGrossTotalNice() {
        return str_replace('.', ',', number_format($this->AmountTotalAmount, 2)) . ' ' . $this->AmountTotalCurrency;
    }

    /**
     * Creates default records, if not exitstent:
     * order email templates
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.01.2011
     */
    public function  requireDefaultRecords() {
        parent::requireDefaultRecords();
        $checkOrderMail = DataObject::get_one(
            'ShopEmail',
            "`Identifier` = 'MailOrderConfirmation'"
        );
        if (!$checkOrderMail) {
            $orderMail = new ShopEmail();
            $orderMail->setField('Identifier',   'MailOrderConfirmation');
            $orderMail->setField('Subject',      'Ihre Bestellung in unserem Webshop');
            $orderMail->setField('Variables',    "\$FirstName\n\$Surname\n\$Salutation\n\$Order");
            $defaultTemplateFile = Director::baseFolder() . '/silvercart/templates/email/MailOrderConfirmation.ss';
            if (is_file($defaultTemplateFile)) {
                $defaultTemplate = file_get_contents($defaultTemplateFile);
            } else {
                $defaultTemplate = '';
            }
            $orderMail->setField('EmailText',    $defaultTemplate);
            $orderMail->write();
        }
        $checkOrderMail = DataObject::get_one(
            'ShopEmail',
            "`Identifier` = 'MailOrderNotification'"
        );
        if (!$checkOrderMail) {
            $orderMail = new ShopEmail();
            $orderMail->setField('Identifier',   'MailOrderNotification');
            $orderMail->setField('Subject',      'Eine neue Bestellung wurde aufgegeben');
            $orderMail->setField('Variables',    "\$FirstName\n\$Surname\n\$Salutation\n\$Order");
            $defaultTemplateFile = Director::baseFolder() . '/silvercart/templates/email/MailOrderNotification.ss';
            if (is_file($defaultTemplateFile)) {
                $defaultTemplate = file_get_contents($defaultTemplateFile);
            } else {
                $defaultTemplate = '';
            }
            $orderMail->setField('EmailText',    $defaultTemplate);
            $orderMail->write();
        }
    }

        /**
     * customize backend fields
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 1.11.2010
     * @return FieldSet the form fields for the backend
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('Versandadresse');
        $fields->removeByName('Rechnungsadresse');

        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_Name', 'Name', $this->shippingAddress()->FirstName . ' ' . $this->shippingAddress()->Surname));
        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_Street', 'Straße', $this->shippingAddress()->Street . ' ' . $this->shippingAddress()->StreetNumber));
        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_Addition', 'Zusatz', $this->shippingAddress()->Addition));
        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_City', 'PLZ/Ort', strtoupper($this->shippingAddress()->country()->ISO2) . '-' . $this->shippingAddress()->Postcode . ' ' . $this->shippingAddress()->City));
        $fields->addFieldToTab('Root.Versandadresse', new ReadonlyField('sa_Phone', 'Telefon', $this->shippingAddress()->PhoneAreaCode . '/' . $this->shippingAddress()->Phone));

        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_Name', 'Name', $this->invoiceAddress()->FirstName . ' ' . $this->invoiceAddress()->Surname));
        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_Street', 'Straße', $this->invoiceAddress()->Street . ' ' . $this->invoiceAddress()->StreetNumber));
        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_Addition', 'Zusatz', $this->invoiceAddress()->Addition));
        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_City', 'PLZ/Ort', strtoupper($this->invoiceAddress()->country()->ISO2) . '-' . $this->invoiceAddress()->Postcode . ' ' . $this->invoiceAddress()->City));
        $fields->addFieldToTab('Root.Rechnungsadresse', new ReadonlyField('ia_Phone', 'Telefon', $this->invoiceAddress()->PhoneAreaCode . '/' . $this->invoiceAddress()->Phone));

        return $fields;
    }

    /**
     * create a invoice address for an order from customers data
     *
     * @param array $registrationData checkout forms submit data; only needed for anonymous customers
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.11.2010
     * @return void
     */
    public function createInvoiceAddress($registrationData = "") {
        $member = Member::currentUser();
        $orderInvoiceAddress = new OrderInvoiceAddress();
        if ($member->ClassName != "AnonymousCustomer") {//for registered users

            /*
             * a member might not have an invoice address Surname/FirstName
             */
            if ($member->invoiceAddress()->Surname) {
                $orderInvoiceAddress->Surname = $member->invoiceAddress()->Surname;
            } else {
                $orderInvoiceAddress->Surname = $member->Surname;
            }
            if ($member->invoiceAddress()->FirstName) {
                $orderInvoiceAddress->FirstName = $member->invoiceAddress()->FirstName;
            } else {
                $orderInvoiceAddress->FirstName = $member->FirstName;
            }
            $orderInvoiceAddress->Street = $member->invoiceAddress()->Street;
            $orderInvoiceAddress->StreetNumber = $member->invoiceAddress()->StreetNumber;
            $orderInvoiceAddress->Postcode = $member->invoiceAddress()->Postcode;
            $orderInvoiceAddress->City = $member->invoiceAddress()->City;
            $orderInvoiceAddress->PhoneAreaCode = $member->invoiceAddress()->PhoneAreaCode;
            $orderInvoiceAddress->Phone = $member->invoiceAddress()->Phone;
            $orderInvoiceAddress->countryID = $member->invoiceAddress()->countryID;
            $orderInvoiceAddress->write();
            $this->invoiceAddressID = $orderInvoiceAddress->ID;
        } else { //for anonymous customers
            $orderInvoiceAddress->castedUpdate($registrationData);
            $orderInvoiceAddress->countryID = $registrationData['Country'];
            $orderInvoiceAddress->write();
            $this->invoiceAddressID = $orderInvoiceAddress->ID;
        }
        $this->write();
    }

    /**
     * create a shipping address for an order from customers data
     * writes $this to the database
     *
     * @param array $registrationData checkout forms submit data; only needed for anonymous customers
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.11.2010
     * @return void
     */
    public function createShippingAddress($registrationData = "") {
        $member = Member::currentUser();
        $orderShippingAddress = new OrderShippingAddress();
        if ($member->ClassName != "AnonymousCustomer") {// for registered customers

            /*
             * get Surname and FirstName from the address, if not available from the member
             */
            if ($member->shippingAddress()->Surname) {
                $orderShippingAddress->Surname = $member->shippingAddress()->Surname;
            } else {
                $orderShippingAddress->Surname = $member->Surname;
            }
            if ($member->shippingAddress()->FirstName) {
                $orderShippingAddress->FirstName = $member->shippingAddress()->FirstName;
            } else {
                $orderShippingAddress->FirstName = $member->FirstName;
            }
            $orderShippingAddress->Street = $member->shippingAddress()->Street;
            $orderShippingAddress->StreetNumber = $member->shippingAddress()->StreetNumber;
            $orderShippingAddress->Postcode = $member->shippingAddress()->Postcode;
            $orderShippingAddress->City = $member->shippingAddress()->City;
            $orderShippingAddress->PhoneAreaCode = $member->shippingAddress()->PhoneAreaCode;
            $orderShippingAddress->Phone = $member->shippingAddress()->Phone;
            $orderShippingAddress->countryID = $member->shippingAddress()->countryID;
            $orderShippingAddress->write(); //write here to have an object ID
            $this->shippingAddressID = $orderShippingAddress->ID;
        } else { //for anonymous customers
            $orderShippingAddress->castedUpdate($registrationData);
            $orderShippingAddress->countryID = $registrationData['Country'];
            $orderShippingAddress->write(); //write here to have an object ID
            $this->shippingAddressID = $orderShippingAddress->ID;
        }
        $this->write();
    }

    /**
     * creates an order from the cart
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function createFromShoppingCart() {
        $member = Member::currentUser();
        $this->customerID = $member->ID;

        // VAT tax for shipping and payment fees
        $shippingMethod = DataObject::get_by_id('ShippingMethod', $this->shippingMethodID);
        if ($shippingMethod) {
            $shippingFee  = $shippingMethod->getShippingFee();

            if ($shippingFee) {
                if ($shippingFee->Tax()) {
                    $this->TaxRateShipment   = $shippingFee->Tax()->Rate;
                    $this->TaxAmountShipment = $shippingFee->getTaxAmount();
                }
            }
        }

        $paymentMethod = DataObject::get_by_id('PaymentMethod', $this->paymentID);
        if ($paymentMethod) {
            $paymentFee = $paymentMethod->HandlingCost();

            if ($paymentFee) {
                if ($paymentFee->Tax()) {
                    $this->TaxRatePayment   = $paymentFee->Tax()->Rate;
                    $this->TaxAmountPayment = $paymentFee->getTaxAmount();
                }
            }
        }

        // price sum of all positions
        $this->AmountTotal->setAmount($member->shoppingCart()->getAmountTotal()->getAmount());
        $this->AmountTotal->setCurrency('EUR');

        // amount of all positions + handling fee of the payment method + shipping fee
        $totalAmount = 
            $this->HandlingCostPayment->getAmount() +
            $this->HandlingCostShipment->getAmount() +
            $member->shoppingCart()->getAmountTotal()->getAmount();

        $this->AmountTotal->setAmount(
            $totalAmount
        );
        $this->AmountGrossTotal->setCurrency('EUR');

        // adjust orders standard status
        $paymentObj = DataObject::get_by_id(
            'PaymentMethod',
            $this->paymentID
        );
        $orderStatus = DataObject::get_one(
            'OrderStatus',
            sprintf(
                "\"Code\" = '%s'",
                $paymentObj->getDefaultOrderStatus()
            )
        );
        if ($orderStatus) {
            $this->statusID = $orderStatus->ID;
        }
        // write order to have an id
        $this->write();

        // Convert shopping cart positions
        $this->convertShoppingCartPositionsToOrderPositions();
    }

    /**
     * convert cart positions in order positions
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    protected function convertShoppingCartPositionsToOrderPositions() {
        $member = Member::currentUser();
        $filter = sprintf("\"shoppingCartID\" = '%s'", $member->shoppingCartID);
        $shoppingCartPositions = DataObject::get('ShoppingCartPosition', $filter);

        if ($shoppingCartPositions) {
            foreach ($shoppingCartPositions as $shoppingCartPosition) {
                $article = $shoppingCartPosition->article();
                if ($article) {
                    $orderPosition = new OrderPosition();
                    $orderPosition->Price->setAmount($article->Price->getAmount());
                    $orderPosition->Price->setCurrency($article->Price->getCurrency());
                    $orderPosition->PriceTotal->setAmount($article->Price->getAmount() * $shoppingCartPosition->Quantity);
                    $orderPosition->PriceTotal->setCurrency($article->Price->getCurrency());
                    $orderPosition->Tax                 = $article->getTaxAmount();
                    $orderPosition->TaxTotal            = $article->getTaxAmount() * $shoppingCartPosition->Quantity;
                    $orderPosition->TaxRate             = $article->tax()->Rate;
                    $orderPosition->ArticleDescription  = $article->LongDescription;
                    $orderPosition->Quantity            = $shoppingCartPosition->Quantity;
                    $orderPosition->Title               = $article->Title;
                    $orderPosition->orderID             = $this->ID;
                    $orderPosition->articleID           = $article->ID;
                    $orderPosition->write();
                    unset($orderPosition);
                }
            }

            // Get taxable positions from registered modules
            $registeredModules = $member->shoppingCart()->callMethodOnRegisteredModules(
                'ShoppingCartPositions',
                array(
                    $member->shoppingCart(),
                    $member,
                    true
                )
            );

            foreach ($registeredModules as $moduleName => $moduleOutput) {
                foreach ($moduleOutput as $modulePosition) {
                    $orderPosition = new OrderPosition();
                    $orderPosition->Price->setAmount($modulePosition->Price);
                    $orderPosition->Price->setCurrency($modulePosition->Currency);
                    $orderPosition->PriceTotal->setAmount($modulePosition->PriceTotal);
                    $orderPosition->PriceTotal->setCurrency($modulePosition->Currency);
                    $orderPosition->Tax                 = 0;
                    $orderPosition->TaxTotal            = $modulePosition->TaxAmount;
                    $orderPosition->TaxRate             = $modulePosition->TaxRate;
                    $orderPosition->ArticleDescription  = $modulePosition->LongDescription;
                    $orderPosition->Quantity            = $modulePosition->Quantity;
                    $orderPosition->Title               = $modulePosition->Name;
                    $orderPosition->orderID             = $this->ID;
                    $orderPosition->write();
                    unset($orderPosition);
                }
            }

            // Get nontaxable positions from registered modules
            $registeredModules = $member->shoppingCart()->callMethodOnRegisteredModules(
                'ShoppingCartPositions',
                array(
                    $member->shoppingCart(),
                    $member,
                    false
                )
            );

            foreach ($registeredModules as $moduleName => $moduleOutput) {
                foreach ($moduleOutput as $modulePosition) {
                    $orderPosition = new OrderPosition();
                    $orderPosition->Price->setAmount($modulePosition->Price);
                    $orderPosition->Price->setCurrency($modulePosition->Currency);
                    $orderPosition->PriceTotal->setAmount($modulePosition->PriceTotal);
                    $orderPosition->PriceTotal->setCurrency($modulePosition->Currency);
                    $orderPosition->Tax                 = 0;
                    $orderPosition->TaxTotal            = $modulePosition->TaxAmount;
                    $orderPosition->TaxRate             = $modulePosition->TaxRate;
                    $orderPosition->ArticleDescription  = $modulePosition->LongDescription;
                    $orderPosition->Quantity            = $modulePosition->Quantity;
                    $orderPosition->Title               = $modulePosition->Name;
                    $orderPosition->orderID             = $this->ID;
                    $orderPosition->write();
                    unset($orderPosition);
                }
            }

            // Convert positions of registered modules
            $member->currentUser()->shoppingCart()->callMethodOnRegisteredModules(
                'ShoppingCartConvert',
                array(
                    Member::currentUser()->shoppingCart(),
                    Member::currentUser()
                )
            );

            // Delete the shoppingcart positions
            foreach ($shoppingCartPositions as $shoppingCartPosition) {
                $shoppingCartPosition->delete();
            }
        }
    }

    /**
     * save order to db
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function save() {
        $this->write();
    }

    /**
     * set payment method for $this
     *
     * @param int $paymentMethodID id of payment method
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 05.01.2011
     */
    public function setPaymentMethod($paymentMethodID) {
        $paymentMethodObj = DataObject::get_by_id(
                        'PaymentMethod',
                        $paymentMethodID
        );

        if ($paymentMethodObj) {
            $this->paymentID = $paymentMethodObj->ID;
            $this->PaymentMethodTitle = $paymentMethodObj->Name;
            $this->HandlingCostPayment->setAmount($paymentMethodObj->getHandlingCost()->getAmount());
            $this->HandlingCostPayment->setCurrency('EUR');
        }
    }

    /**
     * set status of $this
     *
     * @param OrderStatus $orderStatus the order status object
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function setOrderStatus($orderStatus) {
        $orderStatusSet = false;

        if ($orderStatus && $orderStatus->exists()) {
            $this->statusID = $orderStatus->ID;
            $this->write();
            $orderStatusSet = true;
        }

        return $orderStatusSet;
    }

    /**
     * Save the note from the form if there is one
     *
     * @param string $note the customers notice
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     * @return void
     */
    public function setNote($note) {
        $this->setField('Note', $note);
    }

    /**
     * getter
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     *
     */
    public function getFormattedNote() {
        $note = $this->Note;
        $note = str_replace(
            '\r\n',
            '<br />',
            $note
        );

        return $note;
    }

    /**
     * save the cart´s weight
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     * @return void
     */
    public function setWeight() {
        $member = Member::currentUser();
        if ($member->shoppingCart()->getWeightTotal()) {
            $this->WeightTotal = $member->shoppingCart()->getWeightTotal();
        }
    }

    /**
     * set the total price for this order
     *
     * @return void
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public function setAmountTotal() {
        $member = Member::currentUser();

        if ($member && $member->shoppingCart()) {
            $this->AmountTotal = $member->shoppingCart()->getAmountTotal();
        }
    }

    /**
     * set the email for this order
     *
     * @param string $email the email address of the customer
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     * @return void
     */
    public function setCustomerEmail($email = null) {
        $member = Member::currentUser();
        if ($member->Email) { //for registered customers
            $email = $member->Email;
        } else { // for anonymous customers
            $email = $email;
        }
        $this->CustomersEmail = $email;
    }

    /**
     * The shipping method is a relation + an attribte of the order
     *
     * @param int $shippingMethodID the ID of the shipping method
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.12.10
     * @copyright 2010 pixeltricks GmbH
     * @return void
     */
    public function setShippingMethod($shippingMethodID) {
        $selectedShippingMethod = DataObject::get_by_id(
            'ShippingMethod',
            $shippingMethodID
        );

        if ($selectedShippingMethod) {
            $this->shippingMethodID              = $selectedShippingMethod->ID;
            $this->CarrierAndShippingMethodTitle = $selectedShippingMethod->carrier()->Title . "-" . $selectedShippingMethod->Title;
            $this->shippingFeeID                 = $selectedShippingMethod->getShippingFee()->ID;
            $this->HandlingCostShipment->setCurrency('EUR');
            $this->HandlingCostShipment->setAmount($selectedShippingMethod->getShippingFee()->Price->getAmount());
        }
    }

    /**
     * returns tax included in $this
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getTax() {
        $tax = 0.0;

        foreach ($this->orderPositions() as $orderPosition) {
            $tax += $orderPosition->TaxTotal;
        }

        $taxObj = new Money('Tax');
        $taxObj->setAmount($tax);

        return $taxObj;
    }

    /**
     * returns carts net value including all editional costs
     *
     * @return Money amount
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getAmountNet() {
        $amountNet = $this->AmountGrossTotal->getAmount() - $this->Tax->getAmount();
        $amountNetObj = new Money();
        $amountNetObj->setAmount($amountNet);

        return $amountNetObj;
    }

    /**
     * returns carts gross value including all editional costs
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 05.01.2011
     */
    public function getAmountGross() {
        return $this->AmountGrossTotal;
    }

    /**
     * returns bills currency
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 06.01.2011
     */
    public function getCurrency() {
        return $this->AmountGrossTotal->getCurrency();
    }

    /**
     * returns the cart's net amount
     *
     * @return Money money object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getPriceNet() {
        $priceNet = $this->AmountTotal->getAmount() - $this->Tax->getAmount();
        $priceNetObj = new Money();
        $priceNetObj->setAmount($priceNet);

        return $priceNetObj;
    }

    /**
     * returns the cart's gross amount
     *
     * @return Money money object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getPriceGross() {
        return $this->AmountTotal;
    }

    /**
     * Returns the order value of all positions with a tax rate > 0 without any
     * fees.
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function getTaxableAmountGross() {
        $priceGross = new Money();
        $priceGross->setAmount(0);

        foreach ($this->orderPositions() as $position) {
            if ($position->TaxRate > 0) {
                $priceGross->setAmount(
                    $priceGross->getAmount() + $position->PriceTotal->getAmount()
                );
            }
        }

        return $priceGross;
    }

    /**
     * Returns the order value of all positions with a tax rate > 0 including
     * all fees.
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function getTaxableAmountGrossWithFees() {
        $priceGross = new Money();
        $priceGross->setAmount(0);

        foreach ($this->orderPositions() as $position) {
            if ($position->TaxRate > 0) {
                $priceGross->setAmount(
                    $priceGross->getAmount() + $position->PriceTotal->getAmount()
                );
            }
        }

        $priceGross->setAmount(
            $priceGross->getAmount() +
            $this->HandlingCostPayment->getAmount()
        );

        $priceGross->setAmount(
            $priceGross->getAmount() +
            $this->HandlingCostShipment->getAmount()
        );

        return $priceGross;
    }

    /**
     * Returns the sum of tax amounts grouped by tax rates for the articles
     * of the order.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 03.02.2011
     */
    public function getTaxRatesWithoutFees() {
        $taxes = new DataObjectSet;

        foreach ($this->orderPositions() as $orderPosition) {
            $taxRate = $orderPosition->TaxRate;
            if ($taxRate > 0 &&
                !$taxes->find('Rate', $taxRate)) {
                
                $taxes->push(
                    new DataObject(
                        array(
                            'Rate'      => $taxRate,
                            'AmountRaw' => 0.0,
                        )
                    )
                );
            }
            $taxSection = $taxes->find('Rate', $taxRate);
            $taxSection->AmountRaw += $orderPosition->TaxTotal;
        }

        foreach ($taxes as $tax) {
            $taxObj = new Money;
            $taxObj->setAmount($tax->AmountRaw);

            $tax->Amount = $taxObj;
        }

        return $taxes;
    }

    /**
     * Returns the sum of tax amounts grouped by tax rates for the articles
     * and all fees of the order.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 03.02.2011
     */
    public function getTaxRatesWithFees() {
        $taxes = $this->getTaxRatesWithoutFees();

        // Shipping cost tax
        $taxRate = $this->TaxRateShipment;
        if ($taxRate > 0 &&
            !$taxes->find('Rate', $taxRate)) {

            $taxes->push(
                new DataObject(
                    array(
                        'Rate'      => $taxRate,
                        'AmountRaw' => 0.0,
                    )
                )
            );
        }
        $taxSection = $taxes->find('Rate', $taxRate);
        $taxSection->AmountRaw += $this->TaxAmountShipment;

        // Payment cost tax
        $taxRate = $this->TaxRatePayment;
        if ($taxRate > 0 &&
            !$taxes->find('Rate', $taxRate)) {

            $taxes->push(
                new DataObject(
                    array(
                        'Rate'      => $taxRate,
                        'AmountRaw' => 0.0,
                    )
                )
            );
        }
        $taxSection = $taxes->find('Rate', $taxRate);
        $taxSection->AmountRaw += $this->TaxAmountPayment;

        foreach ($taxes as $tax) {
            $taxObj = new Money;
            $taxObj->setAmount($tax->AmountRaw);

            $tax->Amount = $taxObj;
        }

        return $taxes;
    }

    /**
     * returns quantity of all articles of the order
     *
     * @param int $articleId if set only article quantity of this article is returned
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 29.11.10
     */
    public function getQuantity($articleId = null) {
        $positions = $this->orderPositions();
        $quantity = 0;

        foreach ($positions as $position) {
            if ($articleId === null ||
                    $position->article()->ID === $articleId) {

                $quantity += $position->Quantity;
            }
        }

        return $quantity;
    }

    /**
     * returns handling fee for choosen payment method
     *
     * @return float
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public function getHandlingCostPayment() {
        $handlingCosts = 0.0;
        $paymentObj = DataObject::get_by_id(
            'PaymentMethod',
            $this->paymentID
        );

        // get handling fee
        if ($paymentObj) {
            $handlingCosts += $paymentObj->getHandlingCost()->getAmount();
        }
        $handlingCostsObj = new Money('paymentHandlingCosts');
        $handlingCostsObj->setAmount($handlingCosts);

        return $handlingCostsObj;
    }

    /**
     * writes a log entry
     * 
     * @param string $context context for log entry
     * @param string $text    text for log entry
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    public function Log($context, $text) {
        if ($this->mode == 'Live') {
            $path = PIX_LOGFILE;
        } else {
            $path = PIX_LOGFILE;
        }

        if ($fp = fopen($path, 'a+')) {
            $text = sprintf(
                            "%s | Module: \"%s\" | Method: \"%s\"\n%s\n--------------------------------------------------------------------------------\n",
                            date('d.m.Y H:i:s'),
                            $this->ClassName,
                            $context,
                            $text
            );

            fwrite($fp, $text);
            fclose($fp);
        }
    }

    /**
     * send a confirmation mail with order details to the customer $member
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 16.11.2010
     * @return void
     */
    public function sendConfirmationMail() {
        $member = Member::currentUser();
        if ($member) {
            ShopEmail::send(
                'MailOrderConfirmation',
                $this->CustomersEmail,
                array(
                    'FirstName' => $member->FirstName,
                    'Surname' => $member->Surname,
                    'Salutation' => $member->Salutation,
                    'Order' => $this
                )
            );
            ShopEmail::send(
                'MailOrderNotification',
                Email::getAdminEmail(),
                array(
                    'FirstName' => $member->FirstName,
                    'Surname' => $member->Surname,
                    'Salutation' => $member->Salutation,
                    'Order' => $this
                )
            );
        }
    }

    /**
     * hook triggered after write
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 9.11.10
     * @return void
     */
    protected function onAfterWrite() {
        parent::onAfterWrite();
        if ($this->AmountGrossTotal->hasAmount() === false) {
            $price = $this->AmountTotal->getAmount() + $this->HandlingCostShipment->getAmount();
            $this->AmountGrossTotal->setAmount($price);
            $this->AmountGrossTotal->setCurrency('EUR');
            $this->write();
        }
    }
}
