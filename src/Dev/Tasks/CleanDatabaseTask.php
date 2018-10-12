<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Admin\Model\Config;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Product\AvailabilityStatus;
use SilverCart\Model\Product\Product;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Group;

/**
 * Provides a task to remove no more needed objects out of the database.
 * 
 * @package SilverCart
 * @subpackage Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2018 pixeltricks GmbH
 * @since 05.09.2018
 * @license see license file in modules root directory
 */
class CleanDatabaseTask extends BuildTask
{
    use \SilverCart\Dev\CLITask;
    
    /**
     * Object lifetime in days.
     *
     * @var int
     */
    private static $object_lifetime = 40;
    /**
     * Set a custom url segment (to follow dev/tasks/)
     *
     * @var string
     */
    private static $segment = 'sc-clean-database';
    /**
     * Shown in the overview on the {@link TaskRunner}.
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     * 
     * @var string
     */
    protected $title = 'Clean Shop Database Task';
    /**
     * Describe the implications the task has, and the changes it makes. Accepts 
     * HTML formatting.
     * 
     * @var string
     */
    protected $description = 'Task to remove no more needed objects (like anonymous customer data, empty shopping carts, ...) out of the SilverCart shop database.';
    
    /**
     * Runs this task.
     * 
     * @param \SilverStripe\Control\HTTPRequest $request Request
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2018
     */
    public function run($request)
    {
        $this->deleteAnonymousCustomers();
        $this->printInfo("");
        $this->deleteDeadManyManyRelations();
        $this->printInfo("");
        $this->updateAvailabilityByStock();
        $this->printInfo("");
    }
    
    /**
     * Removes anonymous customers out of database.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2018
     */
    protected function deleteAnonymousCustomers()
    {
        $this->extend('onBeforeDeleteAnonymousCustomers');
        $maxDate            = date('Y-m-d H:i:s', time() - (self::config()->get('object_lifetime')*24*60*60));
        $anonymousGroup     = Group::get()->filter('Code', Customer::GROUP_CODE_ANONYMOUS)->first();
        $anonymousCustomers = $anonymousGroup->Members()->where("LastEdited < '{$maxDate}'");
        $count              = $anonymousCustomers->count();
        $this->printInfo("Deleting all anonymous customers created before {$maxDate}...");
        $anonymousCustomers->removeAll();
        $this->printInfo("\t• Removed {$count} anonymous customers out of database...");
        $this->extend('onAfterDeleteAnonymousCustomers');
    }
    
    /**
     * Removes dead many many relations out of database.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2018
     */
    protected function deleteDeadManyManyRelations()
    {
        $this->extend('onBeforeDeleteDeadManyManyRelations');
        $this->printInfo("Deleting all dead many many relations...");
        $this->deleteDeadManyManyRelationsFor('Group', 'Member');
        $this->extend('onAfterDeleteDeadManyManyRelations');
    }
    
    /**
     * Removes dead many many relations for the given object/ralation data out 
     * of database.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.09.2018
     */
    protected function deleteDeadManyManyRelationsFor($baseObjectName, $relationObjectName, $relationName = null)
    {
        if (is_null($relationName)) {
            $relationName = "{$relationObjectName}s";
        }
        $tableName   = "{$baseObjectName}_{$relationName}";
        $where       = "{$relationObjectName}ID NOT IN (SELECT RelationObject.ID FROM {$relationObjectName} AS RelationObject)";
        $countQuery  = "SELECT COUNT(ID) AS Total FROM {$tableName} WHERE {$where}";
        $deleteQuery = "DELETE FROM {$tableName} WHERE {$where}";
        
        $result = DB::query($countQuery);
        DB::query($deleteQuery);
        $total = $result->first()['Total'];
        $this->printInfo("\t• Deleted a total of {$total} dead {$baseObjectName}->{$relationName} relations.");
    }
    
    /**
     * Updates the availability status of products with a stock of <= 0 if
     * stock management is enabled, the products stock is not overbookable and
     * the products availability status is not the status with the 
     * 'SetForNegativeStock' property.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 12.10.2018
     */
    protected function updateAvailabilityByStock()
    {
        if (Config::EnableStockManagement()) {
            $status = AvailabilityStatus::get()->filter('SetForNegativeStock', true)->first();
            if ($status instanceof AvailabilityStatus) {
                $ccc = self::$CLI_COLOR_CHANGE_CYAN;
                $ccg = self::$CLI_COLOR_CHANGE_GREEN;
                $ccr = self::$CLI_COLOR_CHANGE_RED;
                $ccy = self::$CLI_COLOR_CHANGE_YELLOW;
                $this->printInfo("Updating availability status for products with a stock <= 0 to \"{$ccg}{$status->Title}{$ccy}\"...");
                $products = Product::get()
                        ->exclude("AvailabilityStatusID", $status->ID)
                        ->where("StockQuantity <= 0");
                $currentIndex      = 0;
                $totalProductCount = $products->count();
                $this->printInfo(" • Found {$totalProductCount} matching products.");
                foreach ($products as $product) {
                    /* @var $product Product */
                    $currentIndex++;
                    if ($product->isStockQuantityOverbookable()) {
                        $this->printInfo("\t [{$this->getXofY($currentIndex, $totalProductCount)}] Product #{$product->ID} [SKU#{$product->ProductNumberShop}] is overbookable, skip.", self::$CLI_COLOR_MAGENTA);
                        continue;
                    }
                    $this->printInfo("\t [{$ccc}{$this->getXofY($currentIndex, $totalProductCount)}{$ccy}] Updating product #{$product->ID} [SKU#{$product->ProductNumberShop}] with a stock of {$ccr}{$product->StockQuantity}{$ccy}.");
                    $product->AvailabilityStatusID = $status->ID;
                    $product->write();
                }
            } else {
                $this->printInfo("No availability status for products with a stock <= 0 found...", self::$CLI_COLOR_RED);
            }
        }
    }
}