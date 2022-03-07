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
     * Text color for primary.
     * 
     * @var string
     */
    private static $text_color_primary = '#ffffff';
    /**
     * Background color for primary.
     * 
     * @var string
     */
    private static $bg_color_primary   = '#007bff';
    /**
     * Text color for secondary.
     * 
     * @var string
     */
    private static $text_color_secondary = '#ffffff';
    /**
     * Background color for secondary.
     * 
     * @var string
     */
    private static $bg_color_secondary   = '#6c757d';
    /**
     * Text color for success.
     * 
     * @var string
     */
    private static $text_color_success = '#ffffff';
    /**
     * Background color for success.
     * 
     * @var string
     */
    private static $bg_color_success   = '#28a745';
    /**
     * Text color for danger.
     * 
     * @var string
     */
    private static $text_color_danger = '#ffffff';
    /**
     * Background color for danger.
     * 
     * @var string
     */
    private static $bg_color_danger   = '#dc3545';
    /**
     * Text color for warning.
     * 
     * @var string
     */
    private static $text_color_warning = '#212529';
    /**
     * Background color for warning.
     * 
     * @var string
     */
    private static $bg_color_warning   = '#ffc107';
    /**
     * Text color for info.
     * 
     * @var string
     */
    private static $text_color_info = '#ffffff';
    /**
     * Background color for info.
     * 
     * @var string
     */
    private static $bg_color_info   = '#17a2b8';
    /**
     * Text color for light.
     * 
     * @var string
     */
    private static $text_color_light = '#212529';
    /**
     * Background color for light.
     * 
     * @var string
     */
    private static $bg_color_light   = '#f8f9fa';
    /**
     * Text color for dark.
     * 
     * @var string
     */
    private static $text_color_dark = '#ffffff';
    /**
     * Background color for dark.
     * 
     * @var string
     */
    private static $bg_color_dark   = '#343a40';
    
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
        $title = $this->owner->Title;
        if (empty($title)) {
            $title = $this->owner->singular_name();
        }
        return [
            'primary'   => Tools::string2html("<span style=\"padding: 4px 8px; color:{$this->owner->config()->text_color_primary}; background-color:{$this->owner->config()->bg_color_primary}\">{$title}</span>"),
            'secondary' => Tools::string2html("<span style=\"padding: 4px 8px; color:{$this->owner->config()->text_color_secondary}; background-color:{$this->owner->config()->bg_color_secondary}\">{$title}</span>"),
            'success'   => Tools::string2html("<span style=\"padding: 4px 8px; color:{$this->owner->config()->text_color_success}; background-color:{$this->owner->config()->bg_color_success}\">{$title}</span>"),
            'danger'    => Tools::string2html("<span style=\"padding: 4px 8px; color:{$this->owner->config()->text_color_danger}; background-color:{$this->owner->config()->bg_color_danger}\">{$title}</span>"),
            'warning'   => Tools::string2html("<span style=\"padding: 4px 8px; color:{$this->owner->config()->text_color_warning}; background-color:{$this->owner->config()->bg_color_warning}\">{$title}</span>"),
            'info'      => Tools::string2html("<span style=\"padding: 4px 8px; color:{$this->owner->config()->text_color_info}; background-color:{$this->owner->config()->bg_color_info}\">{$title}</span>"),
            'light'     => Tools::string2html("<span style=\"padding: 4px 8px; color:{$this->owner->config()->text_color_light}; background-color:{$this->owner->config()->bg_color_light}\">{$title}</span>"),
            'dark'      => Tools::string2html("<span style=\"padding: 4px 8px; color:{$this->owner->config()->text_color_dark}; background-color:{$this->owner->config()->bg_color_dark}\">{$title}</span>"),
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