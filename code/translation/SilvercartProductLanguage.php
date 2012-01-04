<?php

/**
 * Translations for a product
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartProductLanguage extends DataObject {
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 04.01.2012
     */
    public function singular_name() {
        if (_t('SilvercartProductLanguage.SINGULARNAME')) {
            return _t('SilvercartProductLanguage.SINGULARNAME');
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
     * @since 04.01.2012
     */
    public function plural_name() {
        if (_t('SilvercartProductLanguage.PLURALNAME')) {
            return _t('SilvercartProductLanguage.PLURALNAME');
        } else {
            return parent::plural_name();
        }

    }
    
    public static $db = array(
        'Locale'            => 'DBLocale',
        'Title'             => 'VarChar(255)',
        'ShortDescription'  => 'Text',
        'LongDescription'   => 'HTMLText',
        'MetaDescription'   => 'VarChar(255)',
        'MetaTitle'         => 'VarChar(64)', //search engines use only 64 chars
        'MetaKeywords'      => 'VarChar'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.01.2012
     */
    public static $has_one = array(
        'SilvercartProduct' => 'SilvercartProduct'
    );
    
    public function canTranslate() {
        return true;
    }

    public function getCMSFields_forPopup() {
        return SilvercartLanguageHelper::prepareCMSFields_forPopup($this);
    }
    
    public function summaryFields() {
        $summaryFields = array(
            'Locale' => _t('SilvercartConfig.TRANSLATION'),
            'Title'  => _t('SilvercartProduct.COLUMN_TITLE')
        );
        
        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}

