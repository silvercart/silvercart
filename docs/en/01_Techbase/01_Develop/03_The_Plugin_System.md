# The plugin system
## Why don't we just use decorators?
- - -
The plugin system has some advantages to just using decorators:

1. All pluggable methods are put together in one file (the plugin provider) that can easily be found via the naming convention, e.g. for “Order” the plugin provider is called “OrderPluginProvider”.
2. Otherwise you would have to check the source code of Order and find out, what methods are extendable.
3. We can gather common methods for all plugins in the base class “Plugin”.
4. When multiple plugins are decorated into one plugin provider method their output is merged automatically.
5. The gates are open for later developments like priorities when multiple plugins are decorated into one plugin provider method



   
As long as you don't want to write your own plugin providers the mechanism for decorating is exactly the one you know from Silverstripe: use a DataObjectDecorator object, add it as extension and provide the methods you want to decorate. You can find more information on this and on writing your own plugin providers in the following paragraphs.
## The structure of the SilverCart plugin system
- - -
The most basic object for plugins is the class “Plugin”. It provides methods that can and should be used by all plugins.

This basic object is extended by specialized objects that are plugin-providers for SilverCart classes. Among those are e.g. “OrderPlugin”, “ShoppingCartPlugin”, etc. The plugin-provider objects can be decorated with your own specialized plugins. They expose all possible hooks that you can implemented to provide additional functionality and outputs to the original SilverCart classes.

![](_images/silvercart_plugin_system_overview.jpg)

## The connection between SilverCart classes and plugin providers
- - -

![](_images/silvercart_plugin_system_connection.jpg)

## Example for writing  a plugin
- - -
The following example demonstrates the use of plugins by the SilverCart DHL shipping module. Create the plugin file:

	:::php
    namespace MyNameSpace\Model\Plugins;
    use SilverStripe\Core\Extension;
	class ShipmentDhlOrderPlugin extends Extension {
		public function pluginOrderDetailInformation(&$arguments) {
			return "OK";
		}
	}

Register the plugin (preferrably in your “_config.php” file):

	:::php
    \SilverCart\Model\Plugins\Providers\OrderPluginProvider::add_extension(\MyNameSpace\Model\Plugins\ShipmentDhlOrderPlugin::class);

## Available plugin providers
- - -
Currently the following plug-in providers are available; on the second level all available plugin methods are listed.

 * OrderPluginProvider
 * pluginInit
 * pluginCreateFromShoppingCart
 * pluginOrderDetailInformation

## Creating a custom plugin provider
- - -
### Create a new plugin provider class

	:::php
    namespace MyNameSpace\Model\Plugins;
    use SilverCart\Model\Plugins\Plugin;
	class MyOrderPluginProvider extends Plugin {
		public function TestMethod(&$arguments) {
			$result = $this->extend('pluginTestMethod', $arguments);
			
			return $this->returnExtensionResultAsString($result);
		}
	}

### Register the new plugin provider class

	:::php
    use MyNameSpace\Model\Plugins\MyOrderPluginProvider;
    use SilverCart\Model\Order\Order;
    use SilverCart\Model\Plugins\Plugin;
	Plugin::registerPluginProvider(Order::class, MyOrderPluginProvider::class);


### Implement the new methods from the provider in your plugin

	:::php
	<?php
    namespace MyNameSpace\Model\Plugins;
    use SilverStripe\Core\Extension;
	class ShipmentDhlOrderPlugin extends Extension {
		public function pluginTestMethod(&$arguments) {
			return "It works!";
		}
	}


### Calling the new plugin methods from a template

	:::php
	$Plugin(TestMethod)

### Enable plugin functionality in an object

Just add the following line to your “_config.php” file:

	:::php
	<?php
    use SilverCart\Model\Plugins\PluginObjectExtension;
	{OBJECT_FQN}::add_extension(PluginObjectExtension::class);

This is needed so that calls from templates to the method “Plugin” are possible.

We use this mechanism ourselves for the core objects, e.g. the “Order” object is added in SilverCart's “_config.php”:

	:::php
    use SilverCart\Model\Order\Order;
    use SilverCart\Model\Plugins\PluginObjectExtension;
	Order::add_extension(PluginObjectExtension::class);
