<?php

namespace SilverCart\Model;

use SilverCart\Dev\Tools;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderPosition;
use SilverCart\Model\Order\ShoppingCart;
use SilverCart\Model\Order\ShoppingCartPosition;
use SilverCart\ORM\ExtensibleDataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use function _t;
use function singleton;

/**
 * Custom data value.
 * 
 * @package SilverCart
 * @subpackage Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.03.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $DataName  Data Name
 * @property string $DataType  Data Type
 * @property string $DataTitle Data Title
 * @property string $DataValue Data Value
 * 
 * @method ShoppingCart         ShoppingCart() Returns the related ShoppingCart.
 * @method ShoppingCartPosition ShoppingCartPosition() Returns the related ShoppingCartPosition.
 * @method Order                Order() Returns the related Order.
 * @method OrderPosition        OrderPosition() Returns the related OrderPosition.
 */
class DataValue extends DataObject
{
    use ExtensibleDataObject;
    
    public const TYPE_BOOL    = 'boolean';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_FLOAT   = 'float';
    public const TYPE_INT     = 'int';
    public const TYPE_INTEGER = 'int';
    public const TYPE_STRING  = 'string';

    /**
     * DB table name
     *
     * @var string
     */
    private static string $table_name = 'SilverCart_DataValue';
    /**
     * DB attributes.
     * 
     * @var string[]
     */
    private static array $db = [
        'DataName'  => 'Varchar',
        'DataType'  => 'Varchar',
        'DataTitle' => 'Varchar',
        'DataValue' => 'Text',
    ];
    /**
     * Has one relations.
     * 
     * @var string[]
     */
    private static array $has_one = [
        'ShoppingCart'         => ShoppingCart::class,
        'ShoppingCartPosition' => ShoppingCartPosition::class,
        'Order'                => Order::class,
        'OrderPosition'        => OrderPosition::class,
    ];
    /**
     * Casted fields.
     * 
     * @var string[]
     */
    private static array $casting = [
        'CastedDataValue' => 'Varchar',
    ];
    /**
     * Summary fields.
     * 
     * @var string[]
     */
    private static array $summary_fields = [
        'DataTitle',
        'DataValueNice',
        'DataTypeNice',
    ];
    
    /**
     * Field labels for display in tables.
     *
     * @param bool $includerelations Include relations?
     *
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, []);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the human readable DataType.
     * 
     * @return string
     */
    public function getDataTypeNice() : string
    {
        $type = $this->getField('DataType');
        if (empty($type)) {
            $type = '---';
        }
        $nice = _t(self::class . ".DataType_{$this->getField('DataType')}", $type);
        $this->extend('updateDataTypeNice', $nice);
        return (string) $nice;
    }
    
    /**
     * Returns the casted DataValue.
     * 
     * @return mixed
     */
    public function getCastedDataValue()
    {
        $casted = '';
        $type   = $this->getField('DataType');
        switch ($type) {
            case class_exists($type):
                $casted = DataObject::get_by_id($type, (int) $this->getField('DataValue'));
                break;
            case self::TYPE_BOOLEAN:
                $casted = $this->getField('DataValue') === '1'
                     || $this->getField('DataValue') === 'true';
                break;
            case self::TYPE_FLOAT:
                $casted = (float) $this->getField('DataValue');
                break;
            case self::TYPE_INTEGER:
                $casted = (int) $this->getField('DataValue');
                break;
            case self::TYPE_STRING:
            default:
                $casted = (string) $this->getField('DataValue');
                break;
        }
        $this->extend('updateCastedDataValue', $casted);
        return $casted;
    }
    
    /**
     * Returns the human readable DataValue.
     * 
     * @return string
     */
    public function getDataValueNice() : string
    {
        $nice = '';
        $type = $this->getField('DataType');
        switch ($type) {
            case null:
                $nice = '';
                break;
            case class_exists($type):
                $object = singleton($type);
                $nice = '';
                break;
            case self::TYPE_BOOLEAN:
                $nice = $this->getField('DataValue') === '1'
                     || $this->getField('DataValue') === 'true' ? Tools::field_label('Yes') : Tools::field_label('No');
                break;
            case self::TYPE_FLOAT:
            case self::TYPE_INTEGER:
            case self::TYPE_STRING:
            default:
                $nice = $this->getField('DataValue');
                break;
        }
        $this->extend('updateDataValueNice', $nice);
        return (string) $nice;
    }
}