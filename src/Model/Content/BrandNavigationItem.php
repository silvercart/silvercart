<?php

namespace SilverCart\Model\Content;

use Sheadawson\Linkable\Forms\LinkField;
use Sheadawson\Linkable\Models\Link;
use SilverCart\ORM\ExtensibleDataObject;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Storage\DBFile;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;

/**
* @property string $Title          Title
* @property int    $Sort           Sort
* @property int    $LinkID         Related Link ID
* @property int    $SiteConfigID   Related SiteConfig ID
* @property bool   $IsActive       IsActive
* 
* @method Link       Link()       Returns the related Link.
* @method SiteConfig SiteConfig() Returns the related SiteConfig.
*/
class BrandNavigationItem extends DataObject
{
    use ExtensibleDataObject;
    /**
     * Defines the database table name
     *  @var string
     */
    private static $table_name = 'SilverCart_Content_BrandNavigationItem';
    /**
     * Database fields
     * @var string[]
     */
    private static $db = [
        'Title'     => 'Varchar',
        'Sort'      => 'Int',
        'IsActive'  => 'Boolean'
    ];
    /**
     * Has_one relationship
     * @var string[]
     */
    private static $has_one = [
        'Image'      => Image::class,
        'Link'       => Link::class,
        'SiteConfig' => SiteConfig::class,
    ];
    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var string[]
     */
    private static $summary_fields = [
        'ThumbnailNice',
        'Title',
        'Link.LinkType',
        'Link.LinkURL',
        'IsActive'
    ];
    /**
     * Add default values to database
     *  @var string[]
     */
    private static $defaults = [
        'IsActive' => true
    ];
    /**
     * Scales Image for GridView
     * 
     * @return DBFile|null
     */
    public function getThumbnailNice() : ?DBFile
    {
		return $this->Image()->ScaleHeight(20);
	}

    /**
     * @param bool $includerelations Include relation or not?
     * 
     * @return string[]
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'ThumbnailNice' => _t(self::class .".Image", "Logo"),
            'Link.LinkType' => _t(self::class .".LinkType", "Link Type"),
            'Link.LinkURL'  => _t(self::class .".LinkURL", "Link Url"),
        ]);
    }
    
    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->removeByName('Sort');
            if ($this->SiteConfig()->exists()) {
                $fields->replaceField('SiteConfigID', HiddenField::create('SiteConfigID', '', $this->SiteConfigID));
            }

            Link::config()->set('templates', []);
            $fields->replaceField(
                'LinkID',
                LinkField::create('LinkID', $this->fieldLabel('Link'))
                    ->setAllowedTypes([])
            );
            
        });
        
        return parent::getCMSFields();
    }
}
