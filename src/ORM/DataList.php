<?php

namespace SilverCart\ORM;

use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\DataList as SilverStripeDataList;

/**
 * Alternative DataList to provide optional non-linear (inter relation) sorting
 * of DataObjects.
 *
 * @package SilverCart
 * @subpackage ORM
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.06.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DataList extends SilverStripeDataList
{
    /**
     * Determines whether to sort linear or not.
     *
     * @var bool
     */
    public static $do_linear_sort = true;
    
    /**
     * Sets whether to sort linear or not.
     * 
     * @param bool $do_linear_sort Sort linear?
     * 
     * @return void
     */
    public static function set_do_linear_sort($do_linear_sort) : void
    {
        self::$do_linear_sort = $do_linear_sort;
    }
    
    /**
     * Returns whether to sort linear or not.
     * 
     * @return bool
     */
    public static function get_do_linear_sort() : bool
    {
        return self::$do_linear_sort;
    }
    
    /**
     * Returns whether to sort linear or not.
     * Alias for self::get_do_linear_sort().
     * 
     * @return bool
     */
    public static function do_linear_sort() : bool
    {
        return self::get_do_linear_sort();
    }

    /**
     * Adds the optional non-linear sort.
     * 
     * @return $this
     * 
     * @throws InvalidArgumentException
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2018
     */
    public function sort() {
        $count = func_num_args();

        if ($count == 0) {
            return $this;
        }

        if ($count > 2) {
            throw new InvalidArgumentException('This method takes zero, one or two arguments');
        }

        if ($count == 2) {
            $col = null;
            $dir = null;
            list($col, $dir) = func_get_args();

            // Validate direction
            if (!in_array(strtolower($dir), ['desc', 'asc'])) {
                user_error('Second argument to sort must be either ASC or DESC');
            }

            $sort = [$col => $dir];
        } else {
            $sort = func_get_arg(0);
        }

        $linearOnly = DataList::do_linear_sort();
        return $this->alterDataQuery(function (DataQuery $query, DataList $list) use ($sort, $linearOnly) {

            if (is_string($sort) && $sort) {
                if (false !== stripos($sort, ' asc') || false !== stripos($sort, ' desc')) {
                    $query->sort($sort);
                } else {
                    $list->applyRelation($sort, $column, true);
                    $query->sort($column, 'ASC');
                }
            } elseif (is_array($sort)) {
                // sort(array('Name'=>'desc'));
                $query->sort(null, null); // wipe the sort

                foreach ($sort as $column => $direction) {
                    // Convert column expressions to SQL fragment, while still allowing the passing of raw SQL
                    // fragments.
                    $list->applyRelation($column, $relationColumn, $linearOnly);
                    $query->sort($relationColumn, $direction, false);
                }
            }
        });
    }
}