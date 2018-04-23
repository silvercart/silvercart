<?php

namespace SilverCart\Forms;

use SilverCart\Forms\CustomForm;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Plugins\Plugin;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\Validator;

/**
 * Base form to update a shopping cart position.
 * Provides the basic form fields and context objects.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class UpdatePositionForm extends CustomForm {
    
    /**
     * Context shopping cart position.
     *
     * @var ShoppingCartPosition
     */
    protected $position = null;

    /**
     * 
     * Create a new form, with the given fields an action buttons.
     *
     * @param ShoppingCartPosition $position   Context shopping cart position.
     * @param RequestHandler       $controller Optional parent request handler
     * @param string               $name       The method on the controller that will return this form object.
     * @param FieldList            $fields     All of the fields in the form - a {@link FieldList} of {@link FormField} objects.
     * @param FieldList            $actions    All of the action buttons in the form - a {@link FieldLis} of {@link FormAction} objects
     * @param Validator            $validator  Override the default validator instance (Default: {@link RequiredFields})
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 03.11.2017
     */
    public function __construct(ShoppingCartPosition $position, RequestHandler $controller = null, $name = self::DEFAULT_NAME, FieldList $fields = null, FieldList $actions = null, Validator $validator = null) {
        $this->setPosition($position);
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $blID = 0;
            $ctrl = $this->getController();
            if ($ctrl->hasMethod('data')) {
                $blID = $ctrl->data()->ID;
            }
            $fields += [
                HiddenField::create('PositionID', 'PositionID', $this->getPosition()->ID),
                HiddenField::create('BlID', 'BlID', $blID),
            ];
            Plugin::call($this, 'updateFormFields', [$fields], true);
        });
        return parent::getCustomFields();
    }

    /**
     * Returns the context shopping cart position.
     * 
     * @return type
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * Sets the context shopping cart position.
     * 
     * @param ShoppingCartPosition $position
     * 
     * @return void
     */
    public function setPosition(ShoppingCartPosition $position) {
        $this->position = $position;
    }
    
}
