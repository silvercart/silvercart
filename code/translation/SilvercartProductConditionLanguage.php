<?php

/**
 * 
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 12.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductConditionLanguage extends DataObject {
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.01.2012
     */
    public function singular_name() {
        if (_t('SilvercartProductConditionLanguage.SINGULARNAME')) {
            return _t('SilvercartProductConditionLanguage.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.01.2012
     */
    public function plural_name() {
        if (_t('SilvercartProductConditionLanguage.PLURALNAME')) {
            return _t('SilvercartProductConditionLanguage.PLURALNAME');
        } else {
            return parent::plural_name();
        }

    }
    
    /**
     * Attributes.
     *
     * @var array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.01.2012
     */
    public static $db = array(
        'Title' => 'VarChar(255)'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 12.01.2012
     */
    public static $has_one = array(
        'SilvercartProductCondition' => 'SilvercartProductCondition'
    );
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 12.01.2012
     */
    public function summaryFields() {
        $summaryFields = array(
            'Title' => _t('SilvercartProduct.COLUMN_TITLE')
        );


        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 12.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
            'Title' => _t('SilvercartProduct.COLUMN_TITLE')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}

