<?php

namespace SilverCart\View;

use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\SSViewer;

/**
 * Trait to add extende Extensible features to a DataObject.
 * 
 * @package SilverCart
 * @subpackage ORM
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait RenderableDataObject
{
    /**
     * Returns the rendered DataObject.
     * 
     * @param string $templateAddition Optional template name addition
     * @param array  $customFields     Optional template custom fields
     * 
     * @return DBHTMLText
     */
    public function forTemplate(string $templateAddition = '', array $customFields = []) : DBHTMLText
    {
        $addition  = empty($templateAddition) ? '' : "_{$templateAddition}";
        $templates = SSViewer::get_templates_by_class(static::class, $addition, __CLASS__);
        return $this->renderWith($templates, $customFields);
    }
}