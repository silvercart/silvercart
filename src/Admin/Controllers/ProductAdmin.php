<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\ModelAdmin;
use SilverCart\Admin\Forms\GridField\GridFieldDuplicateAction;
use SilverCart\Model\Product\Product;
use SilverStripe\Forms\Form;

/**
 * ModelAdmin for Products.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ProductAdmin extends ModelAdmin
{
    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'products';
    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 10;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-products';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Products';
    /**
     * Managed models
     *
     * @var array
     */
    private static $managed_models = [
        Product::class,
    ];
    /**
     * A subclass of {@link DataObject}.
     *
     * Determines what is managed in this interface, through
     * {@link getEditForm()} and other logic.
     *
     * @var string
     */
    private static $tree_class = Product::class;
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.09.2018
     */
    protected function init() : void
    {
        /*
         * we tweeked the backend behavior so that when you press the button 'add' on the
         * tab files the file object will be created. This was neccessary because we
         * want to use the fileiframefield without saving. If this created file
         * is not saved by the user it must be deleted because it is an empty entry.
         */
        $request  = $this->getRequest();
        $postVars = $request->postVars();
        if (array_key_exists('update', $postVars)) {
            $productID      = $postVars['update'];
            $currentProduct = Product::get()->byID($productID);

            if ($currentProduct) {
                if ($currentProduct->Files()) {
                    foreach ($currentProduct->Files() as $file) {
                        if ($file->isEmptyObject()) {
                            $file->delete();
                        }
                    }
                }
                if ($currentProduct->Images()) {
                    foreach ($currentProduct->Images() as $image) {
                        if ($image->isEmptyObject()) {
                            $image->delete();
                        }
                    }
                }
            }
        }
        
        parent::init();
    }
    
    /**
     * Builds and returns the edit form.
     * Add the component GridFieldDuplicateAction to the GridField.
     * 
     * @param int       $id     The current records ID. Won't be used for ModelAdmins.
     * @param FieldList $fields Fields to use. Won't be used for ModelAdmins.
     * 
     * @return \SilverStripe\Forms\Form
     */
    public function getEditForm($id = null, $fields = null) : Form
    {
        $this->beforeUpdateEditForm(function(Form $form) {
            $this->getGridFieldConfigFor($form)->addComponent(new GridFieldDuplicateAction());
        });
        return parent::getEditForm($id, $fields);
    }
}