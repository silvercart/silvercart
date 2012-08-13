<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright 2012 pixeltricks GmbH
 * @since 16.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductAdmin extends ModelAdmin {

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
    public static $menu_title = 'Silvercart products';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartProduct' => array(
            'title' => 'SilvercartProduct'
            /*
            'collection_controller' => 'SilvercartProduct_CollectionController',
            'record_controller'     => 'SilvercartProduct_RecordController',
            */
        ),
    );

    /**
     * Definition of the Importers for the managed model.
     *
     * @var array
     */
    public static $model_importers = array(
        /*'SilvercartProduct' => 'SilvercartProductCsvBulkLoader'*/
    );
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 01.08.2011
     */
    public function init() {
        /*
         * we tweeked the backend behavior so that when you press the button 'add' on the
         * tab files the file object will be created. This was neccessary because we
         * want to use the fileiframefield without saving. If this created file
         * is not saved by the user it must be deleted because it is an empty entry.
         */
        $request = $this->getRequest();
        $postVars = $request->postVars();
        if (array_key_exists('update', $postVars)) {
            $productID = $postVars['update'];
            $currentProduct = DataObject::get_by_id('SilvercartProduct', $productID);
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
        
        parent::init();
        $this->extend('updateInit');
    }
    
    /**
     * title in the upper bar of the CMS
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 05.08.2012
     */
    public function SectionTitle() {
        return _t('SilvercartProduct.PLURALNAME');
    }
}



