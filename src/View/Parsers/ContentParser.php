<?php

namespace SilverCart\View\Parsers;


/**
 * Parses Content blocks.
 *
 * @package SilverCart
 * @subpackage View_Parsers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 11.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ContentParser {
    
    use \SilverStripe\Core\Injector\Injectable;
    use \SilverStripe\Core\Config\Configurable;
    use \SilverStripe\Core\Extensible;
    
    /**
     * The original content to parse.
     *
     * @var string
     */
	protected $content;

	/**
	 * Creates a new TextParser object.
	 *
	 * @param string $content The contents of the dbfield
     * 
     * @return ContentParser
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.10.2017
	 */
	public function __construct($content = "") {
		$this->content = $content;
        $this->constructExtensions();
	}

	/**
	 * Convenience method, shouldn't really be used, but it's here if you want it
	 *
	 * @param string $content The contents of the dbfield
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.10.2017
	 */
	public function setContent($content = "") {
		$this->content = $content;
	}

    /**
     * Parses the Content field of the current page within the page
     * controller's context.
     *
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 11.10.2017
     */
    public function parse() {
        $content = new SSViewer_FromString($this->content);
        return $content->process(Controller::curr());
    }
}