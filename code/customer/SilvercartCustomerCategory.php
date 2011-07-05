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
 * @subpackage Customer
 */

/**
 * abstract for destinguishing customers that may have special prices or vouchers
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartCustomerCategory extends DataObject {
    public static $singular_name = "customer category";
    public static $plural_name = "customer categories";
    public static $db = array(
        'Title' => 'VarChar',
        'Code' => 'VarChar'
    );
    public static $has_many = array(
        'prices' => 'SilvercartPrice',
        'customers' => 'Member'
    );
    public static $summary_fields = array(
        "Title" => 'Name'
    );
    
    /**
     * Defines the objects summary fields
     * 
     * @return array objects summery field definition
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 5.7.2011 
     */
    public function summaryFields() {
        return array_merge(
                parent::summaryFields(),
                array(
                    "Title" => _t('SilvercartProduct.COLUMN_TITLE')
                )
        );
    }

    /**
     * configure backend fields
     *
     * @param mixed $params ???
     * 
     * @return FieldSet all backend fields
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 17.3.2011
     */
    public function  getCMSFields($params = null) {
        $fields = parent::getCMSFields($params);
        $fields->removeByName('Code');
        return $fields;
    }
}
