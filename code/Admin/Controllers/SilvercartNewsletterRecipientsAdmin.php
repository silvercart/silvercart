<?php
/**
 * Copyright 2016 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage ModelAdmins
 */

/**
 * Provides a form to export newsletter recipients including anonymous ones.
 * 
 * @package Silvercart
 * @subpackage LeftAndMain
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2016 pixeltricks GmbH
 * @since 25.07.2016
 * @license see license file in modules root directory
 */
class SilvercartNewsletterRecipientsAdmin extends LeftAndMain {
    
    /**
     * List of allowed actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'do_newsletter_recipients_export',
    );

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'customer';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 50;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-newsletter-recipients';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Newsletter Recipients';
    
    /**
     * Returns the edit form for this admin.
     * 
     * @param type $id
     * @param type $fields
     * 
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) {
        $fields = new FieldList();
        
        $desc = _t('SilvercartNewsletterRecipientsAdmin.Description', 'Please choose your export context and press the export button to download a CSV list of email recipients.');
        
        $descriptionField = new SilvercartAlertInfoField('SilvercartProductImagesDescription', str_replace(PHP_EOL, '<br/>', $desc));
        
        $exportContextField = new DropdownField('ExportContext', _t('SilvercartNewsletterRecipientsAdmin.ExportContext', 'Export context'));
        $exportContextField->setSource(array(
            '0' => _t('SilvercartNewsletterRecipientsAdmin.ExportAll', 'Export all customers'),
            '1' => _t('SilvercartNewsletterRecipientsAdmin.ExportAllNewsletterRecipients', 'Export all newsletter recipients'),
            '2' => _t('SilvercartNewsletterRecipientsAdmin.ExportAllNewsletterRecipientsWithAccount', 'Export all newsletter recipients with customer account'),
            '3' => _t('SilvercartNewsletterRecipientsAdmin.ExportAllNewsletterRecipientsWithoutAccount', 'Export all newsletter recipients without customer account'),
            '4' => _t('SilvercartNewsletterRecipientsAdmin.ExportAllNonNewsletterRecipients', 'Export all non-newsletter recipients'),
        ));
        
        $doExportButton = new InlineFormAction('do_newsletter_recipients_export', _t('SilvercartNewsletterRecipientsAdmin.DoExport', 'Export as CSV'));
        $doExportButton->includeDefaultJS(false);
        $doExportButton->setAttribute('data-icon', 'download-csv');
        
        $fields->push($descriptionField);
        $fields->push($exportContextField);
        $fields->push($doExportButton);
        
        $actions = new FieldList();
        $form = new Form($this, "EditForm", $fields, $actions);
        $form->addExtraClass('cms-edit-form cms-panel-padded center ' . $this->BaseCSSClasses());
        $form->loadDataFrom($this->request->getVars());

        $this->extend('updateEditForm', $form);

        return $form;
    }
    
    /**
     * Adds example data to SilverCart when triggered in ModelAdmin.
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.07.2016
     */
    public function do_newsletter_recipients_export(SS_HTTPRequest $request) {
        $exportContext = $request->getVar('ExportContext');
        if (is_null($exportContext)) {
            return false;
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
    protected function getCSVContent($exportContext) {
        $membersSql         = 'SELECT "M"."Email", "M"."Salutation", "M"."FirstName", "M"."Surname" FROM "Member" AS "M" WHERE "M"."Email" IS NOT NULL';
        $anonymousSql       = 'SELECT "ANR"."Email", "ANR"."Salutation", "ANR"."FirstName", "ANR"."Surname" FROM "SilvercartAnonymousNewsletterRecipient" AS "ANR" WHERE "ANR"."Email" IS NOT NULL AND "ANR"."NewsletterOptInStatus" = 1';
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
        
        $tempFolder = getTempFolder();
        $tempCsvFile = $tempFolder . '/do_newsletter_recipients_export.csv';
        
        $csvFile = fopen($tempCsvFile, 'w');
        fputcsv($csvFile, array(
            'email',
            'salutation',
            'firstname',
            'surname',
        ));
        
        if ($useMembersSql) {
            $records = DB::query($membersSql . $membersSqlAddition);
            if ($records->numRecords() > 0) {
                foreach ($records as $record) {
                    $record['Salutation'] = SilvercartTools::getSalutationText($record['Salutation']);
                    fputcsv($csvFile, $record);
                }
            }
        }
        if ($useAnonymousSql) {
            $records = DB::query($anonymousSql);
            if ($records->numRecords() > 0) {
                foreach ($records as $record) {
                    $record['Salutation'] = SilvercartTools::getSalutationText($record['Salutation']);
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
