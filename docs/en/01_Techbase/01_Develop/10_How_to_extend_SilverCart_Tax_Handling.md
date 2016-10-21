# How to extend SilverCart Tax Handling

Basically, SilverCart works with static tax rates, created in backend and related to the products.

But there are some cases where it is necessary to have a dynamic tax handling, e.g. dependant on some customer data like the customers shipping country.

### What do I need to extend the SilverCart Tax Handling?

Well, we use the decorator pattern to extend the tax handling, so you need a decorator.

First, let's create MySilvercartTaxDecorator.php to add the custom handling to the tax system.

What we need to do that is an extension of DataObjectDecorator which provides the method getTaxRate(). getTaxRate() needs to return the tax rate as a Float value.

The decorator in /mysite/code/MySilvercartTaxDecorator.php should look like that:

	:::php
	<?php
	
	/**
	 * Decorates SilvercartTax.
	 * 
	 * @author Sebastian Diel <sdiel@pixeltricks.de>
	 * @since 06.11.2013
	 */
	class MySilvercartTaxDecorator extends DataObjectDecorator {
	
		/**
		 * Overwrites SilverCart's original tax handling.
		 *
		 * @return float
		 * 
		 * @author Sebastian Diel <sdiel@pixeltricks.de>
		 * @since 10.11.2011
		 */
		public function getTaxRate() {
			$taxRate = 0;
	
			// your tax handling here
	
			return $taxRate;
		}
	
	}

#### Register the Decorator

To register the decorator to the right base object, open your /mysite/_config.php and add the following line:

	:::php
	Object::add_extension('SilvercartTax', 'MySilvercartTaxDecorator');

### Example #1: Adding Country Dependant Tax Rates

Let's say, you have extended the SilvercartCountry Object to have an extended tax handling for any country.

This simple example expects that you have implemented a decorator for SilvercartCountry that provides a method getTaxRateFor() and three static country dependant tax rates.
The decorator could look something like that:

	:::php
	<?php
	
	/**
	 * Decorates SilvercartCountry.
	 * Provides country dependant tax handling.
	 * 
	 * @author Sebastian Diel <sdiel@pixeltricks.de>
	 * @since 06.11.2013
	 */
	class MySilvercartCountryDecorator extends DataObjectDecorator {
	
		/**
		 * Additional statics for the decorated DataObject. Adds some country 
		 * dependant static tax rates.
		 * 
		 * @return array
		 * 
		 * @author Sebastian Diel <sdiel@pixeltricks.de>
		 * @since 05.11.2013
		 */
		public function extraStatics() {
			return array(
				'db' => array(
					'TaxRate1' => 'Float',
					'TaxRate2' => 'Float',
					'TaxRate3' => 'Float',
				),
			);
		}
	
		/**
		 * Overwrites SilverCart's original tax handling.
		 *
		 * @param SilvercartTax $tax Base tax object related to the product to get tax for.
		 *
		 * @return float
		 * 
		 * @author Sebastian Diel <sdiel@pixeltricks.de>
		 * @since 10.11.2011
		 */
		public function getTaxRateFor(SilvercartTax $tax) {
			$taxRate = 0;
	
			switch($tax->Identifier) {
				case '1':
					$taxRate = $this->owner->TaxRate1;
					break;
				case '2':
					$taxRate = $this->owner->TaxRate2;
					break;
				case '3':
					$taxRate = $this->owner->TaxRate3;
					break;
				default:
					$taxRate = 0;
			}
	
			return $taxRate;
		}
	
	}


Now, your decorator could look like that to bring some shipping country dependant tax rates:

	:::php
	<?php
	
	/**
	 * Decorates SilvercartTax.
	 * The tax will be determined dependant on the customers shipping coutry.
	 * 
	 * @author Sebastian Diel <sdiel@pixeltricks.de>
	 * @since 06.11.2013
	 */
	class MySilvercartTaxDecorator extends DataObjectDecorator {
	
		/**
		 * Overwrites SilverCart's original tax handling.
		 *
		 * @return float
		 * 
		 * @author Sebastian Diel <sdiel@pixeltricks.de>
		 * @since 10.11.2011
		 */
		public function getTaxRate() {
			$taxRate = 0;
	
			// your tax handling here
			$member = Member::currentUser();
			if ($member instanceof Member) {
				$shippingAddress = $member->SilvercartShippingAddress();
				if ($shippingAddress instanceof SilvercartAddress) {
					$shippingCountry = $shippingAddress->SilvercartCountry();
					if ($shippingCountry instanceof SilvercartCountry) {
						$taxRate = $shippingCountry->getTaxRateFor($this->owner);
					}
				}
			}
	
			return $taxRate;
		}
	
	}

#### Register the Decorators

To register the decorators to the right base objects, open your /mysite/_config.php and add the following lines:

	:::php
	Object::add_extension('SilvercartCountry', 'MySilvercartCountryDecorator');
	Object::add_extension('SilvercartTax',     'MySilvercartTaxDecorator');

#### Flush your cache and build your changes

To get the new stuff working, run a /dev/build/?flush=all on your project:

[http://YOUR_PROJECTS_URL/dev/build/?flush=all]()

Now, you should have custom tax handling for all your products.


### Example #2: Adding Custom API Dependant Tax Rates

Let's say, you have an API provider that returns a tax rate by given customer data.

Now, your decorator could look like that to bring some custom tax API dependant tax rates:

	:::php
	<?php
	
	/**
	 * Decorates SilvercartTax.
	 * The tax will be determined dependant on the customers shipping coutry
	 * using a custom tax API.
	 * 
	 * @author Sebastian Diel <sdiel@pixeltricks.de>
	 * @since 06.11.2013
	 */
	class MySilvercartTaxDecorator extends DataObjectDecorator {
	
		/**
		 * Overwrites SilverCart's original tax handling.
		 *
		 * @return float
		 * 
		 * @author Sebastian Diel <sdiel@pixeltricks.de>
		 * @since 10.11.2011
		 */
		public function getTaxRate() {
			$taxRate = 0;
	
			// your tax handling here
			$member = Member::currentUser();
			if ($member instanceof Member) {
				// Call the tax API here to get some customer dependant tax data
				// using a custom API connector object.
				$myCustomTaxAPI = new MyCustomTaxAPIConnector();
				$taxRate = $myCustomTaxAPI->getTaxRateFor($member);
			}
	
			return $taxRate;
		}
	
	}

#### Register the Decorators

To register the decorator to the right base object, open your /mysite/_config.php and add the following line:

	:::php
	Object::add_extension('SilvercartTax', 'MySilvercartTaxDecorator');

#### Flush your cache and build your changes

To get the new stuff working, run a /dev/build/?flush=all on your project:

[http://YOUR_PROJECTS_URL/dev/build/?flush=all]()

Now, you should have custom tax handling for all your products.