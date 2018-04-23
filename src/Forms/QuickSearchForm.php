<?php

namespace SilverCart\Forms;

use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use Translatable;

/**
 * form definition.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class QuickSearchForm extends CustomForm {
    
    /**
     * Custom form action path, if not linking to itself.
     * E.g. could be used to post to an external link
     *
     * @var string
     */
    protected $formActionPath = 'sc-action/doSearch';
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'quickSearch',
    ];
    
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $fields += [
                HiddenField::create('locale', 'locale', Translatable::get_current_locale()),
                TextField::create('quickSearchQuery', '', '', 30)
            ];
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
            $actions += [
                FormAction::create('dosearch', _t(QuickSearchForm::class . '.SUBMITBUTTONTITLE', 'Search'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
}
