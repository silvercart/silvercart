<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Pages\Page;
use SilverCart\Model\Widgets\PageListWidget;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataExtension;
use function _t;

/**
 * PageListWidget Page Extension.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property Page $owner Owner
 */
class PageListWidgetPage extends DataExtension
{
    /**
     * DB attributes
     *
     * @var array
     */
    private static array $db = [
        'widgetTitle'    => 'Varchar(255)',
        'widgetText'     => 'HTMLText',
        'widgetPriority' => 'Int(0)'
    ];
    
    /**
     * Has one relations
     *
     * @var array
     */
    private static array $has_one = [
        'widgetImage' => Image::class,
    ];
    
    /**
     * Belongs many many relations
     *
     * @var array
     */
    private static array $belongs_many_many = [
        'PageListWidgets' => PageListWidget::class . '.Pages',
    ];

    /**
     * Add labels.
     *
     * @param array &$labels The labels
     *
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels = array_merge(
                $labels,
                [
                    'widgetInfoTab'     => _t(PageListWidgetPage::class . '.WIDGET_INFO_TAB', 'Widget infos'),
                    'widgetInfoTabInfo' => _t(PageListWidgetPage::class . '.WIDGET_INFO_TAB_EXPLANATION', 'The following data can be used by some widgets.'),
                    'widgetImage'       => _t(PageListWidgetPage::class . '.WIDGET_IMAGE', 'Image'),
                    'widgetText'        => _t(PageListWidgetPage::class . '.WIDGET_TEXT', 'Description'),
                    'widgetTitle'       => _t(PageListWidgetPage::class . '.WIDGET_TITLE', 'Title (optional)'),
                    'widgetPriority'    => _t(PageListWidgetPage::class . '.WIDGET_PRIORITY', 'Sort priority (the higher the more backwards)'),
                ]
        );
    }

    /**
     * Add fields to CMS fields.
     *
     * @param FieldList $fields The FieldList
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        $widgetInfoField = ToggleCompositeField::create(
                'widgetInfoTab',
                $this->owner->fieldLabel('widgetInfoTab'),
                [
                    LiteralField::create(   'widgetInfoTabExplanation',             '<div class="field">' . $this->owner->fieldLabel('widgetInfoTabInfo') . '</div>'),
                    TextField::create(      'widgetPriority',                       $this->owner->fieldLabel('widgetPriority')),
                    TextField::create(      'widgetTitle',                          $this->owner->fieldLabel('widgetTitle')),
                    HTMLEditorField::create('widgetText',                           $this->owner->fieldLabel('widgetText')),
                    UploadField::create(    'widgetImage',                          $this->owner->fieldLabel('widgetImage')),
                ]
        )->setHeadingLevel(4);
        $fields->addFieldToTab('Root.Widgets', $widgetInfoField);
    }
}