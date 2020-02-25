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
2. cd silvercart-demo 
3. composer require silvercart/silvercart 4.1.4
4. change the class definition in app/src/Page.php to
   ```class Page extends \SilverCart\Model\Pages\Page```
5. change the class definition in app/src/PageController.php to
   ```class PageController extends \SilverCart\Model\Pages\PageController```
6. composer vendor-expose
7. Set up the database configuration using the .env file (see .env.example)
8. Add the default admin credentials and environment type to your .env configuration
    * SS_DEFAULT_ADMIN_USERNAME="YOUR-ADMIN-USER-NAME"
    * SS_DEFAULT_ADMIN_PASSWORD="YOUR-ADMIN-USER-PASSWORD"
    * SS_ENVIRONMENT_TYPE="dev"
9. Build the database using either
    * sake dev/build (via CLI if sake is installed)
    * php vendor/silverstripe/framework/cli-script.php dev/build (via CLI if sake is not installed)
    * yourdomain.tld/dev/build (in your browser)
10. Open the CMS admin area by running yourdomain.tld/admin in your browser
    * Configuration > Settings > Email Settings
        * enter a valid Email sender name.
        * enter a valid Email sender address.
        * Save the settings.
    * Configuration > Settings > Add Example data
        * Add Example Data (this might take a while!) 
        * Add Example Configuration

## Installing Twitter Bootstrap 4 based theme
1. cd themes
2. wget https://github.com/silvercart/theme-sc-bootstrap4/archive/master.zip
3. unzip master.zip
4. rm master.zip
5. mv theme-sc-bootstrap4-master sc-bootstrap4
7. Add the theme configuration to app/_config/theme.yml
8. cd ..
9. composer vendor-expose
10. run yourdomain.tld/?flush=1 in your browser.

### YAML theme configuration:
Place this configuration into your app/_config/theme.yml after installing the SilverCart theme sc-bootstrap4.

```
---
Name: mytheme
---
SilverStripe\View\SSViewer:
  themes:
    - sc-bootstrap4
    - '$public'
    - '$default'
```

## Summary
SilverCart is an Open Source E-Commerce module for the CMS Framework SilverStripe.

For more information about the SilverCart visit http://www.silvercart.org/about/

## License
See LICENSE

## Support
Please visit our website and contact us to learn more about our support options.  
http://www.silvercart.org/
