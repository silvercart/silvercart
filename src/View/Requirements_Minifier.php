<?php

namespace SilverCart\View;

use SilverStripe\View\Requirements_Minifier as SilverStripeRequirements_Minifier;

/**
 * Uses MatthiasMullie\Minify to minify CSS and JS file content.
 * 
 * @package SilverCart
 * @subpackage View
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.90.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Requirements_Minifier implements SilverStripeRequirements_Minifier
{
    use \SilverStripe\Core\Injector\Injectable;
    
    /**
     * Minify the given content using MatthiasMullie\Minify.
     *
     * @param string $content  Content to minify.
     * @param string $type     Either js or css
     * @param string $filename Name of file to display in case of error
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    public function minify($content, $type, $filename)
    {
        if (class_exists("\\MatthiasMullie\\Minify\\CSS")) {
            if ($type === "css") {
                $minifier = new \MatthiasMullie\Minify\CSS($content);
                $content  = $minifier->minify();
            } elseif ($type === "js") {
                $minifier = new \MatthiasMullie\Minify\JS($content);
                $content  = $minifier->minify();
                if (strpos(strrev($content), ';') !== 0) {
                    $content .= ";";
                }
            }
        }
        return $content;
    }
}
