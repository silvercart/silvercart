<?php

namespace SilverCart\Model\Pages;

use Page;
use SilverCart\Dev\Tools;
use SilverStripe\Forms\FieldList;

/**
 * Page to handle feedback from payment providers.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaymentNotification extends Page
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentNotification';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-block-settings';

    /**
     * Return all fields of the backend.
     *
     * @return FieldList Fields of the CMS
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->removeByName('Content');
            $fields->removeByName('Metadata');
            $fields->removeByName('UseAsRootForMainNavigation');
            $fields->removeByName('DisplayBreadcrumbs');
        });
        $this->getCMSFieldsIsCalled = true;
        return parent::getCMSFields();
    }
}