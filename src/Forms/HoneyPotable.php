<?php

namespace SilverCart\Forms;

use Heyday\SilverStripe\HoneyPot\HoneyPotField;

/**
 * Trait to add the honey pot feature to a form.
 * 
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.02.2022
 * @copyright 2022 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @mixin CustomForm
 */
trait HoneyPotable
{
    /**
     * HoneyPotField
     * 
     * @var HoneyPotField|null
     */
    protected $honeyPotField = null;
    
    /**
     * Returns the HoneyPot related form fields.
     * 
     * @return array
     */
    protected function getHoneyPotFields() : array
    {
        $fields = [];
        if ($this->EnableHoneyPot()) {
            $fields[] = $this->getHoneyPotField();
        }
        return $fields;
    }
    
    /**
     * Returns the HoneyPot related form fields.
     * 
     * @return array
     */
    protected function getHoneyPotField() : ?HoneyPotField
    {
        if ($this->honeyPotField === null
         && $this->EnableHoneyPot()
        ) {
            $fieldName = 'Website';
            if ($this->hasMethod('ContactPage')) {
                $index     = 1;
                while ($this->ContactPage()->FormFields()->filter('Name', $fieldName)->exists()) {
                    $fieldName = "{$fieldName}-{$index}";
                    $index++;
                }
            }
            $this->honeyPotField = HoneyPotField::create($fieldName);
        }
        return $this->honeyPotField;
    }
    
    /**
     * Returns whether HoneyPot is enabled.
     * 
     * @return bool
     */
    public function EnableHoneyPot() : bool
    {
        return class_exists(HoneyPotField::class);
    }
}