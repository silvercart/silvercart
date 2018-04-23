<?php

namespace SilverCart\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\Product\File;
use SilverCart\Model\Product\FileTranslation;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\Map;

/** 
 * Form to search throug the downloads of a SilverCart\Model\Pages\DownloadHolder's 
 * SilverCart\Model\Pages\DownloadPage.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DownloadSearchForm extends CustomForm {
    
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
    
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'SearchQuery',
    ];

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $fields += [
                $searchField = TextField::create('SearchQuery', $this->fieldLabel('SearchQuery')),
            ];
            $searchField->setPlaceholder($this->fieldLabel('SearchQuery'));
            $searchField->setMaxLength(50);
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', $this->fieldLabel('submitButtonTitle'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        $children    = $this->getController()->AllChildren();
        $childrenIDs = $children->map('ID', 'ID');
        $searchQuery = $data['SearchQuery'];
        $searchTerms = explode(' ', $searchQuery);
        
        $fileTranslationTable = Tools::get_table_name(FileTranslation::class);
        $searchFilter         = sprintf(
                '"%s"."Title" LIKE \'%%%s%%\' OR "%s"."Description" LIKE \'%%%s%%\'',
                $fileTranslationTable,
                $searchQuery,
                $fileTranslationTable,
                $searchQuery
        );
        if (count($searchTerms) > 1) {
            foreach ($searchTerms as $searchTerm) {
                $searchFilter .= sprintf(
                    ' OR "%s"."Title" LIKE \'%%%s%%\' OR "%s"."Description" LIKE \'%%%s%%\'',
                    $fileTranslationTable,
                    $searchTerm,
                    $fileTranslationTable,
                    $searchTerm
                );
            }
        }
        
        if ($childrenIDs->count() > 0) {
            $fileTable = Tools::get_table_name(File::class);
            $filter    = sprintf(
                    '(%s) AND "%s"."DownloadPageID" IN (%s)',
                    $searchFilter,
                    $fileTable,
                    implode(',', $childrenIDs->toArray())
            );
        } else {
            $filter = $searchFilter;
        }
        
        $downloads = File::get()->where($filter);
        
        Tools::Session()->clear('SilvercartDownloadSearchForm.current_results');
        Tools::saveSession();
        if ($downloads) {
            Tools::Session()->set('SilvercartDownloadSearchForm.current_results', $downloads->map('ID','ID'));
        }
        Tools::Session()->set('SilvercartDownloadSearchForm.current_query', $searchQuery);
        Tools::saveSession();
        
        $this->getController()->redirect($this->getController()->Link('results'));
    }
    
    /**
     * Returns the current results out of session store.
     * 
     * @return DataList
     */
    public static function get_current_results() {
        $currentResultMap = Tools::Session()->get('SilvercartDownloadSearchForm.current_results');
        $downloads        = false;
        /* @var $currentResultMap Map */
        if (!is_null($currentResultMap) &&
            $currentResultMap->count() > 0) {
            $fileTable = Tools::get_table_name(File::class);
            $downloads = File::get()->where('"' . $fileTable . '"."ID" IN (' . implode(',', $currentResultMap->toArray()) . ')');
        }
        return $downloads;
    }
    
    /**
     * Returns the current search query out of session store.
     * 
     * @return string
     */
    public static function get_current_query() {
        $currentQuery = Tools::Session()->get('SilvercartDownloadSearchForm.current_query');
        return $currentQuery;
    }
}
