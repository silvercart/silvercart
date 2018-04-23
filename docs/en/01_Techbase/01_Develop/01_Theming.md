# Theming

## Part 1: Learn the locations and where to put your files
- - -

### Choose your theming scope

There are three different ways you can go when it comes to theming in SilverCart:

1. Use the standard templates (aka leave them as they are) and just overwrite the CSS (Cascading Stylesheet) definitions
2. Overwrite the templates (.ss files) and use your own CSS definitions
3. Create whole new page types (.php files) and corresponding templates and their CSS files

This tutorial describes ways number one and two. We'll create our own templates and write our own CSS files.

### Where to put your custom theme

You have two choices where you can put your theme. It doesn't matter which one you take, the following chapters will work with both.

At pixeltricks we prefer method two (putting the theme into the project directory) since we have all parts of the project in one place this way.

Method two will also work better when you don't have a dedicated theme already, since SilverCart will not look good with a plain blackcandy theme.

#### 1) Use the standard SilverStripe theming convention

When using the standard SilverStripe theming convention the theme is put into the “themes” directory.

This tutorial assumes that you have configured the theme “blackcandy” in the file “mysite/_config.php”:

	:::php
	SSViewer::set_theme('blackcandy');

So you have to create some additional directories where the template files will be put. SilverCart uses the standard Silverstripe directory structure for themes, so we create the following folders in the folder “themes”: “blackcandy_silvercart”, blackcandy_silvercart/templates”, “blackcandy_silvercart/templates/Includes” and “blackcandy_silvercart/templates/Layout”.

Your silverstripe directory structure should look like this now:

	+ assets
	+ cms
	+ customhtmlform
	+ dataobject_manager
	+ googlesitemaps
	+ mysite
	+ sapphire
	+ silvercart
	+ silvercart_payment_paypal
	+ silvercart_payment_prepayment
	- themes
		+ blackcandy
		+ blackcandy_blog
		- blackcandy_silvercart
		  - templates
			  - Includes
			  - Layout
	+ uploadify


#### 2) Put your theme into your project directory

The project directory is defined in the file “mysite/_config.php”:

	:::php
	$project = "mysite";

In this tutorial we're going to use the standard project “mysite”. If you already have an installation with another project defined just replace “mysite” with the name of your project while reading.

First you'll have to create some additional directories where the template files will be put. SilverCart uses the standard Silverstripe directory structure for themes, so we create the following folders in “mysite”: “templates”, “templates/Includes” and “templates/Layout”.

Your silverstripe directory structure should look like this now:

	+ assets
	+ cms
	+ customhtmlform
	+ dataobject_manager
	+ googlesitemaps
	- mysite
	  + code
	  + javascript
	  - templates
		  - Includes
		  - Layout
	  _config.php
	+ sapphire
	+ silvercart
	+ silvercart_payment_paypal
	+ silvercart_payment_prepayment
	+ themes
	+ uploadify

## Part 2: Know the dimensions
- - -

### Theming by sections

Since we want to overwrite the existing templates we have to know which pages the storefront comprises.

## Part 3: Write your own templates
- - -

### Let's start writing our own templates

By now we know where to put our custom template files and what templates we need to change.

We won't describe the process for all above mentioned templates since it's a repetitive task. If you know how to change one you know how to change all.

So let's begin with changing the productgrouppage list view. If you have created example data (see Techbase: Setting up SilverCart, chapter three) that's the page type you get to see when clicking on “TestProductGroup1” or “TestProductGroup2” etc.

In chapter one we created some directories “templates” and “templates/Layout” (their location depends wether you chose method number one or two). So let's create a new file “ProductGroupPageList.ss” in the directory “templates/SilverCart/View/GroupView”:

	- templates
	  + Includes
	  - SilverCart
        - View
          - GroupView
            ProductGroupPageList.ss

First thing to do after the creation of this file is to update Silverstripe by calling the productgroup page with a ”?flush=all” (this is needed so that the manifest builder knows there are new files).

Now you can change the HTML code of the template file as you feel inclined. Silverstripe will use your new template file instead of the standard SilverCart one. You don't have to rewrite all templates since for every omitted template the corresponding SilverCart standard template will be used.

## Part 4: Write your own CSS files
- - -

### How are CSS files organised?

SilverCart CSS files are splitted into bundles ordered by sections.

The standard CSS files are located in directory “silvercart/css/screen/”. The most basic definitions can be found in the files “content.css” for styling and “basemod.css” for layout definitions.

The section files are located in directory “silvercart/css/screen/custom/”.

Following is an overview of all available CSS files:

	- silvercart
	  - css
		  - screen
			  content.css
			  basemod.css
			  - custom
				  SilvercartCheckout.css
				  SilvercartGeneral.css
				  SilvercartProductGroupHolder.css
				  SilvercartProductGroupPage.css
				  SilvercartProductPage.css
				  SilvercartShoppingCartFull.css

### Write your own CSS files

First you have to create the directory structure where your own CSS files will be placed. Create a new folder structure with the directories “css”, “css/screen”, “css/screen/custom” on the same level where your template directory is located:

	- css
	  - screen
		  - custom
	- templates
	  - Includes
	  - Layout


Depending on where you put your theme this structure will either be located unter “mysite/” or “themes/blackcandy_silvercart/”.

For this tutorial we'll describe the way for the productgrouppage CSS file. Since the procedure is the same for all other files you can just adopt it accordingly.

Create the file “SilvercartproductGroupPage.css” in directory “css/screen/custom/” (best practice is to copy the contents of the standard file so you can change the definitions step by step). Now update Silverstripe by calling the productgroup page with a ”?flush=all” (this is needed so that the manifest builder knows there are new files).

SilverCart will load your custom CSS file now instead of the standard file. For all files that you haven't replaced the standard versions will be used.

Feel free to style the content to you likings by altering the definitions in the newly created file. Fini

In this tutorial you learned how template and CSS files are organised in SilverCart and how you can replace them with your own files. Additionally we looked at a comprehensive overview of all templates ordered by section that you can take as a checklist when theming your own SilverCart webshop.

If you feel that something should be described in more detail, found errors or have questions head over to our forum and drop us a line.

[Visit the forum](http://www.silvercart.org/forum/?url=/forum)
