# Integrating SilverCart into an existing Silverstripe installation

 I presume that you already have a SilverStripe installation running and you want to integrate SilverCart into that existing project.

1. Download the minimum barebone package of SilverCart from the downloads section. It contains all required modules to run SilverCart. Copy the folders to you installation root.
2. Make changes to the class hirarchy in the file /mysite/Page.php. Page must extend SilverCart\Model\Pages\Page\Page and PageController must extend SilverCart\Model\Pages\Page\PageController.
3. Run a [my_URL]/dev/build?flush=all.

Your SilverCart installation is completed! Note that your webserver needs at least 64MB memory for the installation process. 