<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms
 */

/**
 * form definition
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright 2013 pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license see license file in modules root directory
 * @since 23.10.2010
 */
class SilvercartQuickSearchForm extends CustomHtmlForm {
    
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;

    /**
     * form field definition
     *
     * @var array
     */
    protected $formFields = array(
        'quickSearchQuery' => array(
            'type' => 'TextField',
            'title' => '',
            'value' => '',
            'maxLength' => '30',
            'checkRequirements' => array(
            )
        )
    );

    /**
     * executed if there are no valdation errors on submit
     * Form data is saved in session
     *
     * @param SS_HTTPRequest $data     contains the frameworks form data
     * @param Form           $form     not used
     * @param array          $formData contains the modules form data
     *
     * @return array to be rendered in the controller
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Oliver Scheer <oscheer@pixeltricks.de>, Sebastian Diel <sdiel@πixeltricks.de>
     * @since 25.09.2012
     */
    protected function submitSuccess($data, $form, $formData) {
        $formData['quickSearchQuery'] = trim($formData['quickSearchQuery']);
        Session::set("searchQuery", $formData['quickSearchQuery']);
        $searchQuery = SilvercartSearchQuery::get_by_query(Convert::raw2sql($formData['quickSearchQuery']));
        $searchQuery->Count++;
        $searchQuery->write();
        $searchResultsPage = SilvercartPage_Controller::PageByIdentifierCode("SilvercartSearchResultsPage");
        SilvercartProduct::setDefaultSort('relevance');
        $this->controller->redirect($searchResultsPage->RelativeLink());
    }

    /**
     * Set texts for preferences with i18n methods.
     *
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 23.02.2011
     */
    public function preferences() {
        $this->preferences['submitButtonTitle']         = _t('SilvercartQuickSearchForm.SUBMITBUTTONTITLE');
        $this->preferences['doJsValidationScrolling']   = false;
        $this->formFields['quickSearchQuery']['value']  = _t('SilvercartQuickSearchForm.SEARCHBOXLABEL');
        $this->formFields['quickSearchQuery']['title']  = _t('SilvercartQuickSearchForm.TITLE');

        parent::preferences();
    }
}
