<?php

/**
 * Define how an url can be built to redirect to a product detail page without
 * The URL has the attribute and the value as parameters:
 * www.mysite.com/deeplink/attribute/value
 * These definitions are always handled on the SilvercartDeeplinkPage.
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 29.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDeeplink extends DataObject {
    
    
    public static $singular_name = "Deeplink";
    
    public static $plural_name = "Deeplinks";
    /**
     * attributes
     * 
     * @var array additional attributes 
     */
    public static $db = array(
        'productAttribute' => 'VarChar(50)',
        'isActive'         => 'Boolean(0)'
    );
    
    public static $casting = array(
        'DeeplinkUrl'      => 'VarChar',
        'ActivationStatus' => 'VarChar'
    );
    
    /**
     * getter for the avtivation status
     * 
     * @return boolean answer 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.7.2011
     */
    public function isActive() {
        return $this->isActive;
    }
    
    /**
     * Returns the text label for the deeplinks activion status.
     *
     * @return string answer as text
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 30.7.2011
     */
    public function getActivationStatus() {
        if ($this->isActive()) {
            return _t('Silvercart.YES');
        }
        return _t('Silvercart.NO');
    }
    
    /**
     * Return the absolute URL of the deeplink page plus the attribute for the
     * filter;
     * 
     * @return string The absolute link to this deeplink setting or an empty string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * since 28.7.2011
     */
    public function getDeeplinkUrl() {
        $url = "";
        $deeplinkPage = SilvercartPage_Controller::PageByIdentifierCode("SilvercartDeeplinkPage");
        if ($deeplinkPage && $this->productAttribute != "" && $this->isActive()) {
            $url = $deeplinkPage->AbsoluteLink() . $this->productAttribute . "/";
        }
        return $url;
    }
    
    /**
     *
     * @param array $params ???
     * 
     * @return FieldSet a set of fields 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.7.2011
     */
    public function getCMSFields($params = null) {
        $productFields  = array();
        $fields         = parent::getCMSFields($params);
        $fields->removeByName('productAttribute');
        
        $dbFields = DataObject::database_fields('SilvercartProduct');
        foreach($dbFields as $fieldName => $fieldType) {
            $productFields[$fieldName] = $fieldName;
        }
        
        $productAttributes = DataObject::database_fields('SilvercartProduct');
        $productAttributeDropdownSource = SilvercartProduct::fieldLabels();
        $productAttributeDropdown = new DropdownField('productAttribute', _t('SilvercartAttribute.SINGULARNAME'), $productFields, null, null, _t('SilvercartEditAddressForm.EMPTYSTRING_PLEASECHOOSE'));
        
        $fields->addFieldToTab('Root.Main', $productAttributeDropdown);
        $fields->addFieldToTab('Root.Main', new ReadonlyField('deeplink', $this->singular_name(), $this->getDeeplinkUrl()));
        return $fields;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 30.7.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'productAttribute' => _t('SilvercartAttribute.SINGULARNAME'),
            'ActivationStatus' => _t('SilvercartCountry.ACTIVE'),
            'DeeplinkUrl'      => 'Deeplink'
        );
        return $summaryFields;
    }
}

