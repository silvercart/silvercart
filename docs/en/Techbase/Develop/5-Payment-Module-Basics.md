# Payment module basics

<div class="warning" markdown='1'>
**Stop:** This page is still under construction with several details not yet written.

It's published because some developers need parts of the information right now - use it on your own risk. 
</div>

## What is a SilverCart payment module
- - -

A payment module is integrated into the checkout process and gives your customers a way to pay for their shopping cart. It consists of a payment class, a sequence of steps and language files.
### Directory structure of a payment module

Regarding it's directory structure, a SilverCart payment module is organized like an ordinary Silverstripe module.

You create a folder structure on the root level of your Silverstripe installation that looks like the following:

	+ customhtmlform
	...
	+ silvercart
	...
	- silvercart_payment_mycustomname
	  - checkout
		- SilvercartPaymentMyCustomNameFormStep1.php
		- SilvercartPaymentMyCustomNameFormStep2.php
		- ...
	  - code
		  SilvercartPaymentMyCustomName.php
	  - lang
		  en_US.php
		  ...
	  - templates
		- checkout
		  - SilvercartPaymentMyCustomNameFormStep1.ss
		  - SilvercartPaymentMyCustomNameFormStep2.ss
	  _config.php

### Bundling code in the payment class

All of your business logic should be located in the payment class (“SilvercartPaymentMyCustomName” in our above example).
### Payment sequence steps

The payment module sequence steps can display informations to your customer, ask data from them or execute processing logic like getting tokens from payment providers or jumping to a payment provider's checkout.

During the payment module sequence the shopping cart gets transferred into an order.
## Points of action
- - -

Have a look at this sequence which describes the checkout process:

![](_images/payment_module_sequence.png)

Actually our payment module has two key points of action:

1. The entry for the “Choose payment method” page
2. The payment method sequence after the customer has chosen our payment module for paying

### Make your module appear one the "Choose payment method" page

Our goal is to add an existing custom payment module to the payment options on the “Choose your payment” page:

![](_images/payment_not_available.png)

### 1) Check if the module is installed properly

In the screenshot below you can see the payment module “MyCustomName” in the payment method administration area. That means the module has been installed properly but with the current setup it won't yet be available in the checkout process.

![](_images/payment_admin_deactivated.png)

### 2) Activate the module

Click on the module's name in the table to go to the module configuration page. There you'll see a checkbox “Activated” - click on it and save the settings.

![](_images/payment_module_activation.png)

### 3) Attribute the module to a country

Now we have to attribute our payment module to a country. To do that we have to go to the country configuration section.

In the following example we attribute the module to the “United States”. Clicking on the name of the country in the table leads us to the country's edit page where we can take action.

![](_images/payment_country_attribution_1.png)

Under the tab “Payment method” you can attribute payment methods to the country by activating or deactivating the checkbox left to the payment method's name. Don't forget to save the settings.

![](_images/payment_country_attribution_2.png)

After activating the module “MyCustomName” everything is set so that our module will appear in the checkout process.
### 4) See your module in the checkout process

Et voilà - our custom module is displayed as a payment option:

![](_images/payment_country_attribution_3.png)

### Define your custom checkout sequence

The payment process is a sequence of steps. Those steps can differ for every payment module and can be defined without limitations as long as you respect some conventions.

Payment steps are actually CustomHtmlForm steps, so every step is a class/template file tandem.
#### Conventions for sequence files

1. Place your sequence of steps in the directory “silvercart_payment_mycustomname/checkout/”
2. Use the naming scheme “SilvercartPayment{MyCustomName[FormStep{n[.php” for the class files.
     * If you have two steps for your payment module “MyCustomName” your class files should look like this:
       * SilvercartPaymentMyCustomNameFormStep1.php
       * SilvercartPaymentMyCustomNameFormStep2.php
3. Every step needs a template for the php class. The templates should be located inside the folder “silvercart_payment_mycustomname/templates/checkout/”.
4. The templates need to follow the naming scheme for the php classes, except that the file extension is ”.ss” instead of ”.php”.
   * According to our example for the php classes your template files should be named like this:
     * SilvercartPaymentMyCustomNameFormStep1.ss
     * SilvercartPaymentMyCustomNameFormStep2.ss


## Skeleton for a custom payment class
- - -

###### SilvercartPaymentMyCustomName.php

	:::php
	<?php
	/**
	 * Class description.
	 *
	 * @package {package}
	 * @subpackage {subpackage}
	 * @author {author}
	 * @since {since}
	 */
	class SilvercartPaymentMyCustomName extends SilvercartPaymentMethod {
	
		/**
		 * contains module name for display in the admin backend
		 *
		 * @var string
		 *
		 * @author {author}
		 * @since {since}
		 */
		protected $moduleName = 'MyCustomName';
		
		/**
		 * Indicates whether a payment module has multiple payment channels or not.
		 *
		 * @var bool
		 *
		 * @author {author}
		 * @since {since}     */
		public static $has_multiple_payment_channels = false;
		
		/**
		 * A list of possible payment channels.
		 *
		 * @var array
		 *
		 * @author {author}
		 * @since {since}
		 */
		public static $possible_payment_channels = array();
		
		/**
		 * returns CMS fields
		 *
		 * @param mixed $params optional
		 *
		 * @return FieldSet
		 *
		 * @author {author}
		 * @since {since}
		 */
		public function getCMSFields($params = null) {
			$fields         = parent::getCMSFieldsForModules($params);
			$fieldLabels    = self::$field_labels;
			
			return $fields;
		}
		
		// ------------------------------------------------------------------------
		// processing methods
		// ------------------------------------------------------------------------
		
		/**
		 * hook to be called before order creation
		 *
		 * You can use this method to create tokens and/or jump to your payment
		 * provider.
		 *
		 * @return void
		 *
		 * @author {author}
		 * @since {since}
		 */
		public function processPaymentBeforeOrder() {
		}
		
		/**
		 * hook to be called after jumpback from payment provider; called before
		 * order creation
		 *
		 * @return void
		 *
		 * @author {author}
		 * @since {since}
		 */
		public function processReturnJumpFromPaymentProvider() {
		}
		
		/**
		 * hook to be called after order creation
		 *
		 * @param Order $orderObj object to be processed
		 *
		 * @return void
		 *
		 * @author {author}
		 * @since {since}
		 */
		public function processPaymentAfterOrder($orderObj = array()) {
		}
		
		// ------------------------------------------------------------------------
		// payment module specific methods
		// ------------------------------------------------------------------------
		
		// ...
	}


## Helper methods from the SilvercartPaymentMethod class
- - -
Since your payment class extends the SilvercartPaymentMethod class you have access to a bunch of useful methods:

	:::php
	$this->getReturnLink();
	$this->getCancelLink();
	$this->getInvoiceAddress();
	$this->getShippingAddress();
	$this->getCustomerDetails();
	$this->getShoppingCart();
	$this->getOrder();


Let's take a closer look at those methods:
### getOrder()

Keep in mind that this method returns the desired information only after the order has been saved in the webshop. So you should only call it from the method “processPaymentAfterOrder” or later in your payment class.
### getShoppingCart()
### getInvoiceAddress()
### getShippingAddress()
### getCustomerDetails()
### getReturnLink()
### getCancelLink()
## Helper classes for payment sequence steps
- - -
SilverCart offers CustomHtmlForms that provide basic functionality and can be extended by every payment module's steps:

* **SilvercartCheckoutFormStepPaymentInit**
  * This is the common base for all steps that don't need the following two special cases. It checks if the user may use the checkout process and provides handy methods e.g. for getting the current payment method object.
* **SilvercartCheckoutFormStepProcessOrder**
  * This is the step that transfers the shoppingcart to an order.
* **SilvercartCheckoutFormStepDefaultOrderConfirmation**
  * Usually the last step in the checkout process. It displays a status page that informs the customer about the current status.
     
Let's have a look at how some existing payment modules use that structure:
### Example: Prepayment

* **SilvercartPaymentPrepaymentCheckoutFormStep1:** Extends “SilvercartCheckoutFormStepPaymentInit”
* **SilvercartPaymentPrepaymentCheckoutFormStep2:** Extends “SilvercartCheckoutFormStepProcessOrder”
  * The shoppingcart gets transferred to an order
* **SilvercartPaymentPrepaymentCheckoutFormStep3:** Extends “SilvercartCheckoutFormStepDefaultOrderConfirmation”

### Example: Paypal

* **SilvercartPaymentPaypalCheckoutFormStep1:** Extends “SilvercartCheckoutFormStepPaymentInit”
  * Redirects the user to the paypal checkout website
* **SilvercartPaymentPaypalCheckoutFormStep2:** Extends “SilvercartCheckoutFormStepPaymentInit”
  * Handles the jump back from paypal to the shop website
* **SilvercartPaymentPaypalCheckoutFormStep3:** Extends “SilvercartCheckoutFormStepProcessOrder”
  * The shoppingcart gets transferred to an order
* **SilvercartPaymentPaypalCheckoutFormStep4:** Extends “SilvercartCheckoutFormStepPaymentInit”
  * Approves the order and payment with paypal
* **SilvercartPaymentPaypalCheckoutFormStep5:** Extends “SilvercartCheckoutFormStepDefaultOrderConfirmation”
  * Displays the standard order confirmation page to the customer

##Jumping from the webshop to the payment provider
- - -
### Implement the method "processPaymentBeforeOrder" in your payment class

	:::php
	public function processPaymentBeforeOrder() {
		$this->controller->addCompletedStep($this->controller->getCurrentStep());
		$this->controller->addCompletedStep($this->controller->getNextStep());
		$this->controller->setCurrentStep($this->controller->getNextStep());
		
		if ($this->mode == 'Live') {
			Director::redirect({LIVE_URL_TO_YOUR_PAYMENT_PROVIDER_GATEWAY});
		} else {
			Director::redirect({DEVELOPMENT_URL_TO_YOUR_PAYMENT_PROVIDER_GATEWAY});
		}
	}

What are we doing here?

	:::php
	$this->controller->addCompletedStep($this->controller->getCurrentStep());

Since we are in the checkout process we want to set the current step as completed. This is important because of the return link that we get from “$this→getReturnLink();”; this link directs to the exact same URL in the checkout process where we are. If the current step is not completed it would be displayed again.

	:::php
	$this->controller->addCompletedStep($this->controller->getNextStep());

We set the following step as completed, too, because it contains a processor method only.

	:::php
	$this->controller->setCurrentStep($this->controller->getNextStep());

Finally we set the current step to the following one, so that the correct step gets executed after the return jump from the payment provider.

	:::php
	if ($this->mode == 'Live') {
		Director::redirect({LIVE_URL_TO_YOUR_PAYMENT_PROVIDER_GATEWAY});
	} else {
		Director::redirect({DEVELOPMENT_URL_TO_YOUR_PAYMENT_PROVIDER_GATEWAY});
	}

This is the code that executes the jump to the payment provider. In this case we distinguish between live and dev mode. If your payment provider differentiates live and dev mode by another way you could just do a “Director::redirect()” here without the if block.
### Call "processPaymentBeforeOrder" from your sequence step

	:::php
	public function process() {
		if (parent::process()) {
			$this->paymentMethodObj->processPaymentBeforeOrder();
		}
	}

## Channels for payment modules
- - -

### What is a channel?
### Defining channels