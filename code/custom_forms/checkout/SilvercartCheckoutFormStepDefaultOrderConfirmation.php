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
 * @subpackage Forms Checkout
 */

/**
 * CheckoutShowConfirmation
 *
 * shows order confirmation
 *
 * @package Silvercart
 * @subpackage Forms Checkout
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>,
 *         Sascha Koehler <skoehler@pixeltricks.de>
 * @since 14.05.2013
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCheckoutFormStepDefaultOrderConfirmation extends CustomHtmlFormStep {

    /**
     * A list of custom output to add to the content area.
     *
     * @var array
     */
    public static $customOutput = array();

    /**
     * Don't cache this form.
     *
     * @var bool
     */
    protected $excludeFromCache = true;

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.03.2011
     */
    public function preferences() {
        $this->preferences['stepIsVisible']                    = false;
        $this->preferences['stepTitle']                        = _t('SilvercartCheckoutFormStepDefaultOrderConfirmation.TITLE', 'Order Confirmation');
        $this->preferences['ShowCustomHtmlFormStepNavigation'] = false;
        $this->preferences['createShoppingcartForms']          = false;
        $this->preferences['doJsValidationScrolling']          = false;

        parent::preferences();
    }

    /**
     * Renders the default order confirmation template.
     * Can be called in the last step template of a payment module.
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 18.04.2011
     */
    public function defaultOrderConfirmation() {
        return $this->renderWith('SilvercartCheckoutFormStepDefaultOrderConfirmation');
    }

    /**
     * Add a custom output snippet.
     *
     * @param string $output the output to add
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since  2013-05-06
     */
    public static function addCustomOutput($output) {
        self::$customOutput[] = $output;
    }

    /**
     * Returns the combined custom output snippets as string.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since  2013-05-06
     */
    public function CustomOutput() {
        $this->extend('updateCustomOutput');

        $output = '';

        if (count(self::$customOutput) > 0) {
            $output = implode("\n", self::$customOutput);
        }

        return $output;
    }
}
