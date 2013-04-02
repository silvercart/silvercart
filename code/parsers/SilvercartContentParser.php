<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
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
 * @copyright 2012 pixeltricks GmbH
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