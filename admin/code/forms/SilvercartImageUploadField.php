<?php

/**
 * Copyright 2013 pixeltricks GmbH
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
 * @subpackage Forms_Fields
 */

/**
 * Same as SilvercartFileUploadField but uses SilvercartImage instead of 
 * SilvercartFile.
 *
 * @package Silvercart
 * @subpackage Forms_Fields
 * @copyright pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.03.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartImageUploadField extends SilvercartFileUploadField {
    
    /**
     * Class name of the file object
     *
     * @var string
     */
    protected $fileClassName = 'Image';
    
    /**
     * Class name of the relation object
     *
     * @var string
     */
    protected $relationClassName = 'SilvercartImage';

}
