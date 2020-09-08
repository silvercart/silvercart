<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\LeftAndMain;
use SilverCart\Admin\Forms\AlertInfoField;
use SilverCart\Dev\Tools;
use SilverCart\Model\Newsletter\AnonymousNewsletterRecipient;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\DB;

/**
 * Provides a form to export newsletter recipients including anonymous ones.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class NewsletterRecipientsAdmin extends LeftAndMain
{
    /**
     * List of allowed actions
     *
     * @var array
     */
    private static $allowed_actions = [
        'do_newsletter_recipients_export',
    ];
    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'customer';
    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 50;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-newsletter-recipients';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Newsletter Recipients';
    
    /**
     * Returns the edit form for this admin.
     * 
     * @param int       $id     Record ID
     * @param FieldList $fields Fields
     * 
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) : Form
    {
        $fields = FieldList::create(
            $descriptionField   = AlertInfoField::create('ProductImagesDescription', str_replace(PHP_EOL, '<br/>', _t(NewsletterRecipientsAdmin::class . '.Description', 'Please choose your export context and press the export button to download a CSV list of email recipients.'))),
            $exportContextField = DropdownField::create('ExportContext', _t(NewsletterRecipientsAdmin::class . '.ExportContext', 'Export context'))
        );
        $actions = FieldList::create(
            $doExportButton = FormAction::create(
                'do_newsletter_recipients_export',
                _t(NewsletterRecipientsAdmin::class . '.DoExport', 'Export as CSV')
            )->addExtraClass('btn-primary download-csv')
        );
        $exportContextField->setSource([
            '0' => _t(NewsletterRecipientsAdmin::class . '.ExportAll', 'Export all customers'),
            '1' => _t(NewsletterRecipientsAdmin::class . '.ExportAllNewsletterRecipients', 'Export all newsletter recipients'),
            '2' => _t(NewsletterRecipientsAdmin::class . '.ExportAllNewsletterRecipientsWithAccount', 'Export all newsletter recipients with customer account'),
            '3' => _t(NewsletterRecipientsAdmin::class . '.ExportAllNewsletterRecipientsWithoutAccount', 'Export all newsletter recipients without customer account'),
            '4' => _t(NewsletterRecipientsAdmin::class . '.ExportAllNonNewsletterRecipients', 'Export all non-newsletter recipients'),
        ]);
        $doExportButton->setAttribute('data-icon', 'download-csv');
        $form = Form::create(
            $this,
            'EditForm',
            $fields,
            $actions
        );
        $form->addExtraClass('flexbox-area-grow fill-height cms-content cms-edit-form' . $this->BaseCSSClasses());
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');
        $form->setHTMLID('Form_EditForm');
        $form->loadDataFrom($this->request->getVars());
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));
        $this->extend('updateEditForm', $form);
        return $form;
    }
    
    /**
     * Adds example data to SilverCart when triggered in ModelAdmin.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.07.2016
     */
    public function do_newsletter_recipients_export($request) : void
    {
        if (!($request instanceof HTTPRequest)) {
            $this->redirectBack();
            return;
        }
        $exportContext = $request->getVar('ExportContext');
        if (is_null($exportContext)) {
            return;
        }
        $csv = $this->getCSVContent($exportContext);
        header('Content-Type: text/csv');
        header('Content-Type: application/force-download');
        header('Content-Description: File Transfer');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . mb_strlen($csv, '8bit'));
        header('Content-Disposition: attachment;filename=email-recipients.csv');
        print $csv;
        exit();
    }
    
    /**
     * Creates and returns the CSV content to download.
     * $exportContext can be:
     * 0 = all customers with a valid email address
     * 1 = all newsletter recipients
     * 2 = all newsletter recipients with customer account
     * 3 = all newsletter recipients without customer account
     * 4 = all non-newletter recipients with customer account
     * 
     * @param string $exportContext 0|1|2|3|4; Identifies the context to get recipients for
     * 
     * @return string
     */
    protected function getCSVContent(string $exportContext) : string
    {
        $table              = Tools::get_table_name(AnonymousNewsletterRecipient::class);
        $membersSql         = 'SELECT "M"."Email", "M"."Salutation", "M"."FirstName", "M"."Surname" FROM "Member" AS "M" WHERE "M"."Email" IS NOT NULL';
        $anonymousSql       = 'SELECT "ANR"."Email", "ANR"."Salutation", "ANR"."FirstName", "ANR"."Surname" FROM "' . $table . '" AS "ANR" WHERE "ANR"."Email" IS NOT NULL AND "ANR"."NewsletterOptInStatus" = 1';
        $membersSqlAddition = '';
        switch ($exportContext) {
            case '1':
                // All newletter recipients
                $useMembersSql      = true;
                $useAnonymousSql    = true;
                $membersSqlAddition = ' AND "M"."NewsletterOptInStatus" = 1';
                break;
            case '2':
                // All newletter recipients with customer account
                $useMembersSql      = true;
                $useAnonymousSql    = false;
                $membersSqlAddition = ' AND "M"."NewsletterOptInStatus" = 1';
                break;
            case '3':
                // All newletter recipients without customer account
                $useMembersSql      = false;
                $useAnonymousSql    = true;
                break;
            case '4':
                // All non-newletter recipients
                $useMembersSql      = true;
                $useAnonymousSql    = false;
                $membersSqlAddition = ' AND "M"."NewsletterOptInStatus" = 0';
                break;
            case '0':
            default:
                // All customers with a valid email address
                $useMembersSql      = true;
                $useAnonymousSql    = true;
                break;
        }
        $tempCsvFile = TEMP_FOLDER . '/do_newsletter_recipients_export.csv';
        $csvFile     = fopen($tempCsvFile, 'w');
        fputcsv($csvFile, [
            'email',
            'salutation',
            'firstname',
            'surname',
        ]);
        if ($useMembersSql) {
            $records = DB::query($membersSql . $membersSqlAddition);
            if ($records->numRecords() > 0) {
                foreach ($records as $record) {
                    $record['Salutation'] = Tools::getSalutationText($record['Salutation']);
                    fputcsv($csvFile, $record);
                }
            }
        }
        if ($useAnonymousSql) {
            $records = DB::query($anonymousSql);
            if ($records->numRecords() > 0) {
                foreach ($records as $record) {
                    $record['Salutation'] = Tools::getSalutationText($record['Salutation']);
                    fputcsv($csvFile, $record);
                }
            }
        }
        fclose($csvFile);
        $csvFileContent = file_get_contents($tempCsvFile);
        unlink($tempCsvFile);
        return $csvFileContent;
    }
}