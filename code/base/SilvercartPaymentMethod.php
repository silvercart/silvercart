<?php

/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Base
 */

/**
 * base class for payment
 *
 * Every payment module must extend this class
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 07.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentMethod extends DataObject {
    // ------------------------------------------------------------------------
    // Class attributes
    // ------------------------------------------------------------------------

    /**
     * The link to direct after cancelling by user or session expiry.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $cancelLink = '';
    /**
     * The link to redirect back into shop after payment.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $returnLink = '';
    /**
     * Indicates whether an error occured or not.
     *
     * @var bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $errorOccured;
    /**
     * A list of errors.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected $errorList = array();
    /**
     * Indicates whether a payment module has multiple payment channels or not.
     *
     * @var bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public static $has_multiple_payment_channels = false;
    /**
     * A list of possible payment channels.
     *
     * @var array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public static $possible_payment_channels = array();

    // ------------------------------------------------------------------------
    // Attributes and Relations
    // ------------------------------------------------------------------------
    /**
     * Singular name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $singular_name = "payment method";
    /**
     * Plural name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $plural_name = "payment methods";
    /**
     * Defines the attributes of the class
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public static $db = array(
        'isActive'                              => 'Boolean',
        'minAmountForActivation'                => 'Float',
        'maxAmountForActivation'                => 'Float',
        'Name'                                  => 'Varchar(150)',
        'paymentDescription'                    => 'Text',
        'mode'                                  => "Enum('Live,Dev','Dev')",
        'orderStatus'                           => 'Varchar(50)',
        'showPaymentLogos'                      => 'Boolean',
        'orderRestrictionMinQuantity'           => 'Int',
        'enableActivationByOrderRestrictions'   => 'Boolean',
        'ShowFormFieldsOnPaymentSelection' => 'Boolean',
    );
    /**
     * Defines 1:1 relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public static $has_one = array(
        'SilvercartHandlingCost'    => 'SilvercartHandlingCost',
        'SilvercartZone'            => 'SilvercartZone'
    );
    /**
     * Defines 1:n relations
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public static $has_many = array(
        'SilvercartOrders'          => 'SilvercartOrder',
        'PaymentLogos'              => 'SilvercartImage'
    );
    /**
     * Defines n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public static $many_many = array(
        'SilvercartShippingMethods' => 'SilvercartShippingMethod',
        'ShowOnlyForGroups'         => 'Group',
        'ShowNotForGroups'          => 'Group',
        'ShowOnlyForUsers'          => 'Member',
        'ShowNotForUsers'           => 'Member',
        'OrderRestrictionStatus'    => 'SilvercartOrderStatus'
    );
    /**
     * Defines m:n relations
     *
     * @var array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public static $belongs_many_many = array(
        'SilvercartCountries' => 'SilvercartCountry'
    );
    /**
     * Virtual database columns.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $casting = array(
        'AttributedCountries' => 'Varchar(255)',
        'AttributedZones' => 'Varchar(255)',
        'activatedStatus' => 'Varchar(255)'
    );
    /**
     * Default values for new PaymentMethods
     *
     * @var array
     */
    public static $defaults = array(
        'showPaymentLogos' => true,
        'ShowFormFieldsOnPaymentSelection' => false,
    );
    /**
     * List of searchable fields for the model admin
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $searchable_fields = array(
        'Name',
        'isActive' => array(
            'title' => 'Aktiviert'
        ),
        'minAmountForActivation',
        'maxAmountForActivation',
        'SilvercartZone.ID' => array(
            'title' => 'Zugeordnete Zone'
        ),
        'SilvercartCountries.ID' => array(
            'title' => 'Zugeordnete Länder'
        )
    );
    /**
     * Contains the module name for display in the admin backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 03.12.2010
     */
    protected $moduleName = '';
    /**
     * Contains a referer to the order object
     *
     * @var Controller
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    protected $controller;
    /**
     * Details of customer
     *
     * @var Member
     */
    protected $customerDetails = null;
    /**
     * Invoice address
     *
     * @var SilvercartAddress
     */
    protected $invoiceAddress = null;
    /**
     * Shipping address
     *
     * @var SilvercartAddress
     */
    protected $shippingAddress = null;
    /**
     * Shopping cart
     *
     * @var SilvercartShoppingCart
     */
    protected $shoppingCart = null;
    /**
     * Order
     *
     * @var SilvercartOrder
     */
    protected $order = null;
    /**
     * ID of the check out form to render additional form fields
     *
     * @var string
     */
    protected $formID = '';
    
    // ------------------------------------------------------------------------
    // Methods
    // ------------------------------------------------------------------------
    
    /**
     * Searchable fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 5.7.2011
     */
    public function searchableFields() {
        $searchableFields = array(
            'Name' => array(
                'title' => _t('SilvercartProduct.COLUMN_TITLE'),
                'filter' => 'PartialMatchFilter'
            ),
            'isActive' => array(
                'title' => _t("SilvercartShopAdmin.PAYMENT_ISACTIVE"),
                'filter' => 'ExactMatchFilter'
            ),
            'minAmountForActivation' => array(
                'title' => _t('SilvercartShopAdmin.PAYMENT_MINAMOUNTFORACTIVATION'),
                'filter' => 'GreaterThanFilter'
            ),
            'maxAmountForActivation' => array(
                'title' => _t('SilvercartShopAdmin.PAYMENT_MAXAMOUNTFORACTIVATION'),
                'filter' => 'LessThanFilter'
            ),
            'SilvercartZone.ID' => array(
                'title' => _t("SilvercartCountry.ATTRIBUTED_ZONES"),
                'filter' => 'ExactMatchFilter'
            ),
            'SilvercartCountries.ID' => array(
                'title' => _t("SilvercartPaymentMethod.ATTRIBUTED_COUNTRIES"),
                'filter' => 'ExactMatchFilter'
            )
        );
        $this->extend('updateSearchableFields', $searchableFields);
        return $searchableFields;
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.04.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'Name' => 'Name',
                    'activatedStatus' => _t('SilvercartShopAdmin.PAYMENT_ISACTIVE'),
                    'AttributedZones' => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
                    'AttributedCountries' => _t('SilvercartPaymentMethod.ATTRIBUTED_COUNTRIES'),
                    'minAmountForActivation' => _t('SilvercartPaymentMethod.FROM_PURCHASE_VALUE', 'from purchase value'),
                    'maxAmountForActivation' => _t('SilvercartPaymentMethod.TILL_PURCHASE_VALUE', 'till purchase value'),
                    'ShowFormFieldsOnPaymentSelection'  => _t('SilvercartPaymentMethod.SHOW_FORM_FIELDS_ON_PAYMENT_SELECTION'),
                )
        );
    }

    /**
     * i18n for summary fields
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.02.2011
     * @copyright 2010 pixeltricks GmbH
     * @return array
     */
    public function summaryFields() {
        return array(
            'Name' => _t('SilvercartPaymentMethod.NAME'),
            'activatedStatus' => _t('SilvercartShopAdmin.PAYMENT_ISACTIVE'),
            'AttributedZones' => _t('SilvercartCountry.ATTRIBUTED_ZONES'),
            'AttributedCountries' => _t('SilvercartPaymentMethod.ATTRIBUTED_COUNTRIES'),
            'minAmountForActivation' => _t('SilvercartPaymentMethod.FROM_PURCHASE_VALUE'),
            'maxAmountForActivation' => _t('SilvercartPaymentMethod.TILL_PURCHASE_VALUE'),
        );
    }

    /**
     * Returns the title of the payment method
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getTitle() {
        return $this->Name;
    }

    /**
     * Returns the status that for orders created with this payment method
     *
     * @return string orderstatus code
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public function getDefaultOrderStatus() {
        return $this->orderStatus;
    }

    /**
     * Returns the payment methods description
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getPaymentDescription() {
        return $this->getField('paymentDescription');
    }

    /**
     * Returns the path to the payment methods logo
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getLogo() {
        
    }

    /**
     * Returns the link for cancel action or end of session
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getCancelLink() {
        return $this->cancelLink;
    }

    /**
     * Returns the link to get back in the shop
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getReturnLink() {
        return $this->returnLink;
    }

    /**
     * Returns handling costs for this payment method
     *
     * @return Money a money object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getHandlingCost() {
        $handlingCosts = new Money;
        $handlingCosts->setAmount(0);
        $handlingCosts->setCurrency(SilvercartConfig::DefaultCurrency());

        return $handlingCosts;
    }

    /**
     * Retunrns a path to a picture with additional information for this payment method
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function getDescriptionImage() {
        
    }

    /**
     * Returns if an error has occured
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorOccured() {
        return $this->errorOccured;
    }

    /**
     * Returns a DataObjectSet with errors
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function getErrorList() {
        $errorList = array();
        $errorIdx = 0;

        foreach ($this->errorList as $error) {
            $errorList['error' . $errorIdx] = array(
                'error' => $error
            );
            $errorIdx++;
        }

        return new DataObjectSet($errorList);
    }
    
    /**
     * Returns allowed shipping methods.
     * 
     * @param string                 $shippingCountry The SilvercartCountry to check the
     *                                                payment methods for.
     * @param SilvercartShoppingCart $shoppingCart    The shopping cart object
     * 
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.07.2011
     */
    public static function getAllowedPaymentMethodsFor($shippingCountry, $shoppingCart) {
        $allowedPaymentMethods  = array();
        
        if (!$shippingCountry) {
            return $allowedPaymentMethods;
        }
        
        $paymentMethods = $shippingCountry->SilvercartPaymentMethods('isActive = 1');
        $member         = Member::currentUser();
        
        if ($paymentMethods) {
            foreach ($paymentMethods as $paymentMethod) {
                $assumePaymentMethod    = true;
                $containedInGroup       = false;
                $containedInUsers       = false;
                $doAccessChecks         = true;
                
                // ------------------------------------------------------------
                // Basic checks
                // ------------------------------------------------------------
                if ($paymentMethod->enableActivationByOrderRestrictions) {
                    $assumePaymentMethod = $paymentMethod->isActivationByOrderRestrictionsPossible($member);
                    $doAccessChecks      = false;
                }
                
                if (!$paymentMethod->isAvailableForAmount($shoppingCart->getAmountTotalWithoutFees()->getAmount())) {
                    $assumePaymentMethod = false;
                    $doAccessChecks      = false;
                }
                
                // ------------------------------------------------------------
                // Access checks
                // ------------------------------------------------------------
                
                if ($doAccessChecks) {
                    // Check if access for groups or is set positively
                    if ($paymentMethod->ShowOnlyForGroups()->Count() > 0) {
                        foreach ($paymentMethod->ShowOnlyForGroups() as $paymentGroup) {
                            if ($member->Groups()->find('ID', $paymentGroup->ID)) {
                                $containedInGroup = true;
                                break;
                            }
                        }

                        if ($containedInGroup) {
                            $assumePaymentMethod = true;
                        } else {
                            $assumePaymentMethod = false;
                        }
                    }

                    // Check if access for users or is set positively
                    if ($paymentMethod->ShowOnlyForUsers()->Count() > 0) {
                        if ($paymentMethod->ShowOnlyForUsers()->find('ID', $member->ID)) {
                            $containedInUsers = true;
                        }

                        if ($containedInUsers) {
                            $assumePaymentMethod = true;
                        } else {
                            if (!$containedInGroup) {
                                $assumePaymentMethod = false;
                            }
                        }
                    }

                    // Check if access for groups is set negatively
                    if ($paymentMethod->ShowNotForGroups()->Count() > 0) {
                        foreach ($paymentMethod->ShowNotForGroups() as $paymentGroup) {
                            if ($member->Groups()->find('ID', $paymentGroup->ID)) {
                                if (!$containedInUsers) {
                                    $assumePaymentMethod = false;
                                }
                            }
                        }
                    }

                    // Check if access for users is set negatively
                    if ($paymentMethod->ShowNotForUsers()->Count() > 0) {
                        if ($paymentMethod->ShowNotForUsers()->find('ID', $member->ID)) {
                            if (!$containedInUsers) {
                                $assumePaymentMethod = false;
                            }
                        }
                    }
                }
                
                if ($assumePaymentMethod) {
                    $allowedPaymentMethods[] = $paymentMethod;
                }
            }
        }
        
        $allowedPaymentMethods = new DataObjectSet($allowedPaymentMethods);
        
        return $allowedPaymentMethods;
    }
    
    /**
     * Checks if the given member has completed enough orders with a
     * specified status.
     * 
     * @param Member $member The member object whose orders are checked
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.07.2011
     */
    protected function isActivationByOrderRestrictionsPossible(Member $member) {
        $isActivationByOrderRestrictionsPossible = false;
        $nrOfValidOrders                         = 0;
        
        if (!$member) {
           return $isActivationByOrderRestrictionsPossible;
        }
        
        if ($member->SilvercartOrder()) {
            foreach ($member->SilvercartOrder() as $orderObj) {
                if ($this->OrderRestrictionStatus()->find('ID', $orderObj->SilvercartOrderStatus()->ID)) {
                    $nrOfValidOrders++;
                }
                if ($nrOfValidOrders >= $this->orderRestrictionMinQuantity) {
                    break;
                }
            }
        }
        
        if ($nrOfValidOrders >= $this->orderRestrictionMinQuantity) {
            $isActivationByOrderRestrictionsPossible = true;
        }
        
        return $isActivationByOrderRestrictionsPossible;
    }

    /**
     * Returns allowed shipping methods. Those are
     * 
     * - shipping methods which are related directly to the payment method
     * - shipping methods which are NOT related to any payment method
     *
     * @return DataObjectSet
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.05.2011
     */
    public function getAllowedShippingMethods() {
        $allowedShippingMethods = array();
        $shippingMethods        = DataObject::get('SilvercartShippingMethod', 'isActive = 1');

        if ($shippingMethods) {
            foreach ($shippingMethods as $shippingMethod) {

                // Find shippping methods that are directly related to
                // payment methods....
                if ($shippingMethod->SilvercartPaymentMethods()->Count() > 0) {
                    
                    // ... and exclude them, if the current payment method is
                    // not related.
                    if (!$shippingMethod->SilvercartPaymentMethods()->find('ID', $this->ID)) {
                        continue;
                    }
                }
                
                // If there is no shipping fee defined for this shipping
                // method we don't want to show it.
                if ($shippingMethod->getShippingFee() !== false) {
                    $allowedShippingMethods[] = $shippingMethod;
                }
            }
        }
        
        $allowedShippingMethods = new DataObjectSet($allowedShippingMethods);
        
        return $allowedShippingMethods;
    }

    /**
     * Returns weather this payment method is available for a zone specified by id or not
     *
     * @param int $zoneId Zone id to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForZone($zoneId) {
        
    }

    /**
     * Is this payment method allowed for a shipping method?
     *
     * @param int $shippingMethodId Die ID id of shipping method to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForShippingMethod($shippingMethodId) {
        
    }

    /**
     * Is this payment method allowed for a total amount?
     *
     * @param int $amount Amount to be checked
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function isAvailableForAmount($amount) {
        $isAvailable    = false;
        $amount         = (float) $amount;
        $minAmount      = (float) $this->minAmountForActivation;
        $maxAmount      = (float) $this->maxAmountForActivation;

        if (($minAmount != 0.0 &&
             $maxAmount != 0.0)) {
            
            if ($amount >= $minAmount &&
                $amount <= $maxAmount) {

                $isAvailable = true;
            }
        } else if ($minAmount != 0.0) {
            if ($amount >= $minAmount) {
                $isAvailable = true;
            }
        } else if ($maxAmount != 0.0) {
            if ($amount <= $maxAmount) {
                $isAvailable = true;
            }
        } else {
            $isAvailable = true;
        }

        return $isAvailable;
    }

    /**
     * Hook: processed before order creation
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function processPaymentBeforeOrder() {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

    /**
     * Hook: processed after order creation
     *
     * @param Order $orderObj created order object
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 07.11.2010
     */
    public function processPaymentAfterOrder($orderObj) {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

    /**
     * Hook: called after jumpback from payment provider
     * processed before order creation
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    public function processReturnJumpFromPaymentProvider() {
        $this->controller->addCompletedStep();
        $this->controller->NextStep();
    }

    /**
     * possibility to return a text at the end of the order process
     * processed after order creation
     *
     * @param Order $orderObj the order object
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 06.01.2011
     */
    public function processPaymentConfirmationText($orderObj) {
        
    }

    /**
     * writes a payment method to the db in case none does exist yet
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();

        // Es handelt sich nicht um die Basisklasse
        // not a base class
        if ($this->moduleName !== '') {

            $className = $this->ClassName;
            /**
             * original expression
             * $has_multiple_payment_channels = $className::$has_multiple_payment_channels;
             * was replaced with eval call to provide compatibility to PHP 5.2
             */
            $has_multiple_payment_channels = eval('return ' . $className . '::$has_multiple_payment_channels;');

            if ($has_multiple_payment_channels) {
                $paymentModule = new $className();
                foreach ($paymentModule->getPossiblePaymentChannels() as $channel => $name) {
                    if (!DataObject::get_one($className, sprintf("`PaymentChannel`='%s'", $channel))) {
                        $paymentMethod = new $className();
                        $paymentMethod->isActive = 0;
                        $paymentMethod->Name = $name;
                        $paymentMethod->PaymentChannel = $channel;
                        $paymentMethod->write();
                    }
                }
            } elseif (!DataObject::get_one($className)) {
                // entry does not exist yet
                //prepayment's default record gets activated if test data is enabled
                if ($this->moduleName == "Prepayment" && SilvercartRequireDefaultRecords::isEnabledTestData()) {
                    $this->setField('isActive', 1);
                    //As we do not know if the country is instanciated yet we do write this relation in the country class too.
                    $germany = DataObject::get_one('SilvercartCountry', "`ISO2` = 'DE'");
                    if ($germany) {
                        $this->SilvercartCountries()->add($germany);
                    }
                } else {
                    $this->setField('isActive', 0);
                }
                $this->setField('Name', _t($className . '.NAME', $this->moduleName));
                $this->setField('Title', _t($className . '.TITLE', $this->moduleName));
                $this->write();
            }
        }
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('SilvercartShippingMethods'); //not needed because relations can not be set this way
        $fields->removeByName('SilvercartCountries');
        $fields->removeByName('SilvercartOrders');

        /*
         * add ability to set the relation to ShippingMethod with checkboxes
         */
        $shippingMethodsTable = new ManyManyComplexTableField(
                        $this,
                        'SilvercartShippingMethods',
                        'SilvercartShippingMethod',
                        array('Title' => 'Title'),
                        'getCMSFields_forPopup'
        );
        $shippingMethodsTable->setAddTitle(_t('SilvercartPaymentMethod.SHIPPINGMETHOD', 'shipping method'));
        $tabParam = "Root." . _t('SilvercartPaymentMethod.SHIPPINGMETHOD', 'shipping method');
        $fields->addFieldToTab($tabParam, $shippingMethodsTable);
        return $fields;
    }

    /**
     * Returns the detail fields for $this
     *
     * @param mixed $params optional parameters
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 12.11.2010
     * @deprecated This method should be replaced with getCMSFieldsForModules
     */
    public function getCmsFields_forPopup($params = null) {
        $fields = $this->getCMSFields();
        $fields->removeByName('Logos');
        return $fields;
    }

    /**
     * Returns modified CMS fields for the payment modules
     *
     * @return FieldSet
     */
    public function getCMSFieldsForModules() {
        $tabset = new TabSet('Sections');
        
        // --------------------------------------------------------------------
        // Common GUI elements for all payment methods
        // --------------------------------------------------------------------
        $tabBasic = new Tab('Basic', _t('SilvercartPaymentMethod.BASIC_SETTINGS', 'basic settings'));
        $tabset->push($tabBasic);
        
        $tabBasic->setChildren(
            new FieldSet(
                new TextField('Name', _t('SilvercartPaymentMethod.NAME')),
                new TextareaField('paymentDescription', _t('SilvercartShopAdmin.PAYMENT_DESCRIPTION')),
                new CheckboxField('isActive', _t('SilvercartShopAdmin.PAYMENT_ISACTIVE', 'activated')),
                new DropdownField(
                    'mode',
                    _t('SilvercartPaymentMethod.MODE', 'mode', null, 'Modus'
                    ),
                    array(
                        'Live' => _t('SilvercartShopAdmin.PAYMENT_MODE_LIVE'),
                        'Dev' => _t('SilvercartShopAdmin.PAYMENT_MODE_DEV')
                    ),
                    $this->mode
                ),
                new TextField('minAmountForActivation', _t('SilvercartShopAdmin.PAYMENT_MINAMOUNTFORACTIVATION')),
                new TextField('maxAmountForActivation', _t('SilvercartShopAdmin.PAYMENT_MAXAMOUNTFORACTIVATION')),
                new DropdownField(
                    'orderStatus',
                    _t('SilvercartPaymentMethod.STANDARD_ORDER_STATUS', 'standard order status for this payment method'),
                    SilvercartOrderStatus::getStatusList()->map('Code', 'Title', _t("SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE"))
                )
            )
        );

        // --------------------------------------------------------------------
        // GUI for management of logo images
        // --------------------------------------------------------------------
        $tabLogos = new Tab('Logos', _t('SilvercartPaymentMethod.PAYMENT_LOGOS', 'Payment Logos'));
        $tabset->push($tabLogos);

        $tabLogos->setChildren(
            new FieldSet(
                new CheckboxField('showPaymentLogos', _t('SilvercartShopAdmin.SHOW_PAYMENT_LOGOS')),
                new HasManyFileDataObjectManager($this, 'PaymentLogos', 'SilvercartImage', 'Image', null, null, sprintf("`SilvercartPaymentMethodID`='%d'", $this->ID))
            )
        );
        
        // --------------------------------------------------------------------
        // GUI for access management
        // --------------------------------------------------------------------
        $tabAccessManagement = new Tab('AccessManagement', _t('SilvercartPaymentMethod.ACCESS_SETTINGS', 'Access management'));
        $tabset->push($tabAccessManagement);
        
        $tabsetAccessManagement = new TabSet('AccessManagementSection');
        $tabAccessManagement->push($tabsetAccessManagement);
        
        $tabAccessManagementBasic = new Tab('AccessManagementBasic', _t('SilvercartPaymentMethod.ACCESS_MANAGEMENT_BASIC_LABEL', 'General'));
        $tabAccessManagementGroup = new Tab('AccessManagementGroup', _t('SilvercartPaymentMethod.ACCESS_MANAGEMENT_GROUP_LABEL', 'By group(s)'));
        $tabAccessManagementUser  = new Tab('AccessManagementUser',  _t('SilvercartPaymentMethod.ACCESS_MANAGEMENT_USER_LABEL', 'By user(s)'));
        $tabsetAccessManagement->push($tabAccessManagementBasic);
        $tabsetAccessManagement->push($tabAccessManagementGroup);
        $tabsetAccessManagement->push($tabAccessManagementUser);
        
        $showOnlyForGroupsTable = new ManyManyComplexTableField(
            $this,
            'ShowOnlyForGroups',
            'Group'
        );
        $showOnlyForGroupsTable->setPermissions(array('show'));
        $showNotForGroupsTable = new ManyManyComplexTableField(
            $this,
            'ShowNotForGroups',
            'Group'
        );
        $showNotForGroupsTable->setPermissions(array('show'));
        $showOnlyForUsersTable = new ManyManyComplexTableField(
            $this,
            'ShowOnlyForUsers',
            'Member',
            null,
            null,
            "Member.ClassName != 'SilvercartAnonymousCustomer'"
        );
        $showOnlyForUsersTable->setPermissions(array('show'));
        $showNotForUsersTable = new ManyManyComplexTableField(
            $this,
            'ShowNotForUsers',
            'Member',
            null,
            null,
            "Member.ClassName != 'SilvercartAnonymousCustomer'"
        );
        $showNotForUsersTable->setPermissions(array('show'));
        
        $restrictionByOrderQuantityField = new TextField('orderRestrictionMinQuantity', '');
        $restrictionByOrderStatusField   = new ManyManyComplexTableField(
            $this,
            'OrderRestrictionStatus',
            'SilvercartOrderStatus'
        );
        
        // Access management basic --------------------------------------------
        $tabAccessManagementBasic->push(
            new HeaderField('RestrictionLabel', _t('SilvercartPaymentMethod.RESTRICTION_LABEL').':', 2)
        );
        $tabAccessManagementBasic->push(new LiteralField('separatorForActivationByOrderRestrictions', '<hr />'));
        $tabAccessManagementBasic->push(
            new CheckboxField(
                'enableActivationByOrderRestrictions',
                _t('SilvercartPaymentMethod.ENABLE_RESTRICTION_BY_ORDER_LABEL')
            )
        );
        $tabAccessManagementBasic->push(
            new LiteralField('RestrictionByOrderQuantityLabel', '<p>'._t('SilvercartPaymentMethod.RESTRICT_BY_ORDER_QUANTITY').':</p>')
        );
        $tabAccessManagementBasic->push($restrictionByOrderQuantityField);
        $tabAccessManagementBasic->push(
            new LiteralField('RestrictionByOrderStatusLabel', '<p>'._t('SilvercartPaymentMethod.RESTRICT_BY_ORDER_STATUS').':</p>')
        );
        $tabAccessManagementBasic->push(
            $restrictionByOrderStatusField
        );
        
        // Access management for groups ---------------------------------------
        $tabAccessManagementGroup->push(
            new HeaderField('ShowOnlyForGroupsLabel', _t('SilvercartPaymentMethod.SHOW_ONLY_FOR_GROUPS_LABEL').':', 2)
        );
        $tabAccessManagementGroup->push($showOnlyForGroupsTable);
        $tabAccessManagementGroup->push(
            new HeaderField('ShowNotForGroupsLabel', _t('SilvercartPaymentMethod.SHOW_NOT_FOR_GROUPS_LABEL').':', 2)
        );
        $tabAccessManagementGroup->push($showNotForGroupsTable);
        
        // Access management for users ----------------------------------------
        $tabAccessManagementUser->push(
            new HeaderField('ShowOnlyForUsersLabel', _t('SilvercartPaymentMethod.SHOW_ONLY_FOR_USERS_LABEL').':', 2)
        );
        $tabAccessManagementUser->push($showOnlyForUsersTable);
        $tabAccessManagementUser->push(
            new HeaderField('ShowNotForUsersLabel', _t('SilvercartPaymentMethod.SHOW_NOT_FOR_USERS_LABEL').':', 2)
        );
        $tabAccessManagementUser->push($showNotForUsersTable);
        
        return new FieldSet($tabset);
    }

    /**
     * Returns the original CMSFields.
     *
     * @return FieldSet
     */
    public function getCMSFieldsOriginal() {
        return parent::getCMSFields();
    }

    /**
     * set the link to be visited on a cancel action
     *
     * @param string $link the url
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function setCancelLink($link) {
        $this->cancelLink = $link;
    }

    /**
     * sets the link to return to the shop
     *
     * @param string $link the url
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function setReturnLink($link) {
        $this->returnLink = $link;
    }

    /**
     * set the controller
     *
     * @param Controller $controller the controller action
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    public function setController($controller) {
        $this->controller = $controller;
    }

    /**
     * Returns the attributed countries as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedCountries() {
        $attributedCountriesStr = '';
        $attributedCountries = array();
        $maxLength = 150;

        foreach ($this->SilvercartCountries() as $country) {
            $attributedCountries[] = $country->Title;
        }

        if (!empty($attributedCountries)) {
            $attributedCountriesStr = implode(', ', $attributedCountries);

            if (strlen($attributedCountriesStr) > $maxLength) {
                $attributedCountriesStr = substr($attributedCountriesStr, 0, $maxLength) . '...';
            }
        }

        return $attributedCountriesStr;
    }

    /**
     * Returns the attributed zones as string (limited to 150 chars).
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function AttributedZones() {
        $attributedZonesStr = '';
        $attributedZones = array();
        $maxLength = 150;

        foreach ($this->SilvercartZone() as $zone) {
            $attributedZones[] = $zone->Title;
        }

        if (!empty($attributedZones)) {
            $attributedZonesStr = implode(', ', $attributedZones);

            if (strlen($attributedZonesStr) > $maxLength) {
                $attributedZonesStr = substr($attributedZonesStr, 0, $maxLength) . '...';
            }
        }

        return $attributedZonesStr;
    }

    /**
     * Returns the activation status as HTML-Checkbox-Tag.
     *
     * @return CheckboxField
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function activatedStatus() {
        $checkboxField = new CheckboxField('isActivated' . $this->ID, 'isActived', $this->isActive);

        return $checkboxField;
    }

    /**
     * writes a log entry
     *
     * @param string $context the context for the log entry
     * @param string $text    the text for the log entry
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    public function Log($context, $text) {
        $path = Director::baseFolder() . '/silvercart/log/' . $this->ClassName . '.log';
        $text = sprintf(
                "%s - Method: '%s' - %s\n", date('Y-m-d H:i:s'), $context, $text
        );
        file_put_contents($path, $text, FILE_APPEND);
    }

    /**
     * registers an error
     *
     * @param string $errorText text for the error message
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    public function addError($errorText) {
        array_push($this->errorList, $errorText);
    }

    /**
     * Sets the customers details
     *
     * @param Member $customerDetails Details of customer
     *
     * @return void
     */
    public function setCustomerDetails(Member $customerDetails) {
        $this->customerDetails = $customerDetails;
    }

    /**
     * Sets the invoice address
     *
     * @param SilvercartAddress $invoiceAddress Invoice address
     *
     * @return void
     */
    public function setInvoiceAddress(SilvercartAddress $invoiceAddress) {
        $this->invoiceAddress = $invoiceAddress;
    }

    /**
     * Sets the shipping address
     *
     * @param SilvercartAddress $shippingAddress Shipping address
     *
     * @return void
     */
    public function setShippingAddress(SilvercartAddress $shippingAddress) {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * Sets the shopping cart
     *
     * @param SilvercartShoppingCart $shoppingCart Shopping cart
     *
     * @return void
     */
    public function setShoppingCart(SilvercartShoppingCart $shoppingCart) {
        $this->shoppingCart = $shoppingCart;
    }

    /**
     * Sets the order object
     *
     * @param SilvercartOrder $order The order object
     *
     * @return void
     */
    public function setOrder(SilvercartOrder $order) {
        $this->order = $order;
    }

    /**
     * Returns the customers details
     *
     * @return Member
     */
    public function getCustomerDetails() {
        return $this->customerDetails;
    }

    /**
     * Returns the invoice address
     *
     * @return SilvercartAddress
     */
    public function getInvoiceAddress() {
        return $this->invoiceAddress;
    }

    /**
     * Returns the shipping address
     *
     * @return SilvercartAddress
     */
    public function getShippingAddress() {
        return $this->shippingAddress;
    }

    /**
     * Returns the shopping cart
     *
     * @return SilvercartShoppingCart
     */
    public function getShoppingCart() {
        return $this->shoppingCart;
    }

    /**
     * Returns the step configuration.
     *
     * Should return an array with the following structure:
     * array(
     *     '{insert module-filesystem-name}/templates/checkout/' => array(
     *         'prefix' => 'SilvercartPayment{insert module-class-name}CheckoutFormStep'
     *     )
     * )
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.04.2011
     */
    public function getStepConfiguration() {
        $directory = 'silvercart_payment_' . strtolower($this->moduleName) . '/templates/checkout/';
        $className = $this->ClassName;
        /**
         * original expression
         * $has_multiple_payment_channels = $className::$has_multiple_payment_channels;
         * was replaced with eval call to provide compatibility to PHP 5.2
         */
        $has_multiple_payment_channels = eval('return ' . $className . '::$has_multiple_payment_channels;');
        if ($has_multiple_payment_channels
            && !empty($this->PaymentChannel)
            && is_string($this->PaymentChannel)) {
            
            $directory .= $this->PaymentChannel . '/';
            $stepModule = $this->moduleName . ucfirst($this->PaymentChannel);
        } else {
            $stepModule = $this->moduleName;
        }
        if ($this->ShowFormFieldsOnPaymentSelection) {
            $stepModule .= 'Preceded';
        }
        $prefix = 'SilvercartPayment' . $stepModule . 'CheckoutFormStep';
        return array(
            $directory => array(
                'prefix' => $prefix,
            ),
        );
    }

    /**
     * Returns the order
     *
     * @return SilvercartOrder
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setCustomerDetailsByCheckoutData($checkoutData) {
        $customerDetails = new Member();
        $customerDetails->Email = isset($checkoutData['Email']) ? $checkoutData['Email'] : '';
        $customerDetails->Salutation = isset($checkoutData['Invoice_Salutation']) ? $checkoutData['Invoice_Salutation'] : '';
        $customerDetails->FirstName = isset($checkoutData['Invoice_FirstName']) ? $checkoutData['Invoice_FirstName'] : '';
        $customerDetails->Surname = isset($checkoutData['Invoice_Surname']) ? $checkoutData['Invoice_Surname'] : '';
        $this->setCustomerDetails($customerDetails);
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setInvoiceAddressByCheckoutData($checkoutData) {
        $invoiceAddress = new SilvercartAddress();
        $invoiceAddress->Salutation = isset($checkoutData['Invoice_Salutation']) ? $checkoutData['Invoice_Salutation'] : '';
        $invoiceAddress->FirstName = isset($checkoutData['Invoice_FirstName']) ? $checkoutData['Invoice_FirstName'] : '';
        $invoiceAddress->Surname = isset($checkoutData['Invoice_Surname']) ? $checkoutData['Invoice_Surname'] : '';
        $invoiceAddress->Street = isset($checkoutData['Invoice_Street']) ? $checkoutData['Invoice_Street'] : '';
        $invoiceAddress->StreetNumber = isset($checkoutData['Invoice_StreetNumber']) ? $checkoutData['Invoice_StreetNumber'] : '';
        $invoiceAddress->Postcode = isset($checkoutData['Invoice_Postcode']) ? $checkoutData['Invoice_Postcode'] : '';
        $invoiceAddress->City = isset($checkoutData['Invoice_City']) ? $checkoutData['Invoice_City'] : '';
        $invoiceAddress->CountryID = isset($checkoutData['Invoice_Country']) ? $checkoutData['Invoice_Country'] : '';
        $invoiceAddress->PhoneAreaCode = isset($checkoutData['Invoice_PhoneAreaCode']) ? $checkoutData['Invoice_PhoneAreaCode'] : '';
        $invoiceAddress->Phone = isset($checkoutData['Invoice_Phone']) ? $checkoutData['Invoice_Phone'] : '';

        // Insert SilvercartCountry object
        $invoiceAddress->Country = DataObject::get_by_id('SilvercartCountry', $invoiceAddress->CountryID);

        $this->setInvoiceAddress($invoiceAddress);
    }

    /**
     * Sets the customers details by checkout data
     *
     * @param array $checkoutData Checkout data
     *
     * @return void
     */
    public function setShippingAddressByCheckoutData($checkoutData) {
        $shippingAddress = new SilvercartAddress();
        $shippingAddress->Salutation = isset($checkoutData['Shipping_Salutation']) ? $checkoutData['Shipping_Salutation'] : '';
        $shippingAddress->FirstName = isset($checkoutData['Shipping_FirstName']) ? $checkoutData['Shipping_FirstName'] : '';
        $shippingAddress->Surname = isset($checkoutData['Shipping_Surname']) ? $checkoutData['Shipping_Surname'] : '';
        $shippingAddress->Street = isset($checkoutData['Shipping_Street']) ? $checkoutData['Shipping_Street'] : '';
        $shippingAddress->StreetNumber = isset($checkoutData['Shipping_StreetNumber']) ? $checkoutData['Shipping_StreetNumber'] : '';
        $shippingAddress->Postcode = isset($checkoutData['Shipping_Postcode']) ? $checkoutData['Shipping_Postcode'] : '';
        $shippingAddress->City = isset($checkoutData['Shipping_City']) ? $checkoutData['Shipping_City'] : '';
        $shippingAddress->CountryID = isset($checkoutData['Shipping_Country']) ? $checkoutData['Shipping_Country'] : '';
        $shippingAddress->PhoneAreaCode = isset($checkoutData['Shipping_PhoneAreaCode']) ? $checkoutData['Shipping_PhoneAreaCode'] : '';
        $shippingAddress->Phone = isset($checkoutData['Shipping_Phone']) ? $checkoutData['Shipping_Phone'] : '';

        // Insert SilvercartCountry object
        $shippingAddress->Country = DataObject::get_by_id('SilvercartCountry', $shippingAddress->CountryID);

        $this->setShippingAddress($shippingAddress);
    }

    /**
     * Returns all possible payment channels of the current payment module.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.03.2011
     */
    public function getPossiblePaymentChannels() {
        $possiblePaymentChannels = array();
        $className = $this->ClassName;
        /**
         * original expression
         * $has_multiple_payment_channels = $className::$has_multiple_payment_channels;
         * was replaced with eval call to provide compatibility to PHP 5.2
         */
        $has_multiple_payment_channels = eval('return ' . $className . '::$has_multiple_payment_channels;');
        $possible_payment_channels = eval('return ' . $className . '::$possible_payment_channels;');
        if ($has_multiple_payment_channels == false
                || count($possible_payment_channels) == 0) {
            return array();
        }
        foreach ($possible_payment_channels as $key => $value) {
            $possiblePaymentChannels[$key] = _t($this->ClassName . '.PAYMENT_CHANNEL_' . strtoupper($key), $value);
        }
        return $possiblePaymentChannels;
    }

    /**
     * Returns the i18n title for a payment channel.
     *
     * @param string $paymentChannel The payment channel
     *
     * @return string
     */
    public function getPaymentChannelName($paymentChannel) {
        return _t($this->ClassName . '.PAYMENT_CHANNEL_' . strtoupper($paymentChannel));
    }
    
    /**
     * Returns an optional payment specific form name to insert into checkout step 3.
     *
     * @return string|boolean
     */
    public function getNestedFormName() {
        return false;
    }

}
