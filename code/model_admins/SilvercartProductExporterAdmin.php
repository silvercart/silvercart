<?php
/**
 * Copyright 2014 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for SilvercartProductExporters
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sebastian Diel <sdiel@pixeltricks.de>,
 *         Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2014 pixeltricks GmbH
 * @since 03.03.2014
 * @license see license file in modules root directory
 */
class SilvercartProductExporterAdmin extends SilvercartModelAdmin {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'modules';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 10;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-product-exporters';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Product Exporters';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartProductExporter'
    );

    /**
     * Class name of the form field used for the results list.  Overloading this in subclasses
     * can let you customise the results table field.
     * 
     * @var string
     */
    protected $resultsTableClassName = 'SilvercartProductExportTableListField';

    /**
     * Provides a way to use different result tables for the managed models.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2012
     */
    public function resultsTableClassName() {
        $className = $this->resultsTableClassName;
        return $className;
    }
}
