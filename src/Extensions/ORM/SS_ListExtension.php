<?php

namespace SilverCart\Extensions\ORM;

use SilverStripe\Core\Extension;

/**
 * Extension for DataList and ArrayList.
 * 
 * @package SilverCart
 * @subpackage Extensions\ORM
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 30.03.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SS_ListExtension extends Extension
{
    /**
     * The object this extension is applied to.
     *
     * @var \SilverStripe\ORM\SS_List
     */
    protected $owner;
    
    /**
     * Implodes the list.
     * 
     * @param string $fieldname Field name to use
     * @param string $glue      Glue string to use
     * @param string $lastGlue  Optional glue string to use to seperate the last 
     *                          item if the list contains more than one item.
     *                          (e.g. use ", " as $glue and " & " as $lastGlue to get
     *                          "first item, second item & third item" out of a 
     *                          list of three items)
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.03.2019
     */
    public function implode(string $fieldname, string $glue, string $lastGlue = '') : string
    {
        $method = null;
        if (strpos($fieldname, '.') !== false) {
            list($fieldname, $method) = explode('.', $fieldname);
        }
        $array = $this->owner->map('ID', $fieldname)->toArray();
        if (!is_null($method)) {
            foreach ($array as $key => $value) {
                $array[$key] = $value->{$method}();
            }
        }
        if (!empty($lastGlue)
         && count($array) > 1
        ) {
            $lastItem = array_pop($array);
            $result   = implode($glue, $array) . "{$lastGlue}{$lastItem}";
        } else {
            $result = implode($glue, $array);
        }
        return $result;
    }
}