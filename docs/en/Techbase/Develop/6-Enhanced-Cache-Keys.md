# Enhanced Cache Keys

## Why do we need enhanced cache keys?
- - -

Best practice to build a cache key in a DataObject context is to use its 'LastEdited' property.
Sometimes, it is not the best choice to rely on this attribute, because the frontend cache will be rewritten although there were no changes made for display relevant data.

**Example:**
Let's have a look at SilvercartProduct. A huge part of SilverCart's frontend is in dependency of at least one product, so it is important to have the right cache keys for product dependant views.
Building the cache is expensive, the process needs a lot of resources. So we want the cache only to rebuild itself when it is really necessary.
Problem: If a product is bought by a customer, the stock quantity will change. This will modify the LastEdited date. Now, the product lists, widgets and other views cache keys are renewed, the cache will be rebuild.
We do not want this behavior, so we need to use enhanced cache keys for a product by limiting the view relevant DB attributes. 

## How to limit the cache relevant fields?
- - -

To limit a DataObjects cache relevant fields, we need to use the SilvercartDataObject as a decorator for our target DataObject.
In our example, we need to add the extension to SilvercartProduct **and** its translation object (SilvercartProductLanguage).

	:::php
	Object::add_extension('SilvercartProduct',                          'SilvercartDataObject');
	Object::add_extension('SilvercartProductLanguage',                  'SilvercartDataObject');

SilvercartDataObject manipulates a DataObjects write process and checks whether a cache relevant field has changed. If such a fields has changed, the object will be marked to renew the cache by setting its property LastEditedForCache to the current date and time.
To define the cache relevant fields, we need to add the method 'getCacheRelevantFields()' to our DataObject. So, we add the following code to our example object, SilvercartProduct:

	:::php
	
	/**
	 * Sets the cache relevant fields as an array.
	 * 
	 * @return array
	 */
	public function getCacheRelevantFields() {
	    $cacheRelevantFields = array(
	        'isActive',
	        'ProductNumberShop',
	        'ProductNumberManufacturer',
	        'EANCode',
	        'PriceGrossAmount',
	        'PriceNetAmount',
	        'MSRPriceAmount',
	        'PurchaseMinDuration',
	        'PurchaseMaxDuration',
	        'PurchaseTimeUnit',
	        'PackagingQuantity',
	        'StockQuantity'     => 0,
	        'SilvercartTaxID',
	        'SilvercartManufacturerID',
	        'SilvercartProductGroupID',
	        'SilvercartAvailabilityStatusID',
	        'SilvercartProductConditionID',
	        'SilvercartQuantityUnitID',
	    );
	    $this->extend('updateCacheRelevantFields', $cacheRelevantFields);
	    return $cacheRelevantFields;
	}

Same will be done to SilvercartProductLanguage:

	:::php
	
	/**
	 * Sets the cache relevant fields.
	 * 
	 * @return array
	 */
	public function getCacheRelevantFields() {
	    $cacheRelevantFields = array(
	        'Title',
	        'ShortDescription',
	        'LongDescription',
	        'MetaDescription',
	        'MetaTitle',
	        'MetaKeywords',
	    );
	    $this->extend('updateCacheRelevantFields', $cacheRelevantFields);
	    return $cacheRelevantFields;
	}

A special field is 'StockQuantity'. You can see, that the 'StockQuantity' is set as a key value pair. This means, that the field defined as key ('StockQuantity') is only relevant when its value when writing the object is exactly the given value after writing **or** was exactly the given value before writing.

After adding the extension **SilvercartDataObject** and the method **"getCacheRelevantFields()"** to your target object, use **LastEditedForCache** insead of LastEdited for your cache keys.