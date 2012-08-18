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
 * @subpackage Update
 */

/**
 * Update 1.0 - 2
 * This update moves existing product images to the products image gallery.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 25.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_0__2 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion' => '1.0',
        'SilvercartUpdateVersion' => '2',
        'Description' => 'This update moves existing product images to the products image gallery.',
    );

    /**
     * Executes the update logic.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.03.2011
     */
    public function executeUpdate() {
        $changed = false;
        // get Product and Image ID pairs
        $resultSet = DB::query("
            SELECT
                `ID`,`ImageID`
            FROM
                `SilvercartProduct`
            WHERE
                `ImageID` > 0");
        foreach ($resultSet as $row) {
            $product = DataObject::get_by_id('SilvercartProduct', $row['ID']);
            if ($product->SilvercartImages()->Count() == 0) {
                $newImageObject = new SilvercartImage();
                $newImageObject->ImageID = $row['ImageID'];
                $newImageObject->SilvercartProductID = $row['ID'];
                $newImageObject->write();
                $changed = true;
            }
        }
        
        if (!$changed) {
            $this->skip();
        }
        return $changed;
    }

}