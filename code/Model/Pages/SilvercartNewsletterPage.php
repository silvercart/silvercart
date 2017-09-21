<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Page for newsletter (un)subscription.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 22.03.2011
 */
class SilvercartNewsletterPage extends SilvercartMetaNavigationHolder {
    
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = array(
        'UseDoubleOptIn' => 'Boolean',
    );
    
    /**
     * default values for DB attributes
     *
     * @var array
     */
    private static $defaults = array(
        'UseDoubleOptIn' => true,
    );

    /**
     * Defines the allowed children of this page.
     *
     * @var array
     */
    public static $allowed_children = array(
        'SilvercartNewsletterResponsePage',
        'SilvercartNewsletterOptInConfirmationPage'
    );
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/img/page_icons/metanavigation_page";
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $useDoubleOptInField = new CheckboxField('UseDoubleOptIn', $this->fieldLabel('UseDoubleOptIn'));
        $fields->insertAfter('MenuTitle', $useDoubleOptInField);
        
        return $fields;
    }
    
    public function fieldLabels($includerelations = true) {
        return array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'UseDoubleOptIn' => _t('SilvercartNewsletterPage.UseDoubleOptIn'),
                )
        );
    }
    
}


/**
 * Page for newsletter (un)subscription.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 * @since 22.03.2011
 */
class SilvercartNewsletterPage_Controller extends SilvercartMetaNavigationHolder_Controller {

    /**
     * Here we initialise the form object.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.03.2011
     */
    public function init() {
        $this->registerCustomHtmlForm('SilvercartNewsletterForm', new SilvercartNewsletterForm($this));

        parent::init();
    }
}
