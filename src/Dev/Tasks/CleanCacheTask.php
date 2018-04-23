<?php

namespace SilverCart\Dev\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * Task to clean the deep Zend based file cache.
 *
 * @package SilverCart
 * @subpackage Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CleanCacheTask extends BuildTask {
    
    /**
     * List of cleanable cache directories.
     *
     * @var array
     */
    protected static $cache_directories = array();

    /**
     * Description of this task
     * 
     * @var string 
     */
    protected $description = 'Deletes all Zend-Cache based cache files.';

    /**
     * Registers a cache directory to clean by this task.
     * 
     * @param string $cacheDirectory Absolute path cache directory to clean
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2013
     */
    public static function register_cache_directory($cacheDirectory) {
        if (!in_array($cacheDirectory, self::$cache_directories)) {
            self::$cache_directories[] = $cacheDirectory;
        }
    }


    /**
     * Executes the task by calling it by URL.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.06.2014
     */
    public function run($request) {
        if (Security::getCurrentUser() instanceof Member &&
            Security::getCurrentUser()->isAdmin()) {
            foreach (self::$cache_directories as $cacheDirectory) {
                print "Cleaning cache directory '" . $cacheDirectory . "'" . "<br>" . PHP_EOL;
                system("rm -R " . $cacheDirectory);
                print "Done." . "<br>" . PHP_EOL . "<br>" . PHP_EOL;

            }
        } else {
            Controller::curr()->redirect('/');
        }
    }
    
}