<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Translation
 */

/**
 * Translation object of SilvercartProductGroupNavigationWidget
 *
 * @package Silvercart
 * @subpackage Translation
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.09.2013
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartProductGroupNavigationWidgetLanguage extends DataObject {

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'FrontTitle'   => 'VarChar(255)',
    );

    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartProductGroupNavigationWidget' => 'SilvercartProductGroupNavigationWidget'
    );

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     *
     * @return string The objects singular name
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function singular_name() {
        return _t('Silvercart.TRANSLATION');
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     *
     * @return string the objects plural name
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function plural_name() {
        return _t('Silvercart.TRANSLATIONS');
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'FrontTitle'                             => _t('WidgetSetWidget.FRONTTITLE'),
                'SilvercartProductGroupNavigationWidget' => _t('SilvercartProductGroupNavigationWidget.SINGULARNAME'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Summary fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2013
     */
    public function summaryFields() {
        $summaryFields = array_merge(
            parent::summaryFields(),
            array(
                'FrontTitle' => $this->fieldLabel('FrontTitle'),
            )
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}

