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
		  en.yml
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
	i18n::set_default_locale('en');

## Part 2: Extend or modify an existing translation
- - -

It is easy to extend an existing translation. All you have to do is to add the new translation value to the $lang array.

In case of extending the product detail template with some additional information, perhaps some technical specs for a TV screen, create your en.yml like that:

	:::php
	en:
	  SilverCart\Model\Product\Product:
        SCREEN_SIZE: 'Screen size (in.)'
        DISPLAY_TYPE: 'Display type'
        HD_READY: 'HD Ready'
        HDMI_INPUTS: 'HDMI Inputs'

Your new translation file will be added automatically by calling your projects URL with ”?flush=all” as parameter.

Now you can get the new translation values for your tech-spec labels by calling

	:::php
	<%t SilverCart\Model\Product\Product.SCREEN_SIZE 'Screen size' %>

in your template.
### Modify an existing translation
- - -

To modify an existing translation you can just do the same. 

In our example, we want to replace all tax related translation values “VAT” with “GST” for the locale en_GB. So, we will add one new file for en_GB.

So, lets place the file en_GB.yml to mysite/lang.

	:::php
    en_GB:
	  SilverCart\Model\Product\Product:
        VAT: 'GST'
      SilverCart\Model\Pages\Page:
        INCLUDED_VAT: 'included GST'
		INCLUDING_TAX: 'incl. %s%% GST'
		EXCLUDING_TAX: 'plus GST'
		TAX: 'incl. %d%% GST'

Now, refresh the the cached i18n data by calling your site with "?flush=all".

## Part 3: Provide a non existing translation
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
	fr:
	  SilverCart\Model\Product\Product:
        DAY: 'jour'
        DAYS: 'jours'
        WEEK: 'semaine'
        WEEKS: 'semaines'
        MONTH: 'mois'
        MONTHS: 'mois'
      SilverCart\Dev\Tools:
        DATE: 'Date'
        NO: 'Non'
        YES: 'Oui'
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
