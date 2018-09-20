<?php

namespace SilverCart\Control;

use SilverStripe\Control\HTTPResponse;

/**
 * Extension of HTTPResponse to minify the HTML code output.
 * Based on a SilverStripe 3 extension created by Nivanka Fonseka 
 * (nivanka@silverstripers.com).
 * This uses a modified version of
 * https://code.google.com/p/minify/source/browse/min/lib/Minify/HTML.php
 * 
 * @package SilverCart
 * @subpackage Control
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 20.09.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MinifiedHTTPResponse extends HTTPResponse
{
    use \SilverStripe\Core\Config\Configurable;

    /**
     * Determines whether to remove inline JS comments out the HTML code output.
     *
     * @var bool
     */
    private static $clean_js_comments = true;
    /**
     * Determines whether the HTML code output is XHTML or not.
     *
     * @var bool
     */
    private static $is_xhtml          = false;
    /**
     * Array to store replacement placeholders.
     *
     * @var array
     */
    private $arrPlaceHolders;
    /**
     * Hash to use a replacement placeholder.
     *
     * @var string
     */
    private $strReplacementHash;

    /**
     * Sets the body.
     * 
     * @param string $body Body
     * 
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body ? (string) $body : $body; // Don't type-cast false-ish values, eg null is null not ''
        $this->MinifyHTML();
        return $this;
    }

    /**
     * Executes the HTML minification.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    public function MinifyHTML()
    {
        $this->strReplacementHash = 'MINIFYHTML' . md5($this->body);
        $this->arrPlaceHolders    = [];
        // scripts
        $this->body               = preg_replace_callback('/(\\s*)<script(\\b[^>]*?>)([\\s\\S]*?)<\\/script>(\\s*)/i', [$this, 'removeScriptCallBack'], $this->body);
        // styles
        $this->body               = preg_replace_callback('/\\s*<style(\\b[^>]*>)([\\s\\S]*?)<\\/style>\\s*/i',        [$this, 'removeStylesCallBack'], $this->body);
        // comments
        $this->body               = preg_replace_callback('/<!--([\\s\\S]*?)-->/',                                     [$this, 'commentCallBack'], $this->body);
        // replace PREs with placeholders
        $this->body               = preg_replace_callback('/\\s*<pre(\\b[^>]*?>[\\s\\S]*?<\\/pre>)\\s*/i',             [$this, 'removePreCallBack'], $this->body);
        $this->body               = preg_replace_callback('/\\s*<textarea(\\b[^>]*?>[\\s\\S]*?<\\/textarea>)\\s*/i',   [$this, 'removeTextareaCallBack'], $this->body);
        $this->body               = preg_replace('/^\\s+|\\s+$/m', '', $this->body);
        $this->body               = preg_replace('/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
                                            . '|caption|center|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
                                            . '|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'
                                            . '|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
                                            . '|ul)\\b[^>]*>)/i',                                                       '$1', $this->body);
        $this->body               = preg_replace('/>(\\s(?:\\s*))?([^<]+)(\\s(?:\s*))?</',                              '>$1$2$3<', $this->body);
        $this->body               = preg_replace('/(<[a-z\\-]+)\\s+([^>]+>)/i', "$1 $2", $this->body);
        $this->body               = str_replace(array_keys($this->arrPlaceHolders),                                     array_values($this->arrPlaceHolders), $this->body);
        $this->body               = str_replace(array_keys($this->arrPlaceHolders),                                     array_values($this->arrPlaceHolders), $this->body);
        $this->body               = str_replace('<script type="text/javascript"',                                       '<script ', $this->body);
        $this->body               = str_replace('<style type="text/css"',                                               '<style ', $this->body);

        return $this->body;
    }

    /**
     * Adds a placeholder.
     * 
     * @param string $content Content to add a placeholder for.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    private function reservePlace($content)
    {
        $placeholder                         = '%' . $this->strReplacementHash . count($this->arrPlaceHolders) . '%';
        $this->arrPlaceHolders[$placeholder] = $content;
        return $placeholder;
    }

    /**
     * Removes <![CDATA[ ]]> sections if not necessary.
     * 
     * @param string $str String to parse
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    private function removeCdata($str)
    {
        return (false !== strpos($str, '<![CDATA[')) ? str_replace(array('<![CDATA[', ']]>'), '', $str) : $str;
    }

    /**
     * Returns whether the given string needs a <![CDATA[ ]]> section.
     * 
     * @param string $str String to check
     * 
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    private function needsCdata($str)
    {
        return (self::config()->get('is_xhtml') && preg_match('/(?:[<&]|\\-\\-|\\]\\]>)/', $str));
    }
    
    /**************************************************************************/
    /**************************************************************************/
    /**                                                                      **/
    /**                           CALLBACK SECTION                           **/
    /**                                                                      **/
    /**************************************************************************/
    /**************************************************************************/

    /**
     * Callback method to parse comments.
     * 
     * @param array $m matches to check
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    private function commentCallBack($m)
    {
        return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<![')) ? $m[0] : '';
    }

    /**
     * Callback method to replace <pre> content with placeholders.
     * 
     * @param array $m matches to check
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    private function removePreCallBack($m)
    {
        return $this->reservePlace("<pre{$m[1]}");
    }

    /**
     * Callback method to replace <textarea> content with placeholders.
     * 
     * @param array $m matches to check
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    private function removeTextareaCallBack($m)
    {
        return $this->reservePlace("<textarea{$m[1]}");
    }

    /**
     * Callback method to minify inline CSS and replace <style> content with 
     * placeholders.
     * 
     * @param array $m matches to check
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    private function removeStylesCallBack($m)
    {
        $openStyle = "<style{$m[1]}";
        if (class_exists("\\MatthiasMullie\\Minify\\CSS")) {
            $minifier = new \MatthiasMullie\Minify\CSS($m[2]);
            $css      = $minifier->minify();
        } else {
            $css = preg_replace('/(?:^\\s*<!--|-->\\s*$)/', '', $m[2]);
            $css = $this->removeCdata($css);
            $css = call_user_func('trim', $css);
        }

        if ($this->needsCdata($css)) {
            $str = $this->reservePlace("{$openStyle}/*<![CDATA[*/{$css}/*]]>*/</style>");
        } else {
            $str = $this->reservePlace("{$openStyle}{$css}</style>");
        }
        return $str;
    }

    /**
     * Callback method to minify inline JS and replace <script> content with 
     * placeholders.
     * 
     * @param array $m matches to check
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.09.2018
     */
    private function removeScriptCallBack($m)
    {
        $openScript = "<script{$m[2]}";
        $ws1        = ($m[1] === '') ? '' : ' ';
        $ws2        = ($m[4] === '') ? '' : ' ';

        if (class_exists("\\MatthiasMullie\\Minify\\JS")) {
            $minifier = new \MatthiasMullie\Minify\JS($m[3]);
            $js       = $minifier->minify();
        } else {
            $js = $m[3];
            if (self::config()->get('clean_js_comments')) {
                $js = preg_replace('/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/', '', $js);
            }
            $js = $this->removeCdata($js);
            $js = call_user_func('trim', $js);
        }
        if ($this->needsCdata($js)) {
            $str = $this->reservePlace("{$ws1}{$openScript}/*<![CDATA[*/{$js}/*]]>*/</script>{$ws2}");
        } else {
            $str = $this->reservePlace("{$ws1}{$openScript}{$js}</script>{$ws2}");
        }
        return $str;
    }
}