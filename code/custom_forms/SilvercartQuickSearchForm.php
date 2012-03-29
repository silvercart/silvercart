<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Forms
 */

/**
 * form definition
 *
 * @package Silvercart
 * @subpackage Forms
 * @copyright pixeltricks GmbH
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @since 23.10.2010
 */
class SilvercartQuickSearchForm extends CustomHtmlForm {

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
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Oliver Scheer <oscheer@pixeltricks.de>
     * @since 23.10.2010
     */
    protected function submitSuccess($data, $form, $formData) {
        Session::set("searchQuery", $formData['quickSearchQuery']);
        $searchResultsPage = SilvercartPage_Controller::PageByIdentifierCode("SilvercartSearchResultsPage");
        Director::redirect($searchResultsPage->RelativeLink());
    }

    /**
     * Set texts for preferences with i18n methods.
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 23.02.2011
     * @return void
     */
    public function preferences() {
        $this->preferences['submitButtonTitle']         = _t('SilvercartQuickSearchForm.SUBMITBUTTONTITLE');
        $this->preferences['doJsValidationScrolling']   = false;
        $this->formFields['quickSearchQuery']['value']  = _t('SilvercartQuickSearchForm.SEARCHBOXLABEL');
        $this->formFields['quickSearchQuery']['title']  = _t('SilvercartQuickSearchForm.TITLE');

        parent::preferences();
    }
}
