<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @subpackage Pages
 */

/**
 * show an process a contact form
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartContactFormPage extends SilvercartMetaNavigationHolder {
    
    public static $allowed_children = array(
        'SilvercartContactFormResponsePage'
    );
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/images/page_icons/metanavigation_page";
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }

}

/**
 * Controller of this page type
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartContactFormPage_Controller extends SilvercartMetaNavigationHolder_Controller {
    
    public static $allowed_actions = array(
        'productQuestion',
    );

    /**
     * initialisation of the form object
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 21.10.2010
     * @return void
     */
    public function init() {
        $this->registerCustomHtmlForm('SilvercartContactForm', new SilvercartContactForm($this));
        parent::init();
    }
    
    /**
     * Fills the contact form with a predefined product question text and renders the template
     *
     * @param SS_HTTPRequest $request HTTP request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 31.05.2012
     */
    public function productQuestion(SS_HTTPRequest $request) {
        $params = $request->allParams();
        if (!empty($params['ID']) &&
            is_numeric($params['ID'])) {
            $product = DataObject::get_by_id('SilvercartProduct', $params['ID']);
            if ($product) {
                $silvercartContactForm = $this->getRegisteredCustomHtmlForm('SilvercartContactForm');
                $silvercartContactForm->setFormFieldValue(
                        'Message',
                        sprintf(
                                _t('SilvercartProduct.PRODUCT_QUESTION'),
                                $product->Title,
                                $product->ProductNumberShop
                        )
                );
            }
        }
        return $this->render();
    }
}
