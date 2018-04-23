<?php

namespace SilverCart\Admin\Forms;

use SilverCart\Admin\Forms\AlertField;

/**
 * Dataless form field to display information alerts.
 *
 * @package SilverCart
 * @subpackage Admin_Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AlertWarningField extends AlertField {
    
    /**
     * Creates a new field.
     * 
     * @param string $name    Field name
     * @param string $content Field content to display
     * @param string $title   Field title to display
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.07.2016
     */
    public function __construct($name, $content = null, $title = null) {
        parent::__construct($name, $content, $title);
        $this->setAlertType('warning');
    }
    
}