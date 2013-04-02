<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
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
 * @copyright 2013 pixeltricks GmbH
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.03.2013
 * @license see license file in modules root directory
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
