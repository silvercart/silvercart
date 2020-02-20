# SilverCart
SilverCart E-Commerce module for SilverStripe CMS Framework

## Maintainer Contact

* Sebastian Diel <sdiel@pixeltricks.de>
* Ramon Kupper <rkupper@pixeltricks.de>

## Requirements
* SilverStripe CMS 4.1+
* SilverStripe Fluent 4.0+
* SilverStripe Widgets 2.0+
* SilverCart WidgetSets 4.1+

## Basic installation from scratch with composer
1. composer create-project silverstripe/installer silvercart-demo
2. cd silvercart_demo 
3. composer require silvercart/silvercart 4.1.2
4. change the class definition in mysite/code/Page.php to
   ```class Page extends \SilverCart\Model\Pages\Page```
5. change the class definition in mysite/code/PageController.php to
   ```class PageController extends \SilverCart\Model\Pages\PageController```
6. open website in your browser and finish SilverStripe installation process with: 
   *Theme selection: empty theme* (this is the only necessary setting for SilverCart) 
7. Open CMS backend via yourdomain.tld/admin
8. Settings -> Email Settings:
   enter valid Email sender 
9. Settings -> Add Example data:
   Add Example Data (this might take a few minutes!) 
   Add Example Configuration

## Summary
SilverCart is an Open Source E-Commerce module for the CMS Framework SilverStripe.

For more information about the SilverCart visit http://www.silvercart.org/about/

## License
See LICENSE

## Support
Please visit our website and contact us to learn more about our support options.  
http://www.silvercart.org/
