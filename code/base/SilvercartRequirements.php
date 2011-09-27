<?php

/**
 * Erweitert die Date Klasse um SilverControl-spezfisches.
 *
 * @package silvercontrol
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 25.11.2010
 * @license none
 */
class SilvercartRequirements extends Requirements {

    /**
     * Contains all registered variables for CSS files
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.09.2011
     */
    protected static $registeredCssVariables = array();
    
    /**
     * Contains all registered variables for JS files
     *
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.09.2011
     */
    protected static $registeredJsVariables = array();
    
    /**
     * Konkatiniert die in $files angegebenen JavaScript Dateien zu einer dynamisch
     * generierten Datei, komprimiert mit JSMin.
     * Wird das Array $templateVariables übergeben, erfolgt vor der Konkatinierung
     * ein Parsing mit Hilfe der silverstripe Template-Engine.
     * Weitere Informationen bei {@link Requirements_Backend::combine_files()}.
     *
     * @param string $combinedFileName  Name der Zieldatei
     * @param array  $files             Dateien, die vereint werden sollen
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.01.2011
     */
    public static function combine_files_and_parse($combinedFileName, $files) {
        foreach ($files as $index => $filename) {
            $cachedFileName     = 'assets/cache/' . self::prepare_filename_for_caching($filename);
            $cachedFileNameAbs  = self::getCacheFolder() . self::prepare_filename_for_caching($filename);
            $parsedFile         = '';

            if ( (@$_GET['flush'] == 'all')
                 || !file_exists($cachedFileNameAbs)
                 || Director::isDev()) {

                $pathInfo       = pathinfo($filename);
                $fileToParse    = file_get_contents(Director::baseFolder() . '/' . $filename);
                $templateParser = new SSViewer_FromString($fileToParse);

                if (array_key_exists('extension', $pathInfo) &&
                     !empty($pathInfo['extension'])) {
                    
                     if (strtolower($pathInfo['extension']) == 'css') {
                        $parsedFile = $templateParser->process(new ArrayData(self::getRegisteredCssVariables()));
                     }
                     if (strtolower($pathInfo['extension']) == 'js') {
                        $parsedFile = $templateParser->process(new ArrayData(self::getRegisteredJsVariables()));
                     }
                     
                     file_put_contents($cachedFileNameAbs, $parsedFile);
                 } 
            }
            $files[$index] = $cachedFileName;
        }
        
        self::combine_files($combinedFileName, $files);
    }
    
    /**
     * Deletes all cache files from the cache directory.
     *
     * @param boolean $flushCssFiles Indicates wether to delete CSS cache files
     * @param boolean $flushJsFiles  Indicates wether to delete JS cache files
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.09.2011
     */
    public static function flushCache($flushCssFiles = true, $flushJsFiles = true) {
         $cacheFiles = scandir(self::getCacheFolder());
         
         foreach ($cacheFiles as $cacheFile) {
             $pathInfo = pathinfo($cacheFile);
             
             if (array_key_exists('extension', $pathInfo) &&
                 !empty($pathInfo['extension'])) {
                 
                 if (strtolower($pathInfo['extension']) == 'css' &&
                     $flushCssFiles) {
                     
                     unlink(self::getCacheFolder().$cacheFile);
                 }
                 if (strtolower($pathInfo['extension']) == 'js' &&
                     $flushJsFiles) {
                     
                     unlink(self::getCacheFolder().$cacheFile);
                 }
             }
         }
    }

    /**
     * Liefert einen für das Caching gesäuberten Dateinamen (bspw. werden '/'
     * [Slash] durch '_' [Unterstrich] ersetzt).
     *
     * @param string $filename Dateiname
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 06.01.2011
     */
    public static function prepare_filename_for_caching($filename) {
        $pathInfo       = pathinfo(Director::baseFolder().'/'.$filename);
        $replacements   = array(
            '/' => '_',
        );
        foreach ($replacements as $search => $replace) {
            $filename = str_replace($search, $replace, $filename);
        }
        $filename = md5($filename) . sha1($filename) . '.' . $pathInfo['extension'];
        
        return $filename;
    }

    /**
     * Registers a variable for CSS templates
     *
     * @return void
     *
     * @param string $variableName  The name of the variable
     * @param mixed  $variableValue The value of the variable
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.09.2011
     */
    public static function registerCssVariable($variableName, $variableValue) {
        self::$registeredCssVariables[$variableName] = $variableValue;
    }
    
    /**
     * Registers a variable for JS templates
     *
     * @return void
     *
     * @param string $variableName  The name of the variable
     * @param mixed  $variableValue The value of the variable
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.09.2011
     */
    public static function registerJsVariable($variableName, $variableValue) {
        self::$registeredJsVariables[$variableName] = $variableValue;
    }
    
    /**
     * Returns all registered CSS variables as associative array.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.09.2011
     */
    public static function getRegisteredCssVariables() {
        return self::$registeredCssVariables;
    }
    
    /**
     * Returns all registered JS variables as associative array.
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.09.2011
     */
    public static function getRegisteredJsVariables() {
        return self::$registeredJsVariables;
    }
    
    /**
     * Returns the path to the folder for cache files.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.09.2011
     */
    protected static function getCacheFolder() {
         return Director::baseFolder() . '/assets/cache/';
    }
}