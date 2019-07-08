<?php

namespace SilverCart\Model\Customer;

use SilverCart\Model\Customer\Customer;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Group;

/**
 * Validator for Customers.
 *
 * @package SilverCart
 * @subpackage Model_Customer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CustomerValidator extends Extension
{
    /**
     * Return TRUE if a method exists on this object
     *
     * @param string $method Method to check
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public function hasMethod(string $method) : bool
    {
        return method_exists($this, $method);
    }
    
    /**
     * validate form data
     *
     * @param array $data Data to validate
     * @param Form  $form Form
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>, Ramon Kupper <rkupper@pixeltricks.de>
     * @since 08.07.2019
     */
    public function updatePHP(array $data, $form) : bool
    {
        $valid = true;
        $groups = $data['DirectGroups'];
        if (!empty($groups)) {
            if (is_array($groups)) {
                $groups = implode(',', $groups);
            }
            $groupObjects = Group::get()->where("\"Group\".\"ID\" IN ({$groups})");
            $pricetypes   = [];
            foreach ($groupObjects as $group) {
                if (!empty($group->Pricetype)
                 && $group->Pricetype != '---'
                ) {
                    $pricetypes[$group->Pricetype] = true;
                }
            }
            if (count($pricetypes) > 1) {
                $form->getValidator()->validationError(
                        'Groups',
                        _t(Customer::class . '.ERROR_MULTIPLE_PRICETYPES', 'Customer groups with different pricetypes are invalid!'),
                        'bad'
                );
                $valid = false;
            }
        }
        return $valid;
    }
}