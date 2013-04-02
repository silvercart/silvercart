<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Products
 */

/**
 * Abstract for SilvercartQuantityUnit
 *
 * @package Silvercart
 * @subpackage Products
 * @author Ramon Kupper <rkupper@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 25.03.2011
 * @license see license file in modules root directory
 */
class SilvercartQuantityUnit extends DataObject {

    /**
     * Attributes
     *
     * @var array
     *
     * @since 2012-11-22
     */
    public static $db = array(
        'numberOfDecimalPlaces' => 'Int'
    );

    /**
     * cast field types to other SS data types
     *
     * @var array
     */
    public static $casting = array(
        'Title'          => 'Text',
        'Abbreviation'   => 'Text'
    );
    
    /**
     * 1:n relations
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartQuantityUnitLanguages' => 'SilvercartQuantityUnitLanguage'
    );
    
    /**
     * getter for the quantity units title
     *
     * @return string the title in the corresponding front end language 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.01.2012
     */
    public function getTitle() {
        return $this->getLanguageFieldValue('Title');
    }
    
    /**
     * getter for the quantity units title
     *
     * @return string the title in the corresponding front end language 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 11.01.2012
     */
    public function getAbbreviation() {
        return $this->getLanguageFieldValue('Abbreviation');
    }
    
    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldList the fields for the backend
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);

        $fields->insertAfter(
            new LiteralField(
                'ExplanationToDecimalPlaces',
                '<p class="silvercart-formfield-label"><i>'._t('SilvercartQuantityUnit.EXPLANATION_TO_DECIMAL_PLACES').'</i></p>'
            ),
            'numberOfDecimalPlaces'
        );
        
        return $fields;
    }

    /**
     * Returns the translated singular name of the object.
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this);
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @since 29.03.2011
     */
    public function summaryFields() {
        return array_merge(
            parent::summaryFields(),
            array(
                'Title'             => $this->fieldLabel('Title'),
                'Abbreviation'      => $this->fieldLabel('Abbreviation'),
            )
        );
    }

    /**
     * Field labels for display in tables.
     * 
     * @param bool $includerelations config option
     *
     * @return array
     *
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @since 29.03.2011
     */
    public function fieldLabels($includerelations = true) {
        return array_merge(
            parent::fieldLabels(),
            array(
                'Title'                             => _t('SilvercartQuantityUnit.NAME'),
                'Abbreviation'                      => _t('SilvercartQuantityUnit.ABBREVIATION'),
                'SilvercartQuantityUnitLanguages'   => _t('SilvercartQuantityUnitLanguage.PLURALNAME'),
                'numberOfDecimalPlaces'             => _t('SilvercartQuantityUnit.NUMBER_OF_DECIMAL_PLACES'),
            )
        );
    }
}
