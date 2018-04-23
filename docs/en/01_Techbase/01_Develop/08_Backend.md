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
		private static $db = array(
			'Title'		=> 'Varchar(64)',
			'Priority'	=> 'Int',
		);
		
		/**
		 * Default sort field and direction
		 *
		 * @var string
		 */
		private static $default_sort = "Priority DESC";
		
	}

Now, your ModelAdmin should extend **SilverCart\Admin\Controllers\ModelAdmin**. The class **SilverCart\Admin\Controllers\ModelAdmin** provides a public static property **$sortable_field** which is the name of the DB field to sort your records by.

	:::php
	<?php
    
    use SilverCart\Admin\Controllers\ModelAdmin;
	
	/**
	 * Documentation of MyObjectAdmin.
	 */
	class MyObjectAdmin extends ModelAdmin {
	
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
		private static $menuCode = 'default';
		
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
		private static $url_segment = 'my-object';
		
		/**
		 * The menu title
		 *
		 * @var string
		 */
		private static $menu_title = 'My Objects';
		
		/**
		 * Managed models
		 *
		 * @var array
		 */
		private static $managed_models = array(
			'MyObject',
		);
		
	}

Run a dev/build and the result is a sortable GridField.

## How to add a Batch Action to a GridField?
- - -

The first step to get batch actions working for a GridField used by a ModelAdmin is to create them.
Let's create a batch action to set a DataObject with a BD property "isActive" to active and one to set the property to not active.

Action to activate:

	:::php
	<?php

    namespace SilverCart\Admin\Forms\GridField;

    use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\ORM\DataObject;
	
	/**
	 * Batch action to mark an DataObject as active.
	 *
     * @package SilverCart
     * @subpackage Admin_Forms_GridField_BatchActions
	 * @author Sebastian Diel <sdiel@pixeltricks.de>
	 * @copyright 2013 pixeltricks GmbH
	 * @since 14.03.2013
	 * @license see license file in modules root directory
	 */
	class GridFieldBatchAction_ActivateDataObject extends GridFieldBatchAction {
	    
	    /**
	     * Handles the action.
	     * 
	     * @param GridField $gridField GridField to handle action for
	     * @param array     $recordIDs Record IDs to handle action for
	     * @param array     $data      Data to handle action for
	     * 
	     * @return void
	     *
	     * @author Sebastian Diel <sdiel@pixeltricks.de>
	     * @since 14.03.2013
	     */
	    public function handle(GridField $gridField, $recordIDs, $data) {
	        foreach ($recordIDs as $recordID) {
	            $record = DataObject::get_by_id($gridField->getModelClass(), $recordID);
	            if ($record->exists()) {
	                $record->isActive = true;
	                $record->write();
	            }
	        }
	    }
	}

Action to deactivate:

	:::php
	<?php

    namespace SilverCart\Admin\Forms\GridField;

    use SilverCart\Admin\Forms\GridField\GridFieldBatchAction;
    use SilverStripe\Forms\GridField\GridField;
    use SilverStripe\ORM\DataObject;
	
	/**
	 * Batch action to mark an DataObject as not active.
	 *
     * @package SilverCart
     * @subpackage Admin_Forms_GridField_BatchActions
	 * @author Sebastian Diel <sdiel@pixeltricks.de>
	 * @copyright 2013 pixeltricks GmbH
	 * @since 14.03.2013
	 * @license see license file in modules root directory
	 */
	class GridFieldBatchAction_DeactivateDataObject extends GridFieldBatchAction {
	    
	    /**
	     * Handles the action.
	     * 
	     * @param GridField $gridField GridField to handle action for
	     * @param array     $recordIDs Record IDs to handle action for
	     * @param array     $data      Data to handle action for
	     * 
	     * @return void
	     *
	     * @author Sebastian Diel <sdiel@pixeltricks.de>
	     * @since 14.03.2013
	     */
	    public function handle(GridField $gridField, $recordIDs, $data) {
	        foreach ($recordIDs as $recordID) {
	            $record = DataObject::get_by_id($gridField->getModelClass(), $recordID);
	            if ($record->exists()) {
	                $record->isActive = false;
	                $record->write();
	            }
	        }
	    }
	}

We can add a human readable action name by adding a "TITLE" property for the batch actions classname into the i18n .yml files.

Snippet for en.yml:

	:::php
	  SilverCart\Admin\Forms\GridField\GridFieldBatchAction_ActivateDataObject:
	    TITLE: "Activate"
	  SilverCart\Admin\Forms\GridField\GridFieldBatchAction_DeactivateDataObject:
	    TITLE: "Deactivate"

To get batch actions working, register them to the managed model.
It is also important that your **"MyModelAdmin extends SilverCart\Admin\Controllers\ModelAdmin"**.

	:::php
    use MyDataObject;
    use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_ActivateDataObject;
    use SilverCart\Admin\Forms\GridField\GridFieldBatchAction_DeactivateDataObject;
    use SilverCart\Admin\Forms\GridField\GridFieldBatchController;
	GridFieldBatchController::addBatchActionFor(MyDataObject::class, GridFieldBatchAction_ActivateDataObject::class);
	GridFieldBatchController::addBatchActionFor(MyDataObject::class, GridFieldBatchAction_DeactivateDataObject::class);

After doing that the work ist done and you can use the batch action on your MyDataObject.

## How to add Quick Access Fields to a GridField?
- - -

The ModelAdmin you want to add Quick Access Fields to its GridField needs to extend SilverCart\Admin\Controllers\ModelAdmin.

	:::php
	<?php
    
    use SilverCart\Admin\Controllers\ModelAdmin;
	
	/**
	 * Documentation of MyObjectAdmin.
	 */
	class MyObjectAdmin extends ModelAdmin {
	
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

The managed model needs to have the method **getQuickAccessFields()** which should return a FieldList or rendered HTML code to display.

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
		private static $db = array(
			'Title'		=> 'Varchar(64)',
			'Priority'	=> 'Int',
		);
	
		private static $has_many = array(
			'HasManyObjects' => 'HasManyObject',
		);
		
		/**
		 * Default sort field and direction
		 *
		 * @var string
		 */
		private static $default_sort = "Priority DESC";
		
		/**
		 * Returns the quick access fields to display in GridField
		 * 
		 * @return FieldSet
		 */
		public function getQuickAccessFields() {
		    $quickAccessFields = new FieldList();
		    $manyManyObjectTable = new \SilverCart\Admin\Forms\TableField(
	                'HasManyObjects__' . $this->ID,
	                $this->fieldLabel('HasManyObjects'),
	                $this->HasManyObjects()
	        );
	        
	        $quickAccessFields->push($orderPositionTable);
	        
	        $this->extend('updateQuickAccessFields', $quickAccessFields);
	        
	        return $quickAccessFields;
	    }
	}

