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
 * show details of a customers orders
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartOrderDetailPage extends Page {

    public static $singular_name = "";
    public static $can_be_root = false;

    /**
     * configure the class name of the DataObjects to be shown on this page
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getSection() {
        return 'SilvercartOrder';
    }
}

/**
 * Controller of this page type
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartOrderDetailPage_Controller extends Page_Controller {

    /**
     * returns a single order of a logged in member identified by url param id
     *
     * @return DataObject Order object
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.10.10
     */
    public function CustomersOrder() {
        $id         = Convert::raw2sql($this->urlParams['ID']);
        $memberID   = Member::currentUserID();
        $order      = false;
        
        if ($memberID && $id) {
            $order = DataObject::get_one(
                'SilvercartOrder',
                sprintf(
                    "`ID`= '%s' AND `MemberID` = '%s'",
                    $id,
                    $memberID
                )
            );

            return $order;
        }
    }
}