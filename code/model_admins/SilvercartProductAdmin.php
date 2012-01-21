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
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.01.2012
     */
    public static $menuCode = 'products';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.01.2012
     */
    public static $menuSortIndex = 10;

    /**
     * The URL segment
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public static $url_segment = 'silvercart-products';

    /**
     * The menu title
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $menu_title = 'Silvercart products';

    /**
     * Managed models
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public static $managed_models = array(
        'SilvercartProduct' => array(
            'collection_controller' => 'SilvercartProduct_CollectionController'
        ),
    );

    /**
     * Definition of the Importers for the managed model.
     *
     * @var array
     *
     * @author Sascha Koehler
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public static $model_importers = array(
        'SilvercartProduct' => 'SilvercartProductCsvBulkLoader'
    );

    /**
     * Constructor
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartProduct.PLURALNAME');
        
        parent::__construct();
    }
    
    /**
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    public function init() {
        parent::init();
        $this->extend('updateInit');
    }

    /**
     * Provides a way to use different result tables for the managed models.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.08.2011
     */
    public function resultsTableClassName() {
        $className = $this->resultsTableClassName;

        if (isset($this->urlParams['Action']) ) {
            if ($this->urlParams['Action'] == 'SilvercartProduct') {
                $className = 'SilvercartProductTableListField';
            }
        }
        
        return $className;
    }
}



