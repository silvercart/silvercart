<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Parsers
 */

/**
 * Parses Content blocks.
 *
 * @package Silvercart
 * @subpackage Parsers
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 2012-12-10
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartContentParser extends TextParser {

    /**
     * Parses the Content field of the current page within the page
     * controller's context.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2012-12-10
     */
    public function parse() {
        $content = new SSViewer_FromString($this->content);
        $content = $content->process(Controller::curr());

        return $content;
    }
}