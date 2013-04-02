<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage FormFields
 */

/**
 * Contains one field for the Silvercart order export. One or more fields are
 * connected to an exporter.
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 06.07.2011
 * @license see license file in modules root directory
 */
class SilvercartProductExporterField extends DataObject {
    
    /**
     * Attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public static $db = array(
        'name'                                  => 'VarChar(255)',
        'headerTitle'                           => 'VarChar(255)',
        'sortOrder'                             => 'Int',
        'isCallbackField'                       => 'Boolean(0)'
    );
    
    /**
     * Has-one relationships.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.07.2011
     */
    public static $has_one = array(
        'SilvercartProductExporter' => 'SilvercartProductExporter'
    );
}