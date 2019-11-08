<?php

namespace SilverCart\ORM\FieldType;

use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBField;

/**
 * DB field type LONGBLOB to store binary data with a size of up to 4,294,967,295
 * bytes.
 *
 * @package SilverCart
 * @subpackage ORM\FieldType
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.06.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DBLongBlob extends DBField
{
    /**
     * Requires the DB field data.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2019
     */
    public function requireField() : void
    {
        DB::require_field($this->tableName, $this->name, "longblob");
    }
}