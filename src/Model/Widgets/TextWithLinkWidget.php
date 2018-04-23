<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\TextWidget;
use SilverCart\Model\Widgets\TextWithLinkWidgetTranslation;
use SilverStripe\Forms\FieldList;

/**
 * Provides a free text with link widget.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TextWithLinkWidget extends TextWidget {
    
    /**
     * DB attributes
     * 
     * @var array
     */
    private static $db = array(
        'Link' => 'Varchar(256)',
    );
    
    /**
     * Casted Attributes.
     * 
     * @var array
     */
    private static $casting = array(
        'LinkText' => 'Text',
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = array(
        'TextWithLinkWidgetTranslations' => TextWithLinkWidgetTranslation::class,
    );

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartTextWithLinkWidget';

    /**
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string
     */
    public function getLinkText() {
        return $this->getTranslationFieldValue('LinkText');
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Link'                           => _t(TextWithLinkWidget::class . '.Link', 'Link'),
                'LinkText'                       => _t(TextWithLinkWidget::class . '.LinkText', 'Link-Text'),
                'TextWithLinkWidgetTranslations' => TextWithLinkWidget::singleton()->plural_name(),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('TextWidgetTranslations');
        return $fields;
    }
}