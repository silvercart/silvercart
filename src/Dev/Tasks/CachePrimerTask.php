<?php

namespace SilverCart\Dev\Tasks;

use DOMDocument;
use SilverCart\Dev\Tools;
use SilverStripe\Control\Director;
use SilverStripe\Dev\BuildTask;
use SimpleXMLElement;

/**
 * Task to prime a SilverCart based sites cache.
 *
 * @package SilverCart
 * @subpackage Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CachePrimerTask extends BuildTask
{
    use \SilverCart\Dev\CLITask;
    
    /**
     * Set a custom url segment (to follow dev/tasks/)
     *
     * @var string
     */
    private static $segment = 'sc-cache-primer';
    /**
     * Shown in the overview on the {@link TaskRunner}.
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     * 
     * @var string
     */
    protected $title = 'Shop Cache Primer Task';
    /**
     * Describe the implications the task has, and the changes it makes. Accepts 
     * HTML formatting.
     * 
     * @var string
     */
    protected $description = 'Task to create a cached version of every published page available through the Google Sitemap.';
    /**
     * The count of URLs to call
     *
     * @var int
     */
    protected $countOfUrlsToCall = null;
    /**
     * Index to prime cache for
     *
     * @var int
     */
    protected $primeIndex = null;
    /**
     * Sitemap as a SimpleXMLElement
     *
     * @var SimpleXMLElement
     */
    protected $sitemapXml = null;
    /**
     * Count of threads to use
     *
     * @var int
     */
    protected $threads = null;
    /**
     * URL to prime cache for
     *
     * @var string
     */
    protected $urlToPrimeCacheFor = null;
    /**
     * Locales to prime the cache for
     * 
     * @var array
     */
    protected $localesToPrimceCacheFor = null;
    /**
     * context locale to start threads from
     * 
     * @var string
     */
    protected $currentLocale = null;
    
    /**
     * Runs this task.
     * 
     * @param \SilverStripe\Control\HTTPRequest $request Request
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function run($request)
    {
        $this->printInfo("Running Cache Primer...");
        $this->initArgs();
        
        $url           = $this->getUrlToPrimeCacheFor();
        $locales       = $this->getLocales();
        $localesString = implode(',', $locales);
        
        if (empty($url)) {
            $this->printError("Target URL is missing.");
            $this->printUsage();
            exit();
        }
        
        $this->printInfo("Priming cache for {$url} [locale(s): {$localesString}].");
        
        foreach ($locales as $locale) {
            $this->primeForLocale($locale);
        }
    }
    
    protected function primeForLocale($locale)
    {
        $sitemapXmlURL = $this->buildUrlToSitemapXml($this->getUrlToPrimeCacheFor());
        $localizedURL  = "{$sitemapXmlURL}?locale={$locale}";
        $this->printInfo("Loading {$localizedURL}.");
        $this->requestPages($localizedURL);
    }
    
    /**
     * Loads the target page URLs from the sitemap.xml context.
     * 
     * @param string $sitemapURL Sitemap URL
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.12.2018
     */
    public function loadPageURLs($sitemapURL)
    {
        $urls    = [];
        $xmlRoot = new DOMDocument();
        $xmlRoot->preserveWhiteSpace = false;
        $xmlRoot->load($sitemapURL);
        $objectList = $xmlRoot->getElementsByTagName('loc');

        foreach($objectList as $objectURL) {
            if (strpos($objectURL->nodeValue, 'SilverStripe-CMS-Model-SiteTree') !== false) {
                $this->printInfo("• Loading {$objectURL->nodeValue}.");
                $xmlObject = new DOMDocument();
                $xmlObject->preserveWhiteSpace = false;
                $xmlObject->load($objectURL->nodeValue);
                $pageList = $xmlObject->getElementsByTagName('loc');
                foreach($pageList as $pageURL) {
                    if ($pageURL->tagName !== 'loc') {
                        continue;
                    }
                    $urls[] = $pageURL->nodeValue;
                }
            }
        }
        return $urls;
    }
    
    /**
     * Requests the target page URLs from the sitemap.xml context.
     * 
     * @param string $sitemapURL Sitemap URL
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.12.2018
     */
    public function requestPages($sitemapURL)
    {
        $urls         = $this->loadPageURLs($sitemapURL);
        $currentIndex = 1;
        $duration     = 0;
        $totalURLs    = count($urls);
        $calledURLs   = 0;

        $this->printInfo("Found {$totalURLs} pages to load.");

        foreach ($urls as $url) {
            $paddedIndex = str_pad($currentIndex, strlen($totalURLs), " ", STR_PAD_LEFT);
            $logString   = " • [{$paddedIndex}/{$totalURLs}] {$url}...";
            $currentIndex++;
            $this->printProgressInfo($logString);
            $start = microtime(true);
            $filecontent = @file_get_contents($url);
            if ($filecontent === false) {
                $this->printInfo("{$logString} \033[31m Error loading file.");
                $this->printError("\t" . var_export(error_get_last(), true));
                continue;
            } else {
                $calledURLs++;
                $end = microtime(true);
                $timeDifference = $end - $start;
                $this->printInfo("{$logString} \033[32m Done after {$timeDifference} seconds.");
                $duration += $timeDifference;
            }
        }

        $avg = $duration / $calledURLs;
        $this->printInfo("Finished after {$duration} seconds.", "32");
        $this->printInfo("Average prime time is {$avg} seconds.", "32");
    }
    
    /**
     * Prints the usage of this  sake script
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.09.2018
     */
    public function printUsage()
    {
        if ($this->isRunningCLI()) {
            $this->printInfo('');
            $this->printInfo('Usage:');
            $this->printInfo("\t" . 'sake dev/tasks/sc-cache-primer url="http://www.silvercart.org/" [locale=de_DE,en_US]');
            $this->printInfo('');
            $this->printInfo('Default locale:');
            $this->printInfo("\t" . 'sake dev/tasks/sc-cache-primer url="http://www.silvercart.org/"');
            $this->printInfo('');
            $this->printInfo('Locale de_DE:');
            $this->printInfo("\t" . 'sake dev/tasks/sc-cache-primer url="http://www.silvercart.org/" locale=de_DE');
            $this->printInfo('');
            $this->printInfo('Locale de_DE and en_US:');
            $this->printInfo("\t" . 'sake dev/tasks/sc-cache-primer url="http://www.silvercart.org/" locale=de_DE,en_US');
            $this->printInfo('');
        } else {
            $this->printInfo('');
            $this->printInfo('Usage:');
            $this->printInfo('• Default locale:');
            $this->printInfo(Director::absoluteURL('dev/tasks/' . self::$segment) . '/?url=www.silvercart.org');
            $this->printInfo('');
            $this->printInfo('• Locale de_DE:');
            $this->printInfo(Director::absoluteURL('dev/tasks/' . self::$segment) . '/?url=www.silvercart.org&locale=de_DE');
            $this->printInfo('');
            $this->printInfo('• Locale de_DE and en_US:');
            $this->printInfo(Director::absoluteURL('dev/tasks/' . self::$segment) . '/?url=www.silvercart.org&locale=de_DE%2Cen_US');
            $this->printInfo('');
        }
    }
    
    /**
     * Builds the URL to sitemap.xml for the given prime URL.
     * 
     * @param string $urlToPrimeCacheFor URL to prime cache for
     * 
     * @return string
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.02.2013
     */
    protected function buildUrlToSitemapXml($urlToPrimeCacheFor) {
        $urlToSitemapXml    = $urlToPrimeCacheFor;
        if (strpos(strrev($urlToSitemapXml), '/') !== 0) {
            $urlToSitemapXml .= '/';
        }
        $urlToSitemapXml .= 'sitemap.xml';
        return $urlToSitemapXml;
    }
    
    /**
     * Returns the URL to prime cache for
     * 
     * @return string
     */
    public function getUrlToPrimeCacheFor()
    {
        if (is_null($this->urlToPrimeCacheFor)) {
            $this->setUrlToPrimeCacheFor($this->getCliArg('url'));
        }
        return $this->urlToPrimeCacheFor;
    }

    /**
     * Sets the URL to prime cache for
     * 
     * @param string $urlToPrimeCacheFor URL to prime cache for
     * 
     * @return void
     */
    public function setUrlToPrimeCacheFor($urlToPrimeCacheFor)
    {
        if ($urlToPrimeCacheFor === '/dev/tasks/' . self::$segment) {
            $urlToPrimeCacheFor = '';
        } elseif (!empty($urlToPrimeCacheFor)
               && strpos($urlToPrimeCacheFor, 'http') !== 0
        ) {
            $urlToPrimeCacheFor = "http://{$urlToPrimeCacheFor}";
        }
        $this->urlToPrimeCacheFor = $urlToPrimeCacheFor;
    }

    /**
     * returns a array with all locales to prime cache for
     * 
     * @return array
     */
    public function getLocales()
    {
        if (is_null($this->localesToPrimceCacheFor)) {
            $this->setLocales($this->getCliArg('locales'));
        }
        return $this->localesToPrimceCacheFor;
    }

    /**
     * set the locales to prime cache for
     *
     * @param string $locales "de_DE" or comma seperated "de_DE,en_US"
     * 
     * @return void
     */
    public function setLocales($locales = null)
    {
        if (is_null($locales)) {
            $locales = Tools::current_locale();
        }
        $this->localesToPrimceCacheFor = explode(",", $locales);
    }
}
