<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Widgets
 */

/**
 * Provides a view for pages.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 06.12.2012
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartPageListWidgetPage extends DataExtension {

    /**
     * DB attributes
     *
     * @var array
     */
    public static $db = array(
        'widgetTitle'       => 'VarChar(255)',
        'widgetText'        => 'HTMLText',
        'widgetPriority'    => 'Int(0)'
    );
    
    /**
     * Has one relations
     *
     * @var array
     */
    public static $has_one = array(
        'widgetImage' => 'Image'
    );
    
    /**
     * Belongs many many relations
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartPageListWidgets' => 'SilvercartPageListWidget'
    );

    /**
     * Add labels.
     *
     * @param array &$labels The labels
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2013
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
                array(
                    'widgetInfoTab'     => _t('SilvercartPageListWidgetPage.WIDGET_INFO_TAB'),
                    'widgetInfoTabInfo' => _t('SilvercartPageListWidgetPage.WIDGET_INFO_TAB_EXPLANATION'),
                    'widgetImage'       => _t('SilvercartPageListWidgetPage.WIDGET_IMAGE'),
                    'widgetText'        => _t('SilvercartPageListWidgetPage.WIDGET_TEXT'),
                    'widgetTitle'       => _t('SilvercartPageListWidgetPage.WIDGET_TITLE'),
                    'widgetPriority'    => _t('SilvercartPageListWidgetPage.WIDGET_PRIORITY'),
                )
        );
    }

    /**
     * Add fields to CMS fields.
     *
     * @param FieldSet $fields The FieldSet
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.04.2013
     */
    public function updateCMSFields(FieldList $fields) {
        $widgetInfoField = ToggleCompositeField::create(
                'widgetInfoTab',
                $this->owner->fieldLabel('widgetInfoTab'),
                array(
                    new LiteralField(   'widgetInfoTabExplanation',             '<div class="field">' . $this->owner->fieldLabel('widgetInfoTabInfo') . '</div>'),
                    new TextField(      'widgetPriority',                       $this->owner->fieldLabel('widgetPriority')),
                    new TextField(      'widgetTitle',                          $this->owner->fieldLabel('widgetTitle')),
                    new HtmlEditorField('widgetText',                           $this->owner->fieldLabel('widgetText')),
                    new UploadField(    'widgetImage',                          $this->owner->fieldLabel('widgetImage')),
                )
        )->setHeadingLevel(4);

        $fields->addFieldToTab('Root.Widgets', $widgetInfoField);
    }
}