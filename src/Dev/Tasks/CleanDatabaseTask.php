<?php

namespace SilverCart\Dev\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverCart\Model\Customer\Customer;
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
}