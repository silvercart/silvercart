# Backend

## How to make a ModelAdmin sortable?
- - -

First, you need a DB field to sort your records by. Let's create an object **MyObject** with the DB field **Priority** to sort by.

	:::php
	<?php
	
	/**
	 * Documentation of MyObject.
	 */
	class MyObject extends DataObject {
	
		/**
		 * DB attributes
		 *
		 * @var array
		 */
		public static $db = array(
			'Title'		=> 'VarChar(64)',
			'Priority'	=> 'Int',
		);
		
		/**
		 * Default sort field and direction
		 *
		 * @var string
		 */
		public static $default_sort = "Priority DESC";
		
	}

Now, your ModelAdmin should extend **SilvercartModelAdmin**. The class **SilvercartModelAdmin** provides a public static property **$sortable_field** which is the name of the DB field to sort your records by.

	:::php
	<?php
	
	/**
	 * Documentation of MyObjectAdmin.
	 */
	class MyObjectAdmin extends SilvercartModelAdmin {
	
		/**
		 * Name of DB field to make records sortable by.
		 *
		 * @var string
		 */
		public static $sortable_field = 'Priority';
		
		/**
		 * The code of the menu under which this admin should be shown.
		 * 
		 * @var string
		 */
		public static $menuCode = 'default';
		
		/**
		 * The section of the menu under which this admin should be grouped.
		 * 
		 * @var string
		 */
		public static $menuSortIndex = 50;
		
		/**
		 * The URL segment
		 *
		 * @var string
		 */
		public static $url_segment = 'my-object';
		
		/**
		 * The menu title
		 *
		 * @var string
		 */
		public static $menu_title = 'My Objects';
		
		/**
		 * Managed models
		 *
		 * @var array
		 */
		public static $managed_models = array(
			'MyObject',
		);
		
	}

Run a dev/build and the result is a sortable GridField.