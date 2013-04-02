<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms
 */

/**
 * dummy form definition for ProductAddCartForm. It is used to register with
 * ProductPage (only) to provide a seperate template for all different product
 * views (detail, list, tile).
 *
 * @package Silvercart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 14.02.2011
 * @license see license file in modules root directory
 */
class SilvercartProductAddCartFormDetail extends SilvercartProductAddCartForm {

    /**
     * Insert additional fields for this form from registered plugins.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.11.2011
     */
    public function AddCartFormDetailAdditionalFields() {
        return SilvercartPlugin::call($this, 'AddCartFormDetailAdditionalFields', array($this));
    }
}