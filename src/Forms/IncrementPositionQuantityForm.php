<?php

namespace SilverCart\Forms;

use SilverCart\Forms\CustomForm;
use SilverCart\Forms\UpdatePositionForm;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\Model\Pages\Page;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FormAction;

/**
 * Increment a cart positions quantity;
 * only a button
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class IncrementPositionQuantityForm extends UpdatePositionForm {
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', Page::singleton()->fieldLabel('IncreaseQuantity'))
                    ->setUseButtonTag(true)->addExtraClass('btn')
            ];
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
     * @since 23.04.2018
     */
    public function doSubmit($data, CustomForm $form) {
        $this->extend('onBeforeDoSubmit', $data, $form);
        if (array_key_exists('PositionID', $data) &&
            is_numeric($data['PositionID'])) {
            //check if the position belongs to this user. Malicious people could manipulate it.
            $member   = Customer::currentUser();
            $position = ShoppingCartPosition::get()->byID($data['PositionID']);
            if ($position instanceof ShoppingCartPosition &&
                $position->exists() &&
                $position->ShoppingCartID == $member->getCart()->ID) {
                $position->Product()->addToCart($member->getCart()->ID, 1, true);
                $backLinkPage = SiteTree::get()->byID($data['BlID']);
                $this->getController()->redirect($backLinkPage->Link());
            }
        }
        $this->extend('onAfterDoSubmit', $data, $form);
    }
    
}

