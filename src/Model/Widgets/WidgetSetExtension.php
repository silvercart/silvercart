<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Admin\Forms\GridField\GridFieldPublishAction;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBBoolean;

/**
 * Extension for WidgetSets\Model\WidgetSet.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class WidgetSetExtension extends DataExtension
{
    /**
     * Attributes
     *
     * @var array
     */
    private static $db = [
        'UseAsSlider' => DBBoolean::class,
    ];

    /**
     * used to override the WidgetSet::getCMSFields to use the
     * SilverCarts scaffholding with excluded attributes and relations
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Patrick Schneider <pschneider@pixeltricks.de>
     * @since 06.10.2018
     */
    public function overrideGetCMSFields()
    {
        $fields = DataObjectExtension::getCMSFields($this->owner);
        $fields->addFieldsToTab(
            'Root.Main',
             $this->owner->scaffoldWidgetAreaFields()
        );
        
        $widgetsField = $fields->dataFieldByName('Widgets');
        if ($widgetsField instanceof GridField) {
            $widgetsFieldConfig = $widgetsField->getConfig();
            $widgetsFieldConfig->addComponent(new GridFieldPublishAction());
        }
        
        return $fields;
    }
    
    /**
     * exclude these fields from form scaffolding
     *
     * @return array the field names in a numeric array 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 20.02.2013
     */
    public function excludeFromScaffolding()
    {
        $excludedFields = ['WidgetArea'];
        return $excludedFields;
    }
    
    /**
     * Field labels.
     * 
     * @param array &$fieldLabels Field labels to update
     * 
     * @return void
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 20.02.2013
     */
    public function updateFieldLabels(&$fieldLabels)
    {
        $fieldLabels = array_merge(
                $fieldLabels,
                [
                    'UseAsSlider'      => _t(WidgetSetExtension::class . '.UseAsSlider', 'Use as a slider'),
                    'ManageWidgetSets' => _t(WidgetSetExtension::class . '.MANAGE_WIDGETS_BUTTON', 'Manage widget sets'),
                ]
        );
    }
}