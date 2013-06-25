<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Tasks
 */

/**
 * Task to clean the deep Zend based file cache.
 *
 * @package Silvercart
 * @subpackage Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 21.06.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartCleanCacheTask extends BuildTask {
    
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
     * @param SS_HTTPRequest $request Request
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2013
     */
    public function run($request) {
        if (Member::currentUser() instanceof Member &&
            Member::currentUser()->isAdmin()) {
            foreach (self::$cache_directories as $cacheDirectory) {
                print "Cleaning cache directory '" . $cacheDirectory . "'" . "<br>" . PHP_EOL;
                system("rm -R " . $cacheDirectory);
                print "Done." . "<br>" . PHP_EOL . "<br>" . PHP_EOL;

            }
        } else {
            Director::redirect('/');
        }
    }
    
}