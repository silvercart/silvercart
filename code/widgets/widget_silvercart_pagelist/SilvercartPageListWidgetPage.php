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
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function updateFieldLabels(&$labels) {
        $labels['widgetInfoTab']    = _t('SilvercartPageListWidgetPage.WIDGET_INFO_TAB');
        $labels['widgetImage']      = _t('SilvercartPageListWidgetPage.WIDGET_IMAGE');
        $labels['widgetText']       = _t('SilvercartPageListWidgetPage.WIDGET_TEXT');
        $labels['widgetTitle']      = _t('SilvercartPageListWidgetPage.WIDGET_TITLE');
        $labels['widgetPriority']   = _t('SilvercartPageListWidgetPage.WIDGET_PRIORITY');
    }

    /**
     * Add fields to CMS fields.
     *
     * @param FieldSet $fields The FieldSet
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.12.2012
     */
    public function updateCMSFields(FieldList $fields) {
        $widgetInfoTab = $fields->findOrMakeTab('Root.WidgetInfoTab', $this->owner->fieldLabel('widgetInfoTab'));

        $infoField = new LabelField(
            'widgetInfoTabExplanation',
            _t('SilvercartPageListWidgetPage.WIDGET_INFO_TAB_EXPLANATION')
        );
        $priorityField = new TextField(
            'widgetPriority',
            $this->owner->fieldLabel('widgetPriority')
        );
        $titleField = new TextField(
            'widgetTitle',
            $this->owner->fieldLabel('widgetTitle')
        );
        $textField = new HtmlEditorField(
            'widgetText',
            $this->owner->fieldLabel('widgetText')
        );
        $imageField = new UploadField(
            'widgetImage',
            $this->owner->fieldLabel('widgetImage')
        );

        $widgetInfoTab->push($infoField);
        $widgetInfoTab->push($priorityField);
        $widgetInfoTab->push($titleField);
        $widgetInfoTab->push($textField);
        $widgetInfoTab->push($imageField);
    }
}