<?php

namespace SilverCart\Model\Content;

use Sheadawson\Linkable\Forms\LinkField;
use Sheadawson\Linkable\Models\Link;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;

class BrandNavigationItem extends DataObject
{

    /**
     * Defines the database table name
     *  @var string
     */
    private static $table_name = 'SilverCart_Content_BrandNavigationItem';

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'     => 'Varchar',
        'Sort'      => 'Int',
        'isActive'  => 'Boolean'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Image'      => Image::class,
        'Link'       => Link::class,
        'SiteConfig' => SiteConfig::class,
    ];


    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var array
     */
    private static $summary_fields = [
        'ThumbnailNice' => 'Image', 
        'Title' => 'Title', 
        'Link.LinkType' => 'Link Type',
        'Link.LinkURL' => 'Link URL',
        'isActive' => 'is Active'
    ];

    public function getThumbnailNice() {
		return $this->Image()->ScaleHeight(20);
	}

    /**
     * Add default values to database
     *  @var array
     */
    private static $defaults = [
        'isActive' => true
    ];

    

    
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

    
    
    // public function getTitle() : string
    // {
    //     $title = parent::getTitle();
    //     $title = "{$this->Link} ({$this->Image})";
    //     return $title;
    // }
}
