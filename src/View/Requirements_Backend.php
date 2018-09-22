<?php

namespace SilverCart\View;

use SilverCart\View\Requirements_Minifier;
use SilverStripe\Assets\File;
use SilverStripe\Dev\Deprecation;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\View\HTML;
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
class Requirements_Backend extends SilverStripeRequirements_Backend
{
    use \SilverStripe\Core\Config\Configurable;
    
    /**
     * Determines whether to force CSS/JS file combination.
     *
     * @var bool
     */
    private static $force_combine_files = false;
    /**
     * Determines whether to force CSS/JS file combination.
     *
     * @var bool
     */
    private static $force_combine_files_async = true;
    /**
     * Use the injected minification service to minify any javascript file passed to {@link combine_files()}.
     *
     * @var bool
     */
    protected $minifyCombinedFiles = true;
    /**
     * Whether or not file headers should be written when combining files
     *
     * @var boolean
     */
    protected $writeHeaderComment = false;
    
    /**
     * Sets the default minifier.
     * 
     * @return $this
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    public function __construct()
    {
        if (Director::isDev()) {
            $this->setMinifyCombinedFiles(false);
            $this->setWriteHeaderComment(true);
        }
        $this->setMinifier(Requirements_Minifier::create());
    }
    
    /**
     * -------------------------------------------------------------------------
     * -------------------------------------------------------------------------
     * 
     * <b>EDIT:</b><br/>
     * Removes the <i>type="application/javascript"</i> HTML attribute from
     * JavaScript tags to provide valid HTML code.
     * 
     * -------------------------------------------------------------------------
     * -------------------------------------------------------------------------
     * 
     * Update the given HTML content with the appropriate include tags for the registered
     * requirements. Needs to receive a valid HTML/XHTML template in the $content parameter,
     * including a head and body tag.
     *
     * @param string $content HTML content that has already been parsed from the $templateFile
     *                        through {@link SSViewer}
     * 
     * @return string HTML content augmented with the requirements tags
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 30.08.2018
     */
    public function includeInHTML($content)
    {
        if (self::config()->get('force_combine_files')) {
            $this->forceCombineFiles();
        }
        if (func_num_args() > 1) {
            Deprecation::notice(
                '5.0',
                '$templateFile argument is deprecated. includeInHTML takes a sole $content parameter now.'
            );
            $content = func_get_arg(1);
        }

        // Skip if content isn't injectable, or there is nothing to inject
        $tagsAvailable = preg_match('#</head\b#', $content);
        $hasFiles = $this->css || $this->javascript || $this->customCSS || $this->customScript || $this->customHeadTags;
        if (!$tagsAvailable || !$hasFiles) {
            return $content;
        }
        $requirements = '';
        $jsRequirements = '';

        // Combine files - updates $this->javascript and $this->css
        $this->processCombinedFiles();

        // Script tags for js links
        foreach ($this->getJavascript() as $file => $attributes) {
            // Build html attributes
            $htmlAttributes = [
                'src' => $this->pathForFile($file),
            ];
            if (isset($attributes['type'])) {
                $htmlAttributes['type'] = $attributes['type'];
            }
            if (!empty($attributes['async'])) {
                $htmlAttributes['async'] = 'async';
            }
            if (!empty($attributes['defer'])) {
                $htmlAttributes['defer'] = 'defer';
            }
            $jsRequirements .= HTML::createTag('script', $htmlAttributes);
            $jsRequirements .= "\n";
        }

        // Add all inline JavaScript *after* including external files they might rely on
        foreach ($this->getCustomScripts() as $script) {
            $jsRequirements .= HTML::createTag('script', [], "//<![CDATA[\n{$script}\n//]]>");
            $jsRequirements .= "\n";
        }

        // CSS file links
        foreach ($this->getCSS() as $file => $params) {
            $htmlAttributes = [
                'rel' => 'stylesheet',
                'type' => 'text/css',
                'href' => $this->pathForFile($file),
            ];
            if (!empty($params['media'])) {
                $htmlAttributes['media'] = $params['media'];
            }
            $requirements .= HTML::createTag('link', $htmlAttributes);
            $requirements .= "\n";
        }

        // Literal custom CSS content
        foreach ($this->getCustomCSS() as $css) {
            $requirements .= HTML::createTag('style', ['type' => 'text/css'], "\n{$css}\n");
            $requirements .= "\n";
        }

        foreach ($this->getCustomHeadTags() as $customHeadTag) {
            $requirements .= "{$customHeadTag}\n";
        }

        // Inject CSS  into body
        $content = $this->insertTagsIntoHead($requirements, $content);

        // Inject scripts
        if ($this->getForceJSToBottom()) {
            $content = $this->insertScriptsAtBottom($jsRequirements, $content);
        } elseif ($this->getWriteJavascriptToBody()) {
            $content = $this->insertScriptsIntoBody($jsRequirements, $content);
        } else {
            $content = $this->insertTagsIntoHead($jsRequirements, $content);
        }
        return $content;
    }
    
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
    public function combineFiles($combinedFileName, $files, $options = [])
    {
        parent::combineFiles($combinedFileName, $files, $options);
        foreach ($this->combinedFiles as $combinedFileName => $combinedFileProperties) {
            if (array_key_exists('files', $combinedFileProperties)
             && is_array($combinedFileProperties['files'])
            ) {
                foreach ($combinedFileProperties['files'] as $index => $file) {
                    $realFile = ModuleResourceLoader::singleton()->resolvePath($file);
                    $this->combinedFiles[$combinedFileName]['files'][$index] = $realFile;
                }
            }
        }
    }

    /**
     * Given a set of files, combine them (as necessary) and return the url
     *
     * @param string $combinedFile Filename for this combined file
     * @param array $fileList List of files to combine
     * @param string $type Either 'js' or 'css'
     * @return string|null URL to this resource, if there are files to combine
     * @throws Exception
     */
    protected function getCombinedFileURL($combinedFile, $fileList, $type)
    {
        // Skip empty lists
        if (empty($fileList)) {
            return null;
        }

        // Generate path (Filename)
        $hashQuerystring = Config::inst()->get(static::class, 'combine_hash_querystring');
        if (!$hashQuerystring) {
            $combinedFile = $this->hashedCombinedFilename($combinedFile, $fileList);
        }
        $combinedFileID = File::join_paths($this->getCombinedFilesFolder(), $combinedFile);

        // Send file combination request to the backend, with an optional callback to perform regeneration
        $minify = $this->getMinifyCombinedFiles();
        if ($minify && !$this->minifier) {
            throw new Exception(
                sprintf(
                    <<<MESSAGE
Cannot minify files without a minification service defined.
Set %s::minifyCombinedFiles to false, or inject a %s service on
%s.properties.minifier
MESSAGE
                    ,
                    __CLASS__,
                    Requirements_Minifier::class,
                    __CLASS__
                )
            );
        }

        $combinedURL = $this
            ->getAssetHandler()
            ->getContentURL(
                $combinedFileID,
                function () use ($fileList, $minify, $type, $combinedFile) {
                    // Physically combine all file content
                    $combinedData = '';
                    foreach ($fileList as $file) {
                        $filePath = Director::getAbsFile($file);
                        if (!file_exists($filePath)) {
                            throw new InvalidArgumentException("Combined file {$file} does not exist");
                        }
                        $fileContent = file_get_contents($filePath);
                        // Use configured minifier
                        if ($minify) {
                            $fileContent = $this->minifier->minify($fileContent, $type, $file);
                        }

                        if ($this->writeHeaderComment) {
                            // Write a header comment for each file for easier identification and debugging.
                            $combinedData .= "/****** FILE: $file *****/\n";
                        }
                        $combinedData .= $fileContent . "\n";
                    }
                    if ($type === "css") {
                        $combinedData = $this->minifier->minify($combinedData, $type, $combinedFile);
                    }
                    return $combinedData;
                }
            );

        // If the name isn't hashed, we will need to append the querystring m= parameter instead
        // Since url won't be automatically suffixed, add it in here
        if ($hashQuerystring && $this->getSuffixRequirements()) {
            $hash = $this->hashOfFiles($fileList);
            $q = stripos($combinedURL, '?') === false ? '?' : '&';
            $combinedURL .= "{$q}m={$hash}";
        }

        return $combinedURL;
    }
    
    /**
     * Combines all CSS and JS files.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    protected function forceCombineFiles()
    {
        $existingFiles = [];
        foreach ($this->combinedFiles as $existingCombinedFilename => $combinedItem) {
            $existingFiles = array_merge(
                    $existingFiles,
                    $combinedItem['files']
            );
        }
        $jsFilesToCombine = [];
        foreach ($this->getJavascript() as $file => $attributes) {
            if (in_array($file, $existingFiles)) {
                continue;
            }
            $jsAttributes = [];
            $keyParts     = [];
            if (isset($attributes['type'])) {
                if (is_null($attributes['type'])) {
                    $keyParts[] = 'default';
                } else {
                    $jsAttributes['type'] = $attributes['type'];
                    $keyParts[]           = $jsAttributes['type'];
                }
            }
            if (Director::isLive()
             && self::config()->get('force_combine_files_async')) {
                $attributes['async'] = true;
            }
            if (!empty($attributes['async'])) {
                $jsAttributes['async'] = $attributes['async'];
                if ($jsAttributes['async']) {
                    $keyParts[] = $jsAttributes['async'];
                }
            }
            if (!empty($attributes['defer'])) {
                $jsAttributes['defer'] = $attributes['defer'];
                if ($jsAttributes['defer']) {
                    $keyParts[] = $jsAttributes['defer'];
                }
            }
            
            $key = implode(' ', $keyParts);
            if (!array_key_exists($key, $jsFilesToCombine)) {
                $jsFilesToCombine[$key] = [
                    'files'      => [],
                    'attributes' => $jsAttributes,
                ];
            }
            $jsFilesToCombine[$key]['files'][] = $file;
        }
        foreach ($jsFilesToCombine as $jsFiles) {
            $files      = $jsFiles['files'];
            $attributes = $jsFiles['attributes'];
            $fileName   = sha1(implode('--', $files)) . ".js";
            $this->combineFiles($fileName, $files, $attributes);
        }
        
        $cssFilesToCombine = [];
        foreach ($this->getCSS() as $file => $params) {
            if (in_array($file, $existingFiles)) {
                continue;
            }
            $media = "default";
            if (!empty($params['media'])) {
                $media = $params['media'];
            }
            if (!array_key_exists($media, $cssFilesToCombine)) {
                $cssFilesToCombine[$media] = [];
            }
            $cssFilesToCombine[$media][] = $file;
        }
        foreach ($cssFilesToCombine as $media => $files) {
            $fileName = sha1(implode('--', $files)) . ".css";
            $options  = [];
            if ($media !== "default") {
                $options['media'] = $media;
            }
            $this->combineFiles($fileName, $files, $options);
        }
    }
}