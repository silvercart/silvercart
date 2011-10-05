<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * @subpackage Backend
 */

/**
 * We want to use the source class's summaryFields for all our ModelAdmins.
 *
 * @package Silvercart
 * @subpacke Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 05.10.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartTableListField extends TableListField {
    
    /**
     * Set the source class's summary fields as fieldList parameter.
     * 
     * @param string $name         The name of the field
     * @param string $sourceClass  The source class
     * @param array  $fieldList    A list of fields to use for the summary
     * @param string $sourceFilter SQL filter statement
     * @param string $sourceSort   SQL sort statement
     * @param string $sourceJoin   SQL join statement
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.10.2011
     */
    function __construct($name, $sourceClass, $fieldList = null, $sourceFilter = null, 
		$sourceSort = null, $sourceJoin = null) {

		parent::__construct($name, $sourceClass, singleton($sourceClass)->summaryFields(), $sourceFilter, $sourceSort, $sourceJoin);
	}
}