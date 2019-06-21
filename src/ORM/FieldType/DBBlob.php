<?php

namespace SilverCart\ORM\FieldType;

use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBField;

/**
 * DB field type BLOB to store binary data.
 *
 * @package SilverCart
 * @subpackage ORM\FieldType
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.06.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DBBlob extends DBField
{
    /**
     * Requires the BD field data.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2019
     */
    public function requireField() : void
    {
        DB::require_field($this->tableName, $this->name, "blob");
    }
}