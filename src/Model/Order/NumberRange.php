<?php

namespace SilverCart\Model\Order;

use SilverCart\Dev\Tools;
use SilverCart\ORM\DataObjectExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;

/**
 * Abstract for a range of numbers (ordernumbers, customernumbers, invoicenumbers, etc.).
 *
 * @package SilverCart
 * @subpackage Model_Order
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class NumberRange extends DataObject
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'Title'       => 'Varchar(32)',
        'Identifier'  => 'Varchar(32)',
        'Prefix'      => 'Varchar(32)',
        'Suffix'      => 'Varchar(32)',
        'StartCount'  => 'Int(16)',
        'EndCount'    => 'Int(16)',
        'ActualCount' => 'Int(16)',
    ];
    /**
     * default values for some attributes
     *
     * @var array
     */
    private static $defaults = [
        'StartCount'    => '100000',
        'EndCount'      => '999999',
        'ActualCount'   => '100000',
    ];
    /**
     * some virtual attributes (not in db)
     *
     * @var array
     */
    private static $casting = [
        'ActualNumber' => 'Varchar(80)',
        'EndNumber'    => 'Varchar(80)',
        'StartNumber'  => 'Varchar(80)',
    ];
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartNumberRange';
    /**
     * Enable table lock when getting new numbers?
     *
     * @var bool
     */
    private static $enable_table_lock = true;
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this); 
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Title'       => _t(self::class . '.TITLE', 'Title'),
            'Identifier'  => _t(self::class . '.IDENTIFIER', 'Identifier'),
            'Prefix'      => _t(self::class . '.PREFIX', 'Prefix'),
            'Suffix'      => _t(self::class . '.SUFFIX', 'Suffix'),
            'StartCount'  => _t(self::class . '.STARTCOUNT', 'Start'),
            'EndCount'    => _t(self::class . '.ENDCOUNT', 'End'),
            'ActualCount' => _t(self::class . '.ACTUALCOUNT', 'Actual Count'),
        ]);
    }
    
    /**
     * i18n for summary fields
     *
     * @return array
     */
    public function summaryFields() : array
    {
        return [
            'Title'       => $this->fieldLabel('Title'),
            'StartCount'  => $this->fieldLabel('StartCount'),
            'EndCount'    => $this->fieldLabel('EndCount'),
            'ActualCount' => $this->fieldLabel('ActualCount'),
        ];
    }
    
    /**
     * Returns an array of field/relation names (db, has_one, has_many, 
     * many_many, belongs_many_many) to exclude from form scaffolding in
     * backend.
     * This is a performance friendly way to exclude fields.
     * 
     * @return array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.02.2013
     */
    public function excludeFromScaffolding() : array
    {
        $excludeFromScaffolding = [
            'Identifier'
        ];
        $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);
        return $excludeFromScaffolding;
    }

    /**
     * customizes the backends fields
     *
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            if (!empty($this->Identifier)) {
                $fields->makeFieldReadonly('ActualCount');
            }
        });
        return DataObjectExtension::getCMSFields($this);
    }

    /**
     * Remove permission to delete for all members.
     *
     * @param Member $member Member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function canDelete($member = null) : bool
    {
        return false;
    }

    /**
     * Builds the actual number with prefix, count and suffix
     *
     * @return string
     */
    public function getActualNumber() : string
    {
        return "{$this->Prefix}{$this->ActualCount}{$this->Suffix}";
    }

    /**
     * Builds the end number with prefix, count and suffix
     *
     * @return string
     */
    public function getEndNumber() : string
    {
        return "{$this->Prefix}{$this->EndCount}{$this->Suffix}";
    }

    /**
     * Builds the start number with prefix, count and suffix
     *
     * @return string
     */
    public function getStartNumber() : string
    {
        return "{$this->Prefix}{$this->StartCount}{$this->Suffix}";
    }

    /**
     * increments and returns the actual number.
     *
     * @return string
     */
    public function getNewNumber() : string
    {
        $table = Tools::get_table_name(NumberRange::class);
        if ($this->config()->enable_table_lock) {
            DB::query("LOCK TABLES " . $table . " WRITE");
        }
        DB::query(
                sprintf(
                        "UPDATE " . $table . " SET ActualCount=ActualCount+1 WHERE Identifier = '%s'",
                        $this->Identifier
                )
        );
        $results = DB::query(
                sprintf(
                        "SELECT ActualCount FROM " . $table . " WHERE Identifier = '%s'",
                        $this->Identifier
                )
        );
        if ($this->config()->enable_table_lock) {
            DB::query("UNLOCK TABLES");
        }
        
        $firstRow          = $results->first();
        $this->ActualCount = $firstRow['ActualCount'];
        return $this->getActualNumber();
    }

    /**
     * reserves a new number or returns an already reserved one.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function reserveNewNumber() : string
    {
        if (!Tools::Session()->get("Reserved{$this->Identifier}")) {
            Tools::Session()->set("Reserved{$this->Identifier}", $this->getNewNumber());
            Tools::saveSession();
        }
        return (string) Tools::Session()->get("Reserved{$this->Identifier}");
    }

    /**
     * returns a reserverd number and deletes the number from session.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public function useReservedNumber() : string
    {
        $reservedNumber = $this->reserveNewNumber();
        Tools::Session()->clear('Reserved' . $this->Identifier);
        Tools::saveSession();
        return $reservedNumber;
    }

    /**
     * returns a number range by identifier.
     *
     * @param string $identifier Identifier of the number range
     *
     * @return NumberRange|null
     */
    public static function getByIdentifier(string $identifier) : ?NumberRange
    {
        return NumberRange::get()->filter('Identifier', $identifier)->first();
    }

    /**
     * reserves a new number or returns an already reserved one by identifier.
     *
     * @param string $identifier Identifier of the number range
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public static function reserveNewNumberByIdentifier(string $identifier) : string
    {
        $number      = '';
        $numberRange = self::getByIdentifier($identifier);
        if ($numberRange instanceof NumberRange) {
            $number = $numberRange->reserveNewNumber();
        }
        return $number;
    }

    /**
     * returns a reserverd number and deletes the number from session by identifier.
     *
     * @param string $identifier Identifier of the number range
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2011
     */
    public static function useReservedNumberByIdentifier(string $identifier) : string
    {
        $number      = '';
        $numberRange = self::getByIdentifier($identifier);
        if ($numberRange) {
            $number = $numberRange->useReservedNumber();
        }
        return $number;
    }
}