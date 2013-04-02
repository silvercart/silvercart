<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Backend
 */

/**
 * We want to use the source class's summaryFields for all our ModelAdmins.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 05.10.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
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
    public function __construct($name, $sourceClass, $fieldList = null, $sourceFilter = null, $sourceSort = null, $sourceJoin = null) {

        parent::__construct($name, $sourceClass, singleton($sourceClass)->summaryFields(), $sourceFilter, $sourceSort, $sourceJoin);
    }
}