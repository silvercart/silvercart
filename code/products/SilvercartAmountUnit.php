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
 * @subpackage Products
 */

/**
 * Abstract for SilvercartamountUnit
 *
 * @package Silvercart
 * @subpackage Products
 * @author Ramon Kupper <rkupper@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 25.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartAmountUnit extends DataObject {

    /**
     * singular name for backend
     *
     * @var string
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.03.2011
     */
    static $singular_name = "amount unit";

    /**
     * plural name for backend
     *
     * @var string
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 29.03.2011
     */
    static $plural_name = "amount units";

    /**
     * attributes
     *
     * @var array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 29.03.2011
     */
    public static $db = array(
        'Name' => 'VarChar(50)',
        'Abbreviation' => 'VarChar(5)'
    );

    /**
     * Constructor. We localize the static variables here.
     *
     * @param array|null $record      This will be null for a new database record.
     *                                  Alternatively, you can pass an array of
     *                                  field values.  Normally this contructor is only used by the internal systems that get objects from the database.
     * @param boolean    $isSingleton This this to true if this is a singleton() object, a stub for calling methods.  Singletons
     *                                  don't have their defaults set.
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function __construct($record = null, $isSingleton = false) {

        self::$singular_name = _t('SilvercartAmountUnit.SINGULARNAME', 'amount unit');
        self::$plural_name = _t('SilvercartAmountUnit.PLURALNAME', 'amount units');
        parent::__construct($record, $isSingleton);
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function summaryFields() {
        return array_merge(
                parent::summaryFields(),
                array(
                    'Name' => _t('SilvercartAmountUnit.NAME'),
                    'Abbreviation' => _t('SilvercartAmountUnit.ABBREVIATION')
                )
        );
    }

    /**
     * Field labels for display in tables.
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 29.03.2011
     */
    public function fieldLabels() {
        return array_merge(
                parent::fieldLabels(),
                array(
                    'Name' => _t('SilvercartAmountUnit.NAME'),
                    'Abbreviation' => _t('SilvercartAmountUnit.ABBREVIATION')
                )
        );
    }
}
