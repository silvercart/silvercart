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
* ModelAdmin for SilvercartImage.
* 
* @package Silvercart
* @subpackage ModelAdmins
* @author Sascha Koehler <skoehler@pixeltricks.de>
* @copyright 2012 pixeltricks GmbH
* @since 31.05.2012
* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
*/
class SilvercartImageAdmin extends ModelAdmin {

    /**
    * The code of the menu under which this admin should be shown.
    * 
    * @var string
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public static $menuCode = 'config';

    /**
    * The section of the menu under which this admin should be grouped.
    * 
    * @var string
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public static $menuSection = 'others';

    /**
    * The section of the menu under which this admin should be grouped.
    * 
    * @var string
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public static $menuSortIndex = 120;

    /**
    * The URL segment
    *
    * @var string
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public static $url_segment = 'silvercart-silvercart-image';

    /**
    * The menu title
    *
    * @var string
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public static $menu_title = 'Silvercart Images';

    /**
    * Managed models
    *
    * @var array
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public static $managed_models = array(
        'SilvercartImage' => array(
            'collection_controller' => 'SilvercartImageAdmin_CollectionController'
        )
    );

    /**
    * Constructor
    *
    * @return void
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public function __construct() {
        self::$menu_title = _t('SilvercartImage.PLURALNAME');
        parent::__construct();
    }

    /**
    * Provides hook for decorators, so that they can overwrite css
    * and other definitions.
    * 
    * @return void
    *
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public function init() {
        parent::init();
        $this->extend('updateInit');
    }
}

/**
* ModelAdmin for SilvercartImage.
* 
* @package Silvercart
* @subpackage ModelAdmins
* @author Sascha Koehler <skoehler@pixeltricks.de>
* @copyright 2012 pixeltricks GmbH
* @since 31.05.2012
* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
*/
class SilvercartImageAdmin_CollectionController extends ModelAdmin_CollectionController {

    /**
    * Hide the import form
    *
    * @var boolean
    * 
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public $showImportForm = false;

    /**
    * Adjusts the search form fields.
    *
    * @return Form
    * 
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public function SearchForm() {
        $form = parent::SearchForm();

        if (!array_key_exists('ImageType', $_REQUEST)) {
            $_REQUEST['ImageType'] = 'otherImages';
        }

        $imageTypeField = new OptionsetField(
            'ImageType',
            _t('SilvercartImageAdmin.SELECT_IMAGE_TYPE'),
            array(
                'productImages'       => _t('SilvercartImageAdmin.SELECT_PRODUCT_IMAGES'),
                'paymentMethodImages' => _t('SilvercartImageAdmin.SELECT_PAYMENTMETHOD_IMAGES'),
                'otherImages'         => _t('SilvercartImageAdmin.SELECT_OTHER_IMAGES'),
            ),
            $_REQUEST['ImageType']
        );

        $form->Fields()->push($imageTypeField);

        return $form;
    }

    /**
    * Modifies the search query for image types.
    *
    * @param array $searchCriteria The search criteria
    *
    * @return SQLQuery
    * 
    * @author Sascha Koehler <skoehler@pixeltricks.de>
    * @since 31.05.2012
    */
    public function getSearchQuery($searchCriteria) {
        $query = parent::getSearchQuery($searchCriteria);

        if (!array_key_exists('ImageType', $_REQUEST)) {
            $imageType = 'otherImages';
        } else {
            $imageType = $_REQUEST['ImageType'];
        }

        switch ($imageType) {
            case 'productImages':
                $query->where[] = 'SilvercartProductID > 0';
                break;
            case 'paymentMethodImages':
                $query->where[] = 'SilvercartPaymentMethodID > 0';
                break;
            default:
                $query->where[] = 'SilvercartProductID = 0 AND SilvercartPaymentMethodID = 0';
        }

        return $query;
    }
}