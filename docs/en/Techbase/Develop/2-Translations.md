# Translations

## Part 1: Learn the locations and where to put your files
- - -
### Choose your translation scope

There are three different ways you can go to implement custom translations:

1. Extend an existing translation
2. Modify an existing translation
3. Provide a non existing translation


### Where to put your custom translation files

For us, the best practice to deal with custom translation files is to put them into the projects lang directory (default would be /mysite/lang).

We do not recommend to change any file of SilverCart directly to keep updatable without doing any merges.

This tutorial describes all mentioned translation scopes, using the example translation file for British English. All translation files are set and named by locale, so our filename is en_GB.php. Note that our fall back language is American English (SilverStripe default).

After adding your custom translation file, your silverstripe directory structure should look like this:

	+ assets
	+ cms
	+ customhtmlform
	+ dataobject_manager
	+ googlesitemaps
	- mysite
	  + code
	  - lang
		  en_GB.php
	  _config.php
	+ sapphire
	+ silvercart
	+ silvercart_payment_paypal
	+ silvercart_payment_prepayment
	+ themes
	+ uploadify

Now it's time to set the default locale in your /mysite/_config.php:

	:::php
	i18n::enable();
	i18n::set_default_locale('en_GB');

## Part 2: Extend an existing translation
- - -

It is easy to extend an existing translation. All you have to do is to add the new translation value to the $lang array.

In case of extending the product detail template with some additional information, perhaps some technical specs for a TV screen, create your en_GB.php like that:

	:::php
	i18n::include_locale_file('silvercart', 'en_US');
	
	global $lang;
	
	if (array_key_exists('en_GB', $lang) && is_array($lang['en_GB'])) {
		$lang['en_GB'] = array_merge($lang['en_US'], $lang['en_GB']);
	} else {
		$lang['en_GB'] = $lang['en_US'];
	}
	
	$lang['en_GB']['SilvercartProduct']['SCREEN_SIZE'] = 'Screen size (in.)';
	$lang['en_GB']['SilvercartProduct']['DISPLAY_TYPE'] = 'Display type';
	$lang['en_GB']['SilvercartProduct']['HD_READY'] = 'HD Ready';
	$lang['en_GB']['SilvercartProduct']['HDMI_INPUTS'] = 'HDMI Inputs';

Your new translation file will be added automatically by calling your projects URL with ”?flush=all” as parameter.

Now you can get the new translation values for your tech-spec labels by calling

	:::php
	<% _t('SilvercartProduct.SCREEN_SIZE') %>

in your template.
## Part 3: Modify an existing translation
- - -

To modify an existing translation we decided to use i18n plugins as best practice. So, you have to provide a function which is our plugin processor and register it to SilverStripe's i18n.

All registered i18n plugins will be called after SilverStripe got all relevant translation files and merges the default $lang array with the return value of your plugin's function. In this case, we don't want to merge two arrays, we want to overwrite existing values, what can be done by an i18n plugin, too.

In our example, we want to replace all tax related translation values “VAT” with “GST” for the locale en_GB. So, we will add one functional plugin for en_GB and two empty plugins for de_DE and en_US. We don't really need de_DE and en_US, but lets add them just to show you how to handle multiple i18n plugins.

First, lets add a class MyI18nPlugin to store our locale dependant plugin methods. Every plugin is a static method of MyI18nPlugin.

	:::php
	<?php
	
	/**
	 * My I18n plugin.
	 *
	 * @author Sebastian Diel <sdiel@pixeltricks.de>
	 * @since 15.01.2013
	 */
	class MyI18nPlugin {
	
		/**
		 * Plugin for de_DE
		 *
		 * @return void
		 *
		 * @author Sebastian Diel <sdiel@pixeltricks.de>
		 * @since 15.01.2013
		 * 
		 * @global array $lang 
		 */
		public static function de_DE() {
			global $lang;
		}
		
		/**
		 * Plugin for en_GB
		 *
		 * @return void
		 *
		 * @author Sebastian Diel <sdiel@pixeltricks.de>
		 * @since 15.01.2013
		 * 
		 * @global array $lang 
		 */
		public static function en_GB() {
			global $lang;
			$lang['en_GB']['SilvercartProduct']['VAT'] = 'GST';
			$lang['en_GB']['SilvercartPage']['INCLUDED_VAT'] = 'included GST';
			$lang['en_GB']['SilvercartPage']['INCLUDING_TAX'] = 'incl. %s%% GST';
			$lang['en_GB']['SilvercartPage']['EXCLUDING_TAX'] = 'plus GST';
			$lang['en_GB']['SilvercartPage']['TAX'] = 'incl. %d%% GST';
		}
		
		/**
		 * Plugin for en_US
		 *
		 * @return void
		 *
		 * @author Sebastian Diel <sdiel@pixeltricks.de>
		 * @since 15.01.2013
		 * 
		 * @global array $lang 
		 */
		public static function en_US() {
			global $lang;
		}
	
	}

Then, add the following code to register your plugins to your _config.php:

	:::php
	i18n::register_plugin('MyI18nPlugin_de_DE', array('MyI18nPlugin', 'de_DE'));
	i18n::register_plugin('MyI18nPlugin_en_GB', array('MyI18nPlugin', 'en_GB'));
	i18n::register_plugin('MyI18nPlugin_en_US', array('MyI18nPlugin', 'en_US'));


Your translation modifications will be added automatically by calling your projects URL with ”?flush=all” as parameter.

## Part 4: Provide a non existing translation
- - -

To provide a non existing translation, just add the relevant file to your /mysite/lang directory and put in the content of silvercarts default translation file en_US.php.

Let's do it by using the example language French. The correct locale to use is fr_FR. So, our filename is fr_FR.php.

	+ assets
	+ cms
	+ customhtmlform
	+ dataobject_manager
	+ googlesitemaps
	- mysite
	  + code
	  - lang
		  fr_FR.php
	  _config.php
	+ sapphire
	+ silvercart
	+ silvercart_payment_paypal
	+ silvercart_payment_prepayment
	+ themes
	+ uploadify

Now replace all initial array keys 'en_US' with 'fr_FR'. After that, you should add SilverStripe's default mechanism to get the fall back values of non existing french values. The custom translation file should look like that:

	:::php
	i18n::include_locale_file('silvercart', 'en_US');
	
	global $lang;
	
	if (array_key_exists('fr_FR', $lang) && is_array($lang['fr_FR'])) {
		$lang['fr_FR'] = array_merge($lang['en_US'], $lang['fr_FR']);
	} else {
		$lang['fr_FR'] = $lang['en_US'];
	}
	
	$lang['fr_FR']['Silvercart']['DATE'] = 'Date';
	$lang['fr_FR']['Silvercart']['DAY'] = 'jour';
	$lang['fr_FR']['Silvercart']['DAYS'] = 'jours';
	$lang['fr_FR']['Silvercart']['WEEK'] = 'semaine';
	$lang['fr_FR']['Silvercart']['WEEKS'] = 'semaines';
	$lang['fr_FR']['Silvercart']['MONTH'] = 'mois';
	$lang['fr_FR']['Silvercart']['MONTHS'] = 'mois';
	$lang['fr_FR']['Silvercart']['YES'] = 'Oui';
	$lang['fr_FR']['Silvercart']['NO'] = 'Non';
	... 

Now, set the default locale in your /mysite/_config.php to fr_FR:
	:::php
	i18n::enable();
	i18n::set_default_locale('fr_FR');

After calling your projects URL with ”?flush=all” as parameter, our new translation is available for SilverCart.
## Part 5: Contributing
- - -

By adding new translation files or correcting wrong values you can contribute to SilverCart's default code base and help SilverCart growing to a more and more international ecommerce system. Please contact us and share your changes with us.

To contribute just inform us by sending an email to contribute [at] silvercart.org.
## Part 6: Fini
- - -

In this tutorial you learned how to set up new translation values and how to overwrite existing ones.

If you feel that something should be described in more detail, found errors or have questions head over to our forum and drop us a line.

[Visit the forum](http://www.silvercart.org/forum/)
