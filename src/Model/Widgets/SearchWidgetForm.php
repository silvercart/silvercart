<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Control\ActionHandler;
use SilverCart\Forms\FormFields\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;

/**
 * SearchWidget Form.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchWidgetForm extends CustomForm {
    
    /**
     * Custom form action path, if not linking to itself.
     * E.g. could be used to post to an external link
     *
     * @var string
     */
    protected $formActionPath = 'sc-action/doSearch';
    
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
    
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = array(
        'quickSearchQuery',
    );

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $fields += array(
                HiddenField::create('locale', 'locale', Tools::current_locale()),
                TextField::create('quickSearchQuery', $this->fieldLabel('SEARCHLABEL')),
            );
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += array(
                FormAction::create('submit', $this->fieldLabel('SUBMITBUTTONTITLE'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            );
        });
        return parent::getCustomActions();
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        $handler = new ActionHandler();
        $handler->doSearch($this->getController()->getRequest());
    }
    
}
