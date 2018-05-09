<?php

namespace SilverCart\View;

use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\View\Requirements_Backend as SilverStripeRequirements_Backend;

/**
 * Requirements Backend.
 * 
 * Fixes a bug with combined files.
 * <code>
 * // this code should be used when combining files in live mode, but it isn't.
 * ModuleResourceLoader::singleton()->resolvePath($file);
 * </code>
 * 
 * @package SilverCart
 * @subpackage jtl_Connector_SilverCart_Mapper
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2018 pixeltricks GmbH
 * @since 08.05.2018
 * @license see license file in modules root directory
 */
class Requirements_Backend extends SilverStripeRequirements_Backend {
    
    /**
     * -------------------------------------------------------------------------
     * -------------------------------------------------------------------------
     * 
     * <b>BUGFIX:</b><br/>
     * Uses <i>ModuleResourceLoader::singleton()->resolvePath($file)</i> to fix 
     * a file path issue with combined files in live mode.
     * 
     * -------------------------------------------------------------------------
     * -------------------------------------------------------------------------
     * 
     * Concatenate several css or javascript files into a single dynamically generated file. This
     * increases performance by fewer HTTP requests.
     *
     * The combined file is regenerated based on every file modification time. Optionally a
     * rebuild can be triggered by appending ?flush=1 to the URL.
     *
     * All combined files will have a comment on the start of each concatenated file denoting their
     * original position.
     *
     * CAUTION: You're responsible for ensuring that the load order for combined files is
     * retained - otherwise combining JavaScript files can lead to functional errors in the
     * JavaScript logic, and combining CSS can lead to incorrect inheritance. You can also
     * only include each file once across all includes and combinations in a single page load.
     *
     * CAUTION: Combining CSS Files discards any "media" information.
     *
     * Example for combined JavaScript:
     * <code>
     * Requirements::combine_files(
     *    'foobar.js',
     *    array(
     *        'mysite/javascript/foo.js',
     *        'mysite/javascript/bar.js',
     *    ),
     *    array(
     *        'async' => true,
     *        'defer' => true,
     *    )
     * );
     * </code>
     *
     * Example for combined CSS:
     * <code>
     * Requirements::combine_files(
     *    'foobar.css',
     *    array(
     *        'mysite/javascript/foo.css',
     *        'mysite/javascript/bar.css',
     *    ),
     *    array(
     *        'media' => 'print',
     *    )
     * );
     * </code>
     * 
     * @param string $combinedFileName Filename of the combined file relative to docroot
     * @param array  $files            Array of filenames relative to docroot
     * @param array  $options          Array of options for combining files. Available options are:
     * - 'media' : If including CSS Files, you can specify a media type
     * - 'async' : If including JavaScript Files, boolean value to set async attribute to script tag
     * - 'defer' : If including JavaScript Files, boolean value to set defer attribute to script tag
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.05.2018
     * @see parent::combineFiles()
     */
    public function combineFiles($combinedFileName, $files, $options = array()) {
        parent::combineFiles($combinedFileName, $files, $options);
        foreach ($this->combinedFiles as $combinedFileName => $combinedFileProperties) {
            if (array_key_exists('files', $combinedFileProperties) &&
                is_array($combinedFileProperties['files'])) {
                foreach ($combinedFileProperties['files'] as $index => $file) {
                    $realFile = ModuleResourceLoader::singleton()->resolvePath($file);
                    $this->combinedFiles[$combinedFileName]['files'][$index] = $realFile;
                }
            }
        }
    }
    
}