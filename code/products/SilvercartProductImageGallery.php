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
 * An product has one image gallery
 *
 * @package Silvercart
 * @subpackage Products
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 23.10.2010
 * @copyright Pixeltricks GmbH
 */
class SilvercartProductImageGallery extends DataObject {
    static $singular_name = "gallery";
    static $plural_name = "galleries";
    public static $db = array(
        'Title' => 'VarChar'
    );
    public static $has_many = array(
        'SilvercartProducts' => 'SilvercartProduct',
        'pictures'           => 'Image'
    );
}
