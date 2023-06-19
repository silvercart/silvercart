<?php

namespace SilverCart\Services;

use SilverCart\Admin\Model\Config;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Product\AvailabilityStatus;
use SilverCart\Model\Product\Product;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Group;

/**
 * Provides a service to remove no more needed objects out of the database.
 * 
 * @package SilverCart
 * @subpackage Services
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2023 pixeltricks GmbH
 * @since 13.06.2023
 * @license see license file in modules root directory
 */
class CleanDatabaseService extends Service
{
    /**
     * Object lifetime in days.
     *
     * @var int
     */
    private static $object_lifetime = 40;
    
    /**
     * Runs this task.
     * 
     * @return void
     */
    public function run() : void
    {
        $this->deleteAnonymousCustomers();
        $this->deleteDeadManyManyRelations();
        $this->updateAvailabilityByStock();
    }
    
    /**
     * Removes anonymous customers out of database.
     * 
     * @return void
     */
    protected function deleteAnonymousCustomers() : void
    {
        $this->setCurrentStep(1);
        $this->extend('onBeforeDeleteAnonymousCustomers');
        $maxDate            = date('Y-m-d H:i:s', time() - (self::config()->get('object_lifetime')*24*60*60));
        $anonymousGroup     = Group::get()->filter('Code', Customer::GROUP_CODE_ANONYMOUS)->first();
        $anonymousCustomers = $anonymousGroup->Members()->where("LastEdited < '{$maxDate}'");
        $count              = $anonymousCustomers->count();
        $this->addMessage("Deleting all anonymous customers created before {$maxDate}...");
        $anonymousCustomers->removeAll();
        $this->addMessage("\t• Removed {$count} anonymous customers out of database...");
        $this->extend('onAfterDeleteAnonymousCustomers');
    }
    
    /**
     * Removes dead many many relations out of database.
     * 
     * @return void
     */
    protected function deleteDeadManyManyRelations() : void
    {
        $this->setCurrentStep(2);
        $this->extend('onBeforeDeleteDeadManyManyRelations');
        $this->addMessage("Deleting all dead many many relations...");
        $this->deleteDeadManyManyRelationsFor('Group', 'Member');
        $this->extend('onAfterDeleteDeadManyManyRelations');
    }
    
    /**
     * Removes dead many many relations for the given object/ralation data out 
     * of database.
     * 
     * @return void
     */
    protected function deleteDeadManyManyRelationsFor($baseObjectName, $relationObjectName, $relationName = null) : void
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
        $this->addMessage("\t• Deleted a total of {$total} dead {$baseObjectName}->{$relationName} relations.");
    }
    
    /**
     * Updates the availability status of products with a stock of <= 0 if
     * stock management is enabled, the products stock is not overbookable and
     * the products availability status is not the status with the 
     * 'SetForNegativeStock' property.
     * 
     * @return void
     */
    protected function updateAvailabilityByStock() : void
    {
        $this->setCurrentStep(3);
        if (Config::EnableStockManagement()) {
            $status = AvailabilityStatus::get()->filter('SetForNegativeStock', true)->first();
            if ($status instanceof AvailabilityStatus) {
                $this->addMessage("Updating availability status for products with a stock <= 0 to \"{$status->Title}\"...");
                $products = Product::get()
                        ->exclude("AvailabilityStatusID", $status->ID)
                        ->where("StockQuantity <= 0");
                $currentIndex      = 0;
                $totalProductCount = $products->count();
                $this->addMessage(" • Found {$totalProductCount} matching products.");
                foreach ($products as $product) {
                    /* @var $product Product */
                    $currentIndex++;
                    if ($product->isStockQuantityOverbookable()) {
                        $this->addMessage("\t [{$this->getXofY($currentIndex, $totalProductCount)}] Product #{$product->ID} [SKU#{$product->ProductNumberShop}] is overbookable, skip.", self::$CLI_COLOR_MAGENTA);
                        continue;
                    }
                    $this->addMessage("\t [{$this->getXofY($currentIndex, $totalProductCount)}] Updating product #{$product->ID} [SKU#{$product->ProductNumberShop}] with a stock of {$product->StockQuantity}.");
                    $product->AvailabilityStatusID = $status->ID;
                    $product->write();
                }
            } else {
                $this->addMessage("No availability status for products with a stock <= 0 found...", 'ERROR');
            }
        }
    }
}