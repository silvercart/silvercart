# Integrating SilverCart into an existing Silverstripeinstallation

 I presume that you already have a SilverStripe installation running and you want to integrate SilverCart into that existing project.

1. Download the minimum barebone package of SilverCart from the downloads section. It contains all required modules to run SilverCart. Copy the folders to you installation root.
2. Make changes to the class hirarchy in the file /mysite/Page.php. Page must extend SilvercartPage and Page_Controller must extend SilvercartPage_Controller. Further delete all default CSS requirements in the method init() int the file Page.php.
3. Create a folder 'silverstripe-cache' on your installation root (i.e. the same directory where the folder 'mysite' is in.)
4. Make a change to /mysite/_config.php. Delete the line SSViewer::set_theme('blackcandy'); Define your installation language with Translatable::set_default_locale(“en_US”); and set the parameter (here “en_US”) to your locale, e.g. “de_DE”. Define an admin email address for the store by adding Email::setAdminEmail(“test@example.org”);
5. Run a [my_URL]/dev/build?flush=all.

Your SilverCart installation is completed! Note that your webserver needs at least 64MB memory for the installation process. 