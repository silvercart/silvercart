<?php

namespace SilverCart\Extensions\Model;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\OrderStatus;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Extension for status objects.
 * Adds a badge color status option.
 * 
 * @package SilverCart
 * @subpackage Extensions\Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.03.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\ORM\DataObject $owner Owner
 */
class BadgeColorExtension extends DataExtension
{
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'badgeColor' => "Enum('primary,secondary,success,danger,warning,info,light,dark','light')",
    ];
    /**
     * Default DB attribute values.
     *
     * @var array
     */
    private static $defaults = [
        'badgeColor' => 'light',
    ];
    
    /**
     * Updates the field labels.
     * 
     * @param array &$labels Labels to update
     * 
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels = array_merge($labels, [
            'badgeColor' => _t(OrderStatus::class . '.BADGECOLOR', 'Color code'),
            'BadgeColor' => _t(OrderStatus::class . '.BADGECOLOR', 'Color code'),
        ]);
    }
    
    /**
     * Updates the CMS fields.
     * 
     * @param FieldList $fields Fields to update
     * 
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        $badgeColorSource = $this->getBadgeColorDropdownMap();
        $fields->removeByName('badgeColor');
        $fields->addFieldToTab('Root.Main', OptionsetField::create('badgeColor', $this->owner->fieldLabel('badgeColor'), $badgeColorSource));
    }
    
    /**
     * Updates the summary fields.
     * 
     * @param array &$fields Fields to update
     * 
     * @return void
     */
    public function updateSummaryFields(&$fields) : void
    {
        $fields = [
            'BadgeColorIndicator' => $this->owner->fieldLabel('badgeColor'),
        ] + $fields;
    }
    
    /**
     * Helper for summary fields.
     * Returns the badge color indicator.
     * 
     * @return DBHTMLText
     */
    public function getBadgeColorIndicator() : DBHTMLText
    {
        $badgeColorSource = $this->getBadgeColorDropdownMap();
        if (empty($this->owner->badgeColor)
         || !array_key_exists($this->owner->badgeColor, $badgeColorSource)
        ) {
            $this->owner->badgeColor = 'light';
        }
        return $badgeColorSource[$this->owner->badgeColor];
    }
    
    /**
     * Returns the badge color dropdown map.
     * 
     * @return array
     */
    public function getBadgeColorDropdownMap() : array
    {
        return [
            'primary'   => Tools::string2html("<span style=\"padding: 4px 8px; color: #ffffff; background-color:#007bff\">{$this->owner->Title}</span>"),
            'secondary' => Tools::string2html("<span style=\"padding: 4px 8px; color: #ffffff; background-color:#6c757d\">{$this->owner->Title}</span>"),
            'success'   => Tools::string2html("<span style=\"padding: 4px 8px; color: #ffffff; background-color:#28a745\">{$this->owner->Title}</span>"),
            'danger'    => Tools::string2html("<span style=\"padding: 4px 8px; color: #ffffff; background-color:#dc3545\">{$this->owner->Title}</span>"),
            'warning'   => Tools::string2html("<span style=\"padding: 4px 8px; color: #212529; background-color:#ffc107\">{$this->owner->Title}</span>"),
            'info'      => Tools::string2html("<span style=\"padding: 4px 8px; color: #ffffff; background-color:#17a2b8\">{$this->owner->Title}</span>"),
            'light'     => Tools::string2html("<span style=\"padding: 4px 8px; color: #212529; background-color:#f8f9fa\">{$this->owner->Title}</span>"),
            'dark'      => Tools::string2html("<span style=\"padding: 4px 8px; color: #ffffff; background-color:#343a40\">{$this->owner->Title}</span>"),
        ];
    }
    
    /**
     * Renders the badge to display as HTML output.
     * 
     * @param string $cssClasses Optional CSS classes
     * 
     * @return DBHTMLText
     */
    public function HTMLBadge(string $cssClasses = '') : DBHTMLText
    {
        return $this->owner->renderWith(self::class . '_HTMLBadge', [
            'CSSClasses' => $cssClasses,
        ]);
    }
}