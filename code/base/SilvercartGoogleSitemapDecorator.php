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
 * @subpackage Base
 */

/**
 * Product detail pages get shown in sitemap.xml
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.06.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartGoogleSitemapDecorator extends DataObjectDecorator {

    /**
     * Add product detail pages to the sitemap.xml
     *
     * @param DataObjectSet $newPages the Page objects to be parsed for the sitemap.xml
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 3.6.2011
     * @return DataObjectSet DataObjects and Pages to be parsed for the sitemap.xml
     */
    public function updateItems($newPages) {
        $products = SilvercartProduct::get();
        if ($products) {
            foreach ($products as $product) {
                $product->AbsoluteLink = Director::protocolAndHost() . $product->Link();
                $product->ChangeFreq = "monthly";
                $product->Priority = "0.6";
                $newPages->push($product);
            }
        }
        return $newPages;
    }

}

