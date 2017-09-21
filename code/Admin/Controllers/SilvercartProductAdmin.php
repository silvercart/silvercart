<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for SilvercartProducts.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 16.01.2012
 * @license see license file in modules root directory
 */
class SilvercartProductAdmin extends SilvercartModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'products';
    
    public static $tree_class = 'SilvercartProduct';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 10;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-products';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Products';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartProduct'
    );

    /**
     * Definition of the Importers for the managed model.
     *
     * @var array
     */
    //public static $model_importers = array(
    //    'SilvercartProduct' => 'SilvercartProductCsvBulkLoader',
    //);
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @param bool $skipUpdateInit Set to true to skip the parents updateInit extension
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.03.2013
     */
    public function init($skipUpdateInit = false) {
        /*
         * we tweeked the backend behavior so that when you press the button 'add' on the
         * tab files the file object will be created. This was neccessary because we
         * want to use the fileiframefield without saving. If this created file
         * is not saved by the user it must be deleted because it is an empty entry.
         */
        $request = $this->getRequest();
        $postVars = $request->postVars();
        if (array_key_exists('update', $postVars)) {
            $productID      = $postVars['update'];
            $currentProduct = DataObject::get_by_id('SilvercartProduct', $productID);

            if ($currentProduct) {
                if ($currentProduct->SilvercartFiles()) {
                    foreach ($currentProduct->SilvercartFiles() as $file) {
                        if ($file->isEmptyObject()) {
                            $file->delete();
                        }
                    }
                }
                if ($currentProduct->SilvercartImages()) {
                    foreach ($currentProduct->SilvercartImages() as $image) {
                        if ($image->isEmptyObject()) {
                            $image->delete();
                        }
                    }
                }
            }
        }
        
        parent::init($skipUpdateInit);
    }
    
    /**
     * Builds and returns the edit form.
     * Add the component SilvercartGridFieldDuplicateAction to the GridField.
     * 
	 * @param int       $id     The current records ID. Won't be used for ModelAdmins.
	 * @param FieldList $fields Fields to use. Won't be used for ModelAdmins.
     * 
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);
        $this->getGridFieldConfig($form)->addComponent(new SilvercartGridFieldDuplicateAction());
        return $form;
    }
}



