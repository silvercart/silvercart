<?php

namespace SilverCart\Admin\Controllers;

use SilverCart\Admin\Controllers\LeftAndMain;
use SilverCart\Admin\Dev\Tasks\ProductImageImportTask;
use SilverCart\Admin\Forms\AlertDangerField;
use SilverCart\Admin\Forms\AlertInfoField;
use SilverCart\Admin\Forms\AlertSuccessField;
use SilverCart\Admin\Forms\AlertWarningField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;

/**
 * Provides a form to upload multiple images for different products at once.
 * 
 * @package SilverCart
 * @subpackage Admin_Controllers
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2017 pixeltricks GmbH
 * @since 22.09.2017
 * @license see license file in modules root directory
 */
class ProductImageAdmin extends LeftAndMain
{
    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    private static $menuCode = 'products';
    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    private static $menuSortIndex = 11;
    /**
     * The URL segment
     *
     * @var string
     */
    private static $url_segment = 'silvercart-product-images';
    /**
     * The menu title
     *
     * @var string
     */
    private static $menu_title = 'Product Images';
    
    /**
     * Returns the edit form for this admin.
     * 
     * @param type $id
     * @param type $fields
     * 
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) : Form
    {
        $fields  = FieldList::create();
        $actions = FieldList::create();
        
        $desc = _t(ProductImageAdmin::class . '.Description', 'Description');
        
        $descriptionField = AlertInfoField::create('ProductImagesDescription', $desc);
        $uploadField      = UploadField::create('ProductImages', _t(ProductImageAdmin::class . '.UploadProductImages', 'Upload product images'));
        $uploadField->setFolderName(ProductImageImportTask::get_relative_upload_folder());
        
        $fields->push($uploadField);
        $fields->push($descriptionField);
        
        if (!ProductImageImportTask::is_installed()) {
            $cronTitle = _t(ProductImageAdmin::class . '.CronNotInstalledTitle', 'Caution') . ':';
            $cron = _t(ProductImageAdmin::class . '.CronNotInstalledDescription', 'The installation of the product image import is not finished yet.');
            $cronjobInfoField = AlertDangerField::create('ProductImagesCronjobInfo', $cron, $cronTitle);
            $fields->insertAfter('ProductImages', $cronjobInfoField);
        } elseif (ProductImageImportTask::is_running()) {
            $cronTitle = _t(ProductImageAdmin::class . '.CronIsRunningTitle', 'Caution') . ':';
            $cron = _t(ProductImageAdmin::class . '.CronIsRunningDescription', 'The product image import is currently running.');
            $cronjobInfoField = AlertSuccessField::create('ProductImagesCronjobInfo', $cron, $cronTitle);
            $fields->insertAfter('ProductImages', $cronjobInfoField);
        }
        
        $uploadedFiles     = $this->getUploadedFiles();
        if (count($uploadedFiles) > 0) {
            $uploadedFilesInfo = PHP_EOL . implode(PHP_EOL, $uploadedFiles);
            $fileInfoField = AlertWarningField::create('ProductImagesFileInfo', $uploadedFilesInfo, _t(ProductImageAdmin::class . '.FileInfoTitle', 'The following product images are not handled yet:'));
            $fields->insertAfter('ProductImages', $fileInfoField);
        }
        
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
     * Returns a list of not yet handled product image uploads.
     * 
     * @return array
     */
    protected function getUploadedFiles() : array
    {
        $files = [];
        $dirs  = [
            ProductImageImportTask::get_absolute_upload_folder(),
            ProductImageImportTask::get_absolute_protected_upload_folder(),
        ];
        foreach ($dirs as $dir) {
            $files = array_merge($files, ProductImageImportTask::getFilesFromDir($dir, true));
        }
        return $files;
    }
}