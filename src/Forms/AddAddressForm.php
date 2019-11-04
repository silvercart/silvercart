<?php

namespace SilverCart\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Forms\AddressForm;
use SilverCart\Forms\CustomForm;
use SilverCart\Model\Customer\Address;
use SilverCart\Model\Customer\Country;
use SilverCart\Model\Pages\AddressHolder;
use SilverCart\Model\Pages\Page;
use SilverStripe\Security\Member;

/** 
 * Customer form for adding an address.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class AddAddressForm extends AddressForm
{
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-vertical',
    ];

    /**
     * This method will be call if there are no validation error
     *
     * @param array      $data Submitted data
     * @param CustomForm $form Form object
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2017
     */
    public function doSubmit($data, CustomForm $form)
    {
        $member = $this->getCustomer();
        if ($member instanceof Member
         && $member->exists()
        ) {
            $country = Country::get()->byID($data['Country']);
            if ($country) {
                $data['CountryID'] = $country->ID;
            }
            $data['MemberID'] = $member->ID;
            
            $address = Address::create();
            $address->write();
            $address->update($data);
            $address->write();
            
            $redirectTo = Tools::PageByIdentifierCode(Page::IDENTIFIER_ADDRESS_HOLDER)->Link();
            if (!empty($data['redirect'])) {
                $redirectTo = $data['redirect'];
            }
            $this->getController()->redirect($redirectTo);
            $this->getController()->setSuccessMessage(_t(AddressHolder::class . '.ADDED_ADDRESS_SUCCESS', 'Your address was successfully saved.'));
        }
    }
}