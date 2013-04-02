<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Tasks
 */

/**
 * Task to prime a SilverCart based sites cache.
 *
 * @package Silvercart
 * @subpackage Tasks
 * @author Patrick Schneider <pschneider@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 07.02.2013
 * @license see license file in modules root directory
 */
class SilvercartCachePrimer extends SilvercartTask {

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
     * Prints the usage of this sake script
     * 
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 06.02.2013
     */
    public function printUsage() {
        $this->printInfo('Usage: sake SilvercartCachePrimer url="http://www.silvercart.org/" [threads=10]');
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
        $this->Log('THREAD#' . $this->getPrimeIndex(), $text);
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
        
        if (is_null($primeIndex)) {
            $this->splitSitemapForThreads();
            $countOfUrlsToCall  = $this->getCountOfUrlsToCall();
            $urlsPerThread      = ceil($countOfUrlsToCall / $threads);
            
            if ($countOfUrlsToCall == 0) {
                $this->printError('The target URL has no pages to prime cache for.');
                $this->printUsage();
                exit();
            }
            
            $this->printInfo('There are ' . $countOfUrlsToCall . ' pages to prime cache for.');
            $this->printInfo('Primer will be splitted into ' . $threads . ' threads (threads are running in background).');
            $this->printInfo('One thread will handle ' . $urlsPerThread . ' (or less) URLs.');
            
            chdir(Director::baseFolder());
            for ($x = 1; $x <= $threads; $x++) {
                $command = sprintf(
                        'sake SilvercartCachePrimer url="%s" prime-index=%s',
                        $this->getUrlToPrimeCacheFor(),
                        $x
                );
                $PID = self::run_process_in_background($command);
                $this->printInfo('Started Thread #' . $x . ' with PID ' . $PID . '.');
            }
            exit();
        } else {
            $this->LogForThread('Starting thread...');
            $this->primeCache();
            exit();
        }
    }
    
    /**
     * Calls the treads related urls to prime the cache.
     * 
     * @return void
     *
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.02.2013
     */
    public function primeCache() {
        $countOfUrlsToCall      = $this->getCountOfUrlsToCall();
        $duration               = 0;
        $urlsToPrimeCacheFor    = unserialize(file_get_contents($this->getThreadTmpFilePath($this->getPrimeIndex())));
        foreach ($urlsToPrimeCacheFor as $url) {
            $this->LogForThread('Priming cache for ' . $url);
            $start = microtime(true);
            $filecontent = @file_get_contents($url);
            if ($filecontent === false) {
                $this->printError('Error loading file.');
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
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 07.02.2013
     */
    protected function getSitemapXml() {
        if (is_null($this->sitemapXml)) {
            $urlToPrimeCacheFor = $this->getUrlToPrimeCacheFor();
            $urlToSitemapXml    = $this->buildUrlToSitemapXml($urlToPrimeCacheFor);
            $tmpFilePath        = $this->getBaseTmpFilePath() . 'sitemap.xml';

            if (empty($urlToPrimeCacheFor)) {
                $this->printError('Please add a valid URL to prime cache for.');
                $this->printUsage();
                exit();
            }

            $this->printInfo('Priming cache for ' . $urlToPrimeCacheFor);

            if (file_exists($tmpFilePath)) {
                $this->printInfo('Taking locally stored sitemap.xml, no need to request server');
                $xmlstring = file_get_contents($tmpFilePath);
            } else {

                $this->printInfo('Requesting ' . $urlToSitemapXml);

                SilvercartDebugHelper::startTimer();

                $xmlstring = @file_get_contents($urlToSitemapXml);
                file_put_contents($tmpFilePath, $xmlstring);

                if ($xmlstring === false) {
                    $this->printError('The target URL ' . $urlToSitemapXml . ' is invalid.');
                    $this->printError('Please add a valid URL to prime cache for.');
                    $this->printUsage();
                    exit();
                }

                $this->printInfo('Got sitemap.xml after ' . number_format(SilvercartDebugHelper::getTimeDifference(false), 2) . ' seconds.');
            }

            try {
                libxml_use_internal_errors(true);
                $xml = new SimpleXMLElement($xmlstring);
                libxml_clear_errors();
            } catch (Exception $exc) {
                $this->printError($exc->getMessage());
                $this->printError('The target URL ' . $urlToSitemapXml . ' is invalid.');
                $this->printError('Please add a valid URL to prime cache for.');
                $this->printUsage();
                exit();
            }
            $this->sitemapXml = $xml;
        }
        
        return $this->sitemapXml;
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
        return $this->getTmpFolder() . '/SilvercartCachePrimer-' . md5($this->getUrlToPrimeCacheFor()) . '-';
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
        return $this->getBaseTmpFilePath() . 'thread-' . $thread;
    }

    /**
     * Returns the count of URLs to call
     * 
     * @return int
     */
    public function getCountOfUrlsToCall() {
        if (is_null($this->countOfUrlsToCall)) {
            $this->countOfUrlsToCall = count($this->getSitemapXml()->url);;
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
    
}