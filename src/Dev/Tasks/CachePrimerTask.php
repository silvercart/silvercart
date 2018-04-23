<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Admin\Model\Config;
use SilverCart\Dev\DebugTools;
use SilverCart\Dev\Tasks\Task;
use SilverStripe\Control\Director;

/**
 * Task to prime a SilverCart based sites cache.
 *
 * @package SilverCart
 * @subpackage Dev_Tasks
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 12.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class CachePrimerTask extends Task {

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
     * Prints the usage of this  sake script
     * 
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 06.02.2013
     */
    public function printUsage() {
        $this->printInfo('Usage: sake CachePrimerTask url="http://www.silvercart.org/" [threads=10] [locale=de_DE,en_US]');
    }
    
    /**
     * Logs a message in context of the thread
     * 
     * @param string $text Text to log
     * 
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.02.2013
     */
    protected function LogForThread($text) {
        $this->Log('LOCALE:' . $this->getCurrentLocale() . ' THREAD#' . $this->getPrimeIndex(), $text);
    }

    /**
     * Processes the cache primer.
     * 
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.02.2013
     */
    public function process() {
        parent::process();
        
        $primeIndex         = $this->getPrimeIndex();
        $threads            = $this->getThreads();
        $locales            = $this->getLocales();
        if (count($locales) > 1) {
            foreach ($locales as $locale) {
                // start locale thread
                chdir(Director::baseFolder());
                $command = sprintf(
                    'sake CachePrimerTask url="%s" threads=%s locale=%s',
                    $this->getUrlToPrimeCacheFor(),
                    $threads,
                    $locale
                );
                $PID = self::run_process_in_background($command);
                $this->printInfo('Started thread for locale ' . $locale . ' with PID ' . $PID . '.');
            }
        } elseif (is_null($primeIndex)) {
            $this->buildSitemapXmlFiles();
            // only one locale, split and call with prime index
            $this->splitSitemapForThreads();
            $countOfUrlsToCall  = $this->getCountOfUrlsToCall();
            $urlsPerThread      = ceil($countOfUrlsToCall / $threads);
            $currentLocale      = $this->getCurrentLocale();

            if ($countOfUrlsToCall == 0) {
                $this->printError('The target URL has no pages to prime cache for.');
                $this->printUsage();
                exit();
            }
            
            $this->printInfo('There are ' . $countOfUrlsToCall . ' pages to prime cache for locale ' . $currentLocale . '.');
            $this->printInfo('Primer will be splitted into ' . $threads . ' threads (threads are running in background).');
            $this->printInfo('One thread will handle ' . $urlsPerThread . ' (or less) URLs.');
            
            chdir(Director::baseFolder());
            for ($x = 1; $x <= $threads; $x++) {
                $command = sprintf(
                        'sake CachePrimerTask url="%s" locale=%s prime-index=%s',
                        $this->getUrlToPrimeCacheFor(),
                        $currentLocale,
                        $x
                );
                $PID = self::run_process_in_background($command);
                $this->printInfo('Started ' . $currentLocale . ' thread #' . $x . ' with PID ' . $PID . '.');
            }
            exit();
        } else {
            $this->LogForThread('Starting thread...');
            $this->primeCache();
            exit();
        }
    }
    
    /**
     * Calls the threads related urls to prime the cache.
     * 
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.02.2013
     */
    public function primeCache() {
        $duration               = 0;
        $urlsToPrimeCacheFor    = unserialize(file_get_contents($this->getThreadTmpFilePath($this->getPrimeIndex())));
        $countOfUrlsToCall      = count($urlsToPrimeCacheFor);

        foreach ($urlsToPrimeCacheFor as $url) {
            $this->LogForThread('Priming cache for ' . $url);
            $start = microtime(true);
            $filecontent = @file_get_contents($url);
            if ($filecontent === false) {
                $this->printError('Error loading file.');
                $this->printError(var_export(error_get_last(), true));
                $countOfUrlsToCall--;
                continue;
            } else {
                $end = microtime(true);
                $timeDifference = $end - $start;
                $this->LogForThread('Done after ' . $timeDifference . ' seconds');
                $duration += $timeDifference;
            }
        }
        
        $this->setCountOfUrlsToCall($countOfUrlsToCall);
        $avg = $duration / $countOfUrlsToCall;
        $this->LogForThread('Average prime time is ' . $avg . ' seconds');
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
     * Returns the sitemap.xml as a SimpleXMLElement.
     * 
     * @return SimpleXMLElement
     */
    protected function getSitemapXml() {
        $currentLocale = $this->getCurrentLocale();
        if (!is_null($currentLocale)) {
            $this->buildSitemapXmlFiles();
        }
        return $this->sitemapXml;
    }

    /**
     * Merges the sitemaps that are being referenced from sitemap.xml.
     * Background: in SS3, the sitemap.xml consists of links to other sitemaps
     * 
     * @return SimpleXMLElement
     * 
     * @author Ramon Kupper <rkupper@pixeltricks.de>
     * @since 26.09.2015
     */
    protected function mergeSitemaps($urlToSitemapXml, $tmpFilePathForLocale) {
        $xmlstring = @file_get_contents($urlToSitemapXml);
        if ($xmlstring === false) {
            $this->printError('The target URL ' . $urlToSitemapXml . ' is invalid.');
            $this->printError('Please add a valid URL to prime cache for.');
            $this->printUsage();
            exit();
        }
        
        try {
                libxml_use_internal_errors(true);
                $xml = new SimpleXMLElement($xmlstring);
                libxml_clear_errors();
            } catch (Exception $exc) {
                $this->printError($exc->getMessage());
                $this->printError('The target URL ' . $baseUrlToSitemapXml . ' is invalid.');
                $this->printError('Please add a valid URL to prime cache for.');
                $this->printUsage();
                exit();
            }
                $sitemapUrls = array();
                foreach($xml->sitemap as $sitemap) {
                    $partialSitemapUrl = (string) $sitemap->loc;
                    $partialSitemapXml = $this->getPartialSitemap($partialSitemapUrl);
                    
                    foreach ($partialSitemapXml as $node) {
                        array_push($sitemapUrls, (string)$node->loc);
                    }
                };
                
                /* @var $XMLWriter XMLWriter */
                $XMLWriter = new XMLWriter();
                $XMLWriter->openMemory();
                $XMLWriter->startDocument();
                $XMLWriter->setIndent('true');
                $XMLWriter->startElement('urlset');
                    foreach($sitemapUrls as $sitemapUrl) {
                        $XMLWriter->startElement('url');
                            $XMLWriter->startElement('loc');
                                $XMLWriter->text($sitemapUrl);
                            $XMLWriter->endElement();
                        $XMLWriter->endElement();
                    }
                $XMLWriter->endElement();
                $xmlData = $XMLWriter->outputMemory();
                
                file_put_contents($tmpFilePathForLocale, $xmlData);

                return $xmlstring;
    }
    
    /**
     * Returns a partial sitemap
     * Background: in SS3, the sitemap.xml consists of links to other sitemaps
     * 
     * @return SimpleXMLElement
     */
    protected function getPartialSitemap($partialSitemapUrl) {
        $xmlstring = @file_get_contents($partialSitemapUrl);
        if ($xmlstring === false) {
            $this->printError('The target URL ' . $partialSitemapUrl . ' is invalid.');
            $this->printError('Please add a valid URL to prime cache for.');
            $this->printUsage();
            exit();
        }
        try {
               libxml_use_internal_errors(true);
               $xml = new SimpleXMLElement($xmlstring);
               libxml_clear_errors();
            } catch (Exception $exc) {
               $this->printError($exc->getMessage());
               $this->printError('The target URL ' . $baseUrlToSitemapXml . ' is invalid.');
               $this->printError('Please add a valid URL to prime cache for.');
               $this->printUsage();
               exit();
            }
        return $xml;
    }
    
    /**
     * builds the tmp files for sitemap 
     *
     * @return void
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 11.03.2013
     */
    protected function buildSitemapXmlFiles() {
        if (is_null($this->sitemapXml)) {
            $urlToPrimeCacheFor  = $this->getUrlToPrimeCacheFor();
            $baseUrlToSitemapXml = $this->buildUrlToSitemapXml($urlToPrimeCacheFor);
            $tmpFilePathBase     = $this->getBaseTmpFilePath() . 'sitemap.xml';

            if (empty($urlToPrimeCacheFor)) {
                $this->printError('Please add a valid URL to prime cache for.');
                $this->printUsage();
                exit();
            }
           
            $this->printInfo('Priming cache for ' . $urlToPrimeCacheFor);
            $locale = $this->getCurrentLocale();
            $tmpFilePathForLocale = $tmpFilePathBase . "-" . $locale;
            if (file_exists($tmpFilePathForLocale)) {
                $this->printInfo('Taking locally stored sitemap.xml for locale ' . $locale . ', no need to request server');
                $xmlstring = file_get_contents($tmpFilePathForLocale);
            } else {
                
                $urlToSitemapXml = $baseUrlToSitemapXml . "?locale=" . $locale;

                $this->printInfo('Requesting ' . $urlToSitemapXml);

                DebugTools::startTimer();

                $xmlstring = $this->mergeSitemaps($urlToSitemapXml, $tmpFilePathForLocale);
                
                $this->printInfo('Got sitemap.xml for locale ' . $locale . ' after ' . number_format(DebugTools::getTimeDifference(false), 2) . ' seconds.');
            }
            
            if ($xmlstring === false) {
                $this->printError('The target URL ' . $urlToSitemapXml . ' is invalid.');
                $this->printError('Please add a valid URL to prime cache for.');
                $this->printUsage();
                exit();
            }

             try {
                    libxml_use_internal_errors(true);
                    $xml = new SimpleXMLElement($xmlstring);
                    libxml_clear_errors();
                } catch (Exception $exc) {
                    $this->printError($exc->getMessage());
                    $this->printError('The target URL ' . $baseUrlToSitemapXml . ' is invalid.');
                    $this->printError('Please add a valid URL to prime cache for.');
                    $this->printUsage();
                    exit();
                }
            $this->sitemapXml = $xml;
        }
    }

    /**
     * Splits the requested sitemap.xml into one file per thread.
     * 
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.02.2013
     */
    protected function splitSitemapForThreads() {
        $threads            = $this->getThreads();
        $sitemapXml         = $this->getSitemapXml();
        $countOfUrlsToCall  = $this->getCountOfUrlsToCall();
        $urlsPerThread      = ceil($countOfUrlsToCall / $threads);
        $count              = 0;
        $urlsForThread      = array();
        $thread             = 1;

        foreach ($sitemapXml->url as $url) {
            $urlsForThread[] = (string) $url->loc;
            $count++;
            if ($count == $urlsPerThread) {
                file_put_contents($this->getThreadTmpFilePath($thread), serialize($urlsForThread));
                $count          = 0;
                $urlsForThread  = array();
                $thread++;
            }
        }

        if ($count > 0) {
            file_put_contents($this->getThreadTmpFilePath($thread), serialize($urlsForThread));
        }        
    }
    
    /**
     * Returns the base file path to a cache prime related temporary file.
     * 
     * @return string
     */
    protected function getBaseTmpFilePath() {
        return $this->getTmpFolder() . '/silvercart-cache-primer-' . md5($this->getUrlToPrimeCacheFor()) . '-';
    }
    
    /**
     * Returns the file path to a cache prime related temporary file in context 
     * of the given thread.
     * 
     * @param int $thread Index of the thread to get tmp file path for
     * 
     * @return string
     */
    protected function getThreadTmpFilePath($thread) {
        return $this->getBaseTmpFilePath() . 'locale-' . $this->getCurrentLocale() . '-thread-' . $thread;
    }

    /**
     * Returns the count of URLs to call
     * 
     * @return int
     */
    public function getCountOfUrlsToCall() {
        if (is_null($this->countOfUrlsToCall)) {
            $this->setCountOfUrlsToCall(count($this->getSitemapXml()->url));
        }
        return $this->countOfUrlsToCall;
    }

    /**
     * Sets the count of URLs to call
     * 
     * @param int $countOfUrlsToCall Count of URLs to call
     * 
     * @return void
     */
    public function setCountOfUrlsToCall($countOfUrlsToCall) {
        $this->countOfUrlsToCall = $countOfUrlsToCall;
    }
    
    /**
     * Returns the prime index
     * 
     * @return int
     */
    public function getPrimeIndex() {
        if (is_null($this->primeIndex)) {
            $this->setPrimeIndex($this->getCliArg('prime-index'));
        }
        return $this->primeIndex;
    }

    /**
     * Sets the prime index
     * 
     * @param int $primeIndex Prime index
     * 
     * @return void
     */
    public function setPrimeIndex($primeIndex) {
        $this->primeIndex = $primeIndex;
    }
    
    /**
     * Returns the thread count
     * 
     * @return int
     */
    public function getThreads() {
        if (is_null($this->threads)) {
            $this->setThreads($this->getCliArg('threads'));
        }
        return $this->threads;
    }

    /**
     * Sets the thread count
     * 
     * @param int $threads Thread count
     * 
     * @return void
     */
    public function setThreads($threads) {
        if (is_null($threads)) {
            $threads = 1;
        }
        $this->threads = $threads;
    }
    
    /**
     * Returns the URL to prime cache for
     * 
     * @return string
     */
    public function getUrlToPrimeCacheFor() {
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
    public function setUrlToPrimeCacheFor($urlToPrimeCacheFor) {
        $this->urlToPrimeCacheFor = $urlToPrimeCacheFor;
    }

    /**
     * returns a array with all locales to prime cache for
     * 
     * @return array
     */
    public function getLocales() {
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
    public function setLocales($locales = null) {
        $this->localesToPrimceCacheFor = explode(",", $locales);
    }

    /**
     * sets the context locale into $currentLocale
     *
     * @param string $locale locale e.g. en_US
     * 
     * @return void
     */
    public function setCurrentLocale($locale) {
        $this->currentLocale = $locale;
    }

    /**
     * returns the context locale as string e.g. en_US
     * if no locale is given SilverCarts default language is used
     *
     * @return string
     */
    public function getCurrentLocale() {
        if (is_null($this->currentLocale)) {
            $currentLocale = $this->getCliArg('locale');
            if (is_null($currentLocale)) {
                $currentLocale = Config::DefaultLanguage();
            }
            $this->setCurrentLocale($currentLocale);
        }
        return $this->currentLocale;
    }
    
}
