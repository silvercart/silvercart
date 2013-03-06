<?php
/**
 * Copyright 2013 pixeltricks GmbH
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
 * @subpackage Update
 */

/**
 * Update 1.3 - 9
 * Create page "revocation instructions".
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 2013-03-04
 * @copyright pixeltricks GmbH
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_3__9 extends SilvercartUpdate {

    /**
     * Set the defaults for this update.
     *
     * @var array
     */
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '9',
        'Description'               => 'Create page "revocation instructions"',
    );

    /**
     * Executes the update logic.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-03-04
     */
    public function executeUpdate() {
        $termsOfServicePage = SilvercartTools::PageByIdentifierCode('TermsOfServicePage');

        if ($termsOfServicePage) {
            $revocationInstructionPage                  = new RedirectorPage();
            $revocationInstructionPage->RedirectionType = 'Internal';
            $revocationInstructionPage->LinkToID        = $termsOfServicePage->ID;
            $revocationInstructionPage->Title           = _t('RevocationInstructionPage.DEFAULT_TITLE', 'revocation instruction');
            $revocationInstructionPage->URLSegment      = _t('RevocationInstructionPage.DEFAULT_URLSEGMENT', 'revocation-instruction');
            $revocationInstructionPage->Status          = "Published";
            $revocationInstructionPage->ShowInMenus     = 1;
            $revocationInstructionPage->ParentID        = $termsOfServicePage->ParentID;
            $revocationInstructionPage->IdentifierCode  = "SilvercartRevocationInstructionPage";
            $revocationInstructionPage->write();
            $revocationInstructionPage->publish("Stage", "Live");

            return true;
        } else {
            return false;
        }
    }
}