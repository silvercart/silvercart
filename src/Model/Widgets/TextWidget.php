<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Admin\Model\Config;
use SilverCart\Model\Widgets\TextWidgetTranslation;
use SilverCart\Model\Widgets\Widget;

/**
 * Provides a free text widget.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TextWidget extends Widget {
    
    /**
     * DB attributes
     * 
     * @var array
     */
    private static $db = array(
        'isContentView' => 'Boolean',
    );
    
    /**
     * Casted Attributes.
     * 
     * @var array
     */
    private static $casting = array(
        'Headline' => 'Text',
        'FreeText' => 'Text',
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'TextWidgetTranslations' => TextWidgetTranslation::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartTextWidget';

    /**
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string
     */
    public function getFreeText() {
        return $this->getTranslationFieldValue('FreeText');
    }

    /**
     * retirieves the attribute Headline from related language class depending
     * on the current locale
     *
     * @return string
     */
    public function getHeadline() {
        return $this->getTranslationFieldValue('Headline');
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.03.2014
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'TextWidgetTranslations' => TextWidgetTranslation::singleton()->plural_name(),
                'Headline'               => _t(TextWidget::class . '.HEADLINEFIELD_LABEL', 'Headline (optional):'),
                'FreeText'               => _t(TextWidget::class . '.FREETEXTFIELD_LABEL', 'Your text:'),
                'isContentView'          => _t(TextWidget::class . '.IS_CONTENT_VIEW', 'use content view instead of widget view'),
                'Content'                => _t(TextWidget::class . '.CONTENT', 'Content'),
                'Translations'           => _t(Config::class . '.TRANSLATIONS', 'Translations'),
                
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
}