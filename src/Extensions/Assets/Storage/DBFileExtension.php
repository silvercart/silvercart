<?php

namespace SilverCart\Extensions\Assets\Storage;

use SilverStripe\Assets\Flysystem\FlysystemAssetStore;
use SilverStripe\Assets\Flysystem\ProtectedAssetAdapter;
use SilverStripe\ORM\DataExtension;

/**
 * Extension for SilverStripe DBFile.
 * 
 * @package SilverCart
 * @subpackage Extensions\Assets\Storage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.12.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class DBFileExtension extends DataExtension
{
    /**
     * Performs filename cleanup before sending it back.
     *
     * This name should not contain hash or variants.
     *
     * @param string $filename
     * 
     * @return string
     * 
     * @see FlysystemAssetStore::cleanFilename()
     */
    protected function cleanFilename(string $filename) : string
    {
        // Since we use double underscore to delimit variants, eradicate them from filename
        return preg_replace('/_{2,}/', '_', $filename);
    }

    /**
     * Determine if legacy filenames should be used. These do not have hash path parts.
     *
     * @return bool
     * 
     * @see FlysystemAssetStore::useLegacyFilenames()
     */
    protected function useLegacyFilenames() : bool
    {
        return FlysystemAssetStore::config()->legacy_filenames;
    }

    /**
     * Map file tuple (hash, name, variant) to a filename to be used by flysystem
     *
     * The resulting file will look something like my/directory/EA775CB4D4/filename__variant.jpg
     *
     * @param bool $legacy Use legacy file names?
     * 
     * @return string Adapter specific identifier for this file/version
     * 
     * @see FlysystemAssetStore::getFileID()
     */
    public function getFileID(bool $legacy = false) : string
    {
        // Since we use double underscore to delimit variants, eradicate them from filename
        $filename = $this->cleanFilename($this->owner->Filename);
        $name     = basename($filename);

        // Split extension
        $extension = null;
        if (($pos = strpos($name, '.')) !== false) {
            $extension = substr($name, $pos);
            $name      = substr($name, 0, $pos);
        }
        // Unless in legacy mode, inject hash just prior to the filename
        if ($legacy
         || $this->useLegacyFilenames()
        ) {
            $fileID = $name;
        } else {
            $fileID = substr($this->owner->Hash, 0, 10) . "/{$name}";
        }
        // Add directory
        $dirname = ltrim(dirname($filename), '.');
        if ($dirname) {
            $fileID = "{$dirname}/{$fileID}";
        }
        // Add variant
        if (!empty($this->owner->Variant)) {
            $fileID .= "__{$this->owner->Variant}";
        }
        // Add extension
        if ($extension) {
            $fileID .= $extension;
        }
        return $fileID;
    }
    
    /**
     * Returns the file path to this file.
     * 
     * @return string
     */
    public function getFilePath() : string
    {
        $path = ASSETS_PATH . DIRECTORY_SEPARATOR . $this->getFileID();
        if (!file_exists($path)) {
            $path = ASSETS_PATH . DIRECTORY_SEPARATOR . $this->getFileID(true);
            if (!file_exists($path)) {
                $path = ASSETS_PATH . DIRECTORY_SEPARATOR . ProtectedAssetAdapter::config()->secure_folder . DIRECTORY_SEPARATOR . $this->getFileID();
                if (!file_exists($path)) {
                    $path = ASSETS_PATH . DIRECTORY_SEPARATOR . ProtectedAssetAdapter::config()->secure_folder . DIRECTORY_SEPARATOR . $this->getFileID(true);
                    if (!file_exists($path)) {
                        $path = '';
                    }
                }
            }
        }
        return $path;
    }
}