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
 * Child of customer area; overview of all addresses;
 *
 * @copyright 2010 pixeltricks GmbH
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 16.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartAddressHolder extends SilvercartMyAccountHolder {

    public static $singular_name = "";
    public static $can_be_root = false;
    public static $allowed_children = array(
        "SilvercartAddressPage"
    );

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        return $fields;
    }

    /**
     * configure the class name of the DataObjects to be shown on this page
     * this is needed to show correct breadcrumbs
     *
     * @return string class name of the DataObject to be shown on this page
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 3.11.2010
     */
    public function getSection() {
        return 'SilvercartAddress';
    }

}

/**
 * Controller Class
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 16.02.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2010 pixeltricks GmbH
 */
class SilvercartAddressHolder_Controller extends SilvercartMyAccountHolder_Controller {

    /**
     * execute these statements on object call
     *
     * @return void
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.10.2010
     */
    public function init() {
        parent::init();
    }

}