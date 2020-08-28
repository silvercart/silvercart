<?php

namespace SilverCart\Admin\Forms;

use SilverStripe\Assets\File;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use ReflectionClass;

/**
 * Special upload field which creates a bridge object SilverCart\Model\Product\File to attach 
 * the uploaded file to. The newly created SilverCart\Model\Product\File will be attached to 
 * the handled record.
 *
 * @package SilverCart
 * @subpackage Admin_Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 25.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class FileUploadField extends UploadField
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'upload',
        'attach',
    ];
    /**
     * Class name of the file object
     *
     * @var string
     */
    protected $fileClassName = 'File';
    /**
     * Class name of the relation object
     *
     * @var string
     */
    protected $relationClassName = \SilverCart\Model\Product\File::class;

    /**
     * Returns the file class name
     * 
     * @return string
     */
    public function getFileClassName() : string
    {
        return $this->fileClassName;
    }

    /**
     * Sets the file class name
     * 
     * @param string $fileClassName Class name of the file object
     * 
     * @return void
     */
    public function setFileClassName(string $fileClassName) : void
    {
        $this->fileClassName = $fileClassName;
    }

    /**
     * Returns the relation class name
     * 
     * @return string
     */
    public function getRelationClassName() : string
    {
        return $this->relationClassName;
    }

    /**
     * Sets the relation class name
     * 
     * @param string $relationClassName Class name of the relation object
     * 
     * @return void
     */
    public function setRelationClassName(string $relationClassName) : void
    {
        $this->relationClassName = $relationClassName;
    }

    /**
     * Returns the schema defaults.
     * 
     * @return array
     */
    public function getSchemaDataDefaults() : array
    {
        $defaults   = parent::getSchemaDataDefaults();
        $attachLink = $this->Link('attach');
        $defaults['data']['attachFileEndpoint'] = [
            'url'           => $attachLink,
            'method'        => 'post',
            'payloadFormat' => 'urlencoded',
        ];
        return $defaults;
    }
    
    /**
     * Adds a JS requirement and returns the field markup.
     * 
     * @param array $properties key value pairs of template variables
     * 
     * @return DBHTMLText
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    public function Field($properties = array()) : DBHTMLText
    {
        Requirements::javascript('silvercart/silvercart:client/admin/javascript/FileUploadField.js');
        return parent::Field($properties);
    }

    /**
     * Adds a File and attaches the File onto $this->record.
     * 
     * @param File $file File to attach
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.03.2013
     */
    protected function attachFile(File $file) : void
    {
        $record            = $this->getRecord();
        $name              = $this->getName();
        $relationName      = str_replace('Upload', '', $name);
        $relationClassName = $this->getRelationClassName();
        $fileClassName     = $this->getFileClassName();
        
        if ($record
         && $record->exists()
        ) {
            $silvercartFile = new $relationClassName();
            $silvercartFile->{$fileClassName . 'ID'} = $file->ID;
            $silvercartFile->write();
            if ($record->hasMany($relationName)
             || $record->manyMany($relationName)
            ) {
                if (!$record->isInDB()) {
                    $record->write();
                }
                $record->{$relationName}()->add($silvercartFile);
            } elseif ($record->hasOne($relationName)) {
                $record->{$relationName . 'ID'} = $silvercartFile->ID;
                $record->write();
            }
        }
    }

    /**
     * Action to handle upload of a single file
     * 
     * @param SilverStripe\Control\HTTPRequest $request Request
     * 
     * @return SilverStripe\Control\HTTPResponse
     * 
     * @throws \SilverStripe\Control\HTTPResponse_Exception
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.03.2014
     */
    public function upload(HTTPRequest $request) : HTTPResponse
    {
        if ($this->isDisabled()
         || $this->isReadonly()
        ) {
            $this->httpError(403);
        }
        // Protect against CSRF on destructive action
        $token = $this->getForm()->getSecurityToken();
        if (!$token->checkRequest($request)) {
            $this->httpError(400);
        }
        $tmpFile = $request->postVar('Upload');
        /** @var File $file */
        $file = $this->saveTemporaryFile($tmpFile, $error);
        // Prepare result
        if ($error) {
            $result = [
                'message' => [
                    'type' => 'error',
                    'value' => $error,
                ]
            ];
            $this->getUpload()->clearErrors();
            return (new HTTPResponse(json_encode($result), 400))
                ->addHeader('Content-Type', 'application/json');
        }
        $this->attachFile($file);
        // Return success response
        $result = [
            AssetAdmin::singleton()->getObjectFromData($file)
        ];
        // Don't discard pre-generated client side canvas thumbnail
        if ($result[0]['category'] === 'image') {
            unset($result[0]['thumbnail']);
        }
        $this->getUpload()->clearErrors();
        return (new HTTPResponse(json_encode($result)))
            ->addHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieves details for files that this field wishes to attache to the 
     * client-side form
     * 
     * @param SilverStripe\Control\HTTPRequest $request Request
     * 
     * @return SilverStripe\Control\HTTPResponse
     * 
     * @throws \SilverStripe\Control\HTTPResponse_Exception
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 28.08.2020
     */
    public function attach(HTTPRequest $request) : HTTPResponse
    {
        if (!$request->isPOST()) {
            $this->httpError(403);
        }
        if (!$this->getAttachEnabled()) {
            $this->httpError(403);
        }
        // Retrieve file attributes required by front end
        $return = [];
        $files  = File::get()->byIDs($request->postVar('ids'));
        foreach ($files as $file) {
            $this->attachFile($file);
            $return[] = $this->encodeFileAttributes($file);
        }
        return HTTPResponse::create(json_encode($return))
            ->addHeader('Content-Type', 'application/json');
    }
    
    /**
     * Prepares the given file to return as JSON output.
     * 
     * @param File $file File
     * 
     * @return array
     */
    public function encodeFileAttributes(File $file) : array
    {
        $result = [
            AssetAdmin::singleton()->getObjectFromData($file)
        ];
        // Don't discard pre-generated client side canvas thumbnail
        if ($result[0]['category'] === 'image') {
            unset($result[0]['thumbnail']);
        }
        return $result;
    }
    
    /**
     * Returns the field type used for CSS/JS.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function Type() : string
    {
        $reflection = new ReflectionClass(static::class);
        return parent::Type() . ' sc-' . strtolower($reflection->getShortName());
    }
}