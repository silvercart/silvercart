<?php
/*
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
 */

/**
 * page type to display search results
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartSearchResultsPage extends Page {

    public static $singular_name = "";
    public static $allowed_children = array(
        'none'
    );
}

/**
 * correlating controller
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 * @since 23.10.2010
 */
class SilvercartSearchResultsPage_Controller extends Page_Controller {

    protected $searchResultProducts;

    /**
     * Diese Funktion wird beim Initialisieren ausgeführt
     *
     * @return <type>
     *
     * @author Oliver Scheer <oscheer@pixeltricks.de>
     * @since 11.11.2010
     */
    public function init() {
        parent::init();
        $var = Convert::raw2sql(Session::get('searchQuery')); // input data must be secured
        $whereClause = sprintf("`Title` LIKE '%%%s%%' OR `ShortDescription` LIKE '%%%s%%' OR `LongDescription` LIKE '%%%s%%' OR `MetaKeywords` LIKE '%%%s%%'", $var,$var,$var,$var);
        if (!isset($_GET['start']) || !is_numeric($_GET['start']) || (int) $_GET['start'] < 1) {
            $_GET['start'] = 0;
        }
        $SQL_start = (int) $_GET['start'];
        $this->searchResultProducts = SilvercartProduct::get( $whereClause, null, null, sprintf("%s,15", $SQL_start));

        $productIdx = 0;
        if ($this->searchResultProducts) {
            $productAddCartForm = $this->getCartFormName();
            foreach ($this->searchResultProducts as $product) {
                $this->registerCustomHtmlForm('ProductAddCartForm'.$productIdx, new $productAddCartForm($this, array('productID' => $product->ID)));
                $product->productAddCartForm = $this->InsertCustomHtmlForm(
                    'ProductAddCartForm' . $productIdx,
                    array(
                        $product
                    )
                );
                $productIdx++;
            }
        }
    }

    /**
     * Returns the products that match the search result in any kind
     *
     * @return DataObjectSet|false the resulting products of the search query
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getProducts() {
        return $this->searchResultProducts;
    }

    /**
     * returns the search query out of the session for the template.
     *
     * @return String the search query saved in the session
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.11.10
     */
    public function getSearchQuery() {
        return stripslashes(Session::get('searchQuery'));
    }
}