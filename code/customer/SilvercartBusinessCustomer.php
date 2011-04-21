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
 * abstract for a business customer which has own attributes.
 * They are treated differently when it comes to billing
 *
 * @package Silvercart
 * @subpackage Customer
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartBusinessCustomer extends Member {

    public static $db = array(
        'UmsatzsteuerID' => 'VarChar'
    );

    /**
     * Add CustomerNumber to searchable fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.04.2011
     */
    public function searchableFields() {
        return array_merge(
                parent::searchableFields(),
                array(
                    'CustomerNumber' => array(
                        'title'     => _t('SilvercartCustomerRole.CUSTOMERNUMBER'),
                        'filter'    => 'PartialMatchFilter'
                    ),
                )
        );
    }

    /**
     * Set a new/reserved customernumber before writing
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.04.2011
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();
        if (empty ($this->CustomerNumber)) {
            $this->CustomerNumber = SilvercartNumberRange::useReservedNumberByIdentifier('CustomerNumber');
        }
    }
}
