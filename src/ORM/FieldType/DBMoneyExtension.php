<?php

namespace SilverCart\ORM\FieldType;

use SilverStripe\Core\Extension;

/**
 * Extension for SilverStripe\ORM\FieldType\DBMoney.
 *
 * @package SilverCart
 * @subpackage ORM_FieldType
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 10.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DBMoneyExtension extends Extension {

    /**
     * Returns the amount formatted.
     *
     * @param array $options The options
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.02.2013
     */
    public function NiceAmount($options = array()) {
        $options['display'] = Zend_Currency::NO_SYMBOL;
        return $this->owner->Nice($options);
    }

}