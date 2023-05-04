<?php

namespace SilverCart\Model\CookieConsent;

use SilverCart\Model\Pages\CheckoutStepController;
use SilverCart\ORM\Connect\DBMigration;
use SilverCart\Security\ExternalResourceValidator;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\ORM\FieldType\DBVarchar;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * Represents an external resource like Google Analytics code, Matomo Analytics 
 * code, Facebook Plugin SDK code or similar Javascript or HTML injections.
 * If the Broarm\CookieConsent module is installed each external resource can be
 * related to a cookie to be able to prevent to load the
 * 
 * @package SilverCart
 * @subpackage SubPackage
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 03.11.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property string $Name        Name
 * @property string $Title       Title
 * @property string $Description Description
 * @property string $Code        Code
 * @property string $URLSegment  URLSegment
 * @property string $Position    Position
 */
class ExternalResource extends DataObject implements TemplateGlobalProvider
{
    use \SilverCart\ORM\ExtensibleDataObject;
    use \SilverCart\Model\URLSegmentable;
    
    const POSITION_HTML_HEAD = 'head';
    const POSITION_HTML_BODY = 'body';
    
    const RESOURCE_GOOGLE_ANALYTICS_TRACKING_CODE  = 'GoogleAnalyticsTrackingCode';
    const RESOURCE_GOOGLE_CONVERSION_TRACKING_CODE = 'GoogleConversionTrackingCode';
    const RESOURCE_GOOGLE_WEBMASTER_CODE           = 'GoogleWebmasterCode';
    const RESOURCE_MATOMO_TRACKING_CODE            = 'MatomoTrackingCode';
    
    /**
     * Returns the globals to use in template.
     * Overwrites the default globals for Member.
     * 
     * @return array
     */
    public static function get_template_global_variables() {
        return [
            'RequireExternalResourcesForHead' => 'RequireExternalResourcesForHead',
            'RequireExternalResourcesForBody' => 'RequireExternalResourcesForBody',
        ];
    }
    
    /**
     * Returns the resource with the given $name.
     * 
     * @param string $name Name
     * 
     * @return \SilverCart\Model\CookieConsent\ExternalResource|null
     */
    public static function getByName(string $name) : ?ExternalResource
    {
        return self::get()->filter('Name', $name)->first();
    }


    /**
     * Requires the external resources for the given $position (default: @see self::POSITION_HTML_BODY).
     * 
     * @param string $position Position to require resources for
     * 
     * @return DBHTMLText
     */
    public static function RequireExternalResources(string $position = self::POSITION_HTML_BODY) : DBHTMLText
    {
        $resourceString = '';
        $resources      = self::get()->filter('Position', $position);
        foreach ($resources as $resource) {
            /* @var $resource ExternalResource */
            if (!$resource->canRequire()) {
                continue;
            }
            $resourceString .= $resource->Code;
        }
        return DBHTMLText::create()->setValue($resourceString);
    }
    
    /**
     * Requires the external resources for the HTML body.
     * 
     * @return DBHTMLText
     */
    public static function RequireExternalResourcesForBody() : DBHTMLText
    {
        return self::RequireExternalResources(self::POSITION_HTML_BODY);
    }
    
    /**
     * Requires the external resources for the HTML head.
     * 
     * @return DBHTMLText
     */
    public static function RequireExternalResourcesForHead() : DBHTMLText
    {
        return self::RequireExternalResources(self::POSITION_HTML_HEAD);
    }

    /**
     * DB table name.
     *
     * @var string
     */
    private static $table_name = 'SilverCart_CookieConsent_ExternalResource';
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'Name'               => DBVarchar::class,
        'Title'              => DBVarchar::class,
        'Description'        => DBText::class,
        'Code'               => DBText::class,
        'URLSegment'         => DBVarchar::class,
        'RestrictToLiveMode' => DBBoolean::class,
        'Position'           => 'Enum("'
            . self::POSITION_HTML_BODY . ','
            . self::POSITION_HTML_HEAD
            . '","' . self::POSITION_HTML_BODY . '")',
    ];
    /**
     * Summary fields.
     *
     * @var string[]
     */
    private static $summary_fields = [
        'Title',
        'Description',
        'RestrictToLiveMode',
    ];
    /**
     * Default resources to add.
     *
     * @var string[]
     */
    private static $defaults = [
        self::RESOURCE_GOOGLE_ANALYTICS_TRACKING_CODE  => [
            'Position'           => self::POSITION_HTML_BODY,
            'RestrictToLiveMode' => true,
        ],
        self::RESOURCE_GOOGLE_CONVERSION_TRACKING_CODE => [
            'Position'           => self::POSITION_HTML_BODY,
            'RestrictToLiveMode' => true,
        ],
        self::RESOURCE_GOOGLE_WEBMASTER_CODE           => [
            'Position'           => self::POSITION_HTML_HEAD,
            'RestrictToLiveMode' => true,
        ],
        self::RESOURCE_MATOMO_TRACKING_CODE            => [
            'Position'           => self::POSITION_HTML_BODY,
            'RestrictToLiveMode' => true,
        ],
    ];
    /**
     * Prepared default resources to add.
     *
     * @var string[]
     */
    protected static $prepared_defaults = null;


    /**
     * Returns whether the given $member can delete this record.
     * If no $member is given, the currently logged in user will be used instead.
     * 
     * @param \SilverStripe\Security\Member $member Member context
     * 
     * @return bool
     */
    public function canDelete($member = null) : bool
    {
        if ($this->isDefault()) {
            return false;
        }
        return parent::canDelete($member);
    }
    
    /**
     * Returns whether the given $member can require this record.
     * If no $member is given, the currently logged in user will be used instead.
     * 
     * @param \SilverStripe\Security\Member $member Member context
     * 
     * @return bool
     */
    public function canRequire($member = null) : bool
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }
        $can = trim((string) $this->getField('Code')) === '';
        if ($this->RestrictToLiveMode
         && !Director::isLive()
        ) {
            $can = false;
        } elseif ($this->Name === self::RESOURCE_GOOGLE_CONVERSION_TRACKING_CODE) {
            $can  = false;
            $ctrl = Controller::curr();
            if ($ctrl instanceof CheckoutStepController
             && $ctrl->getRequest()->param('Action') === 'thanks'
            ) {
                $can = true;
            }
            return $can;
        } else {
            $can = true;
        }
        return $can;
    }
    
    /**
     * Returns the field labels.
     * 
     * @param bool $includerelations Include relations?
     * 
     * @return array
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, []);
    }
    
    /**
     * Returns the CMS fields.
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fields->dataFieldByName('Name')->setDescription($this->fieldLabel('NameDesc'));
            if (!empty($this->Name)) {
                $fields->dataFieldByName('Name')->setReadonly(true);
            }
            $fields->removeByName('URLSegment');
            if ($this->isDefault()) {
                $fields->dataFieldByName('Title')->setReadonly(true);
                $fields->dataFieldByName('Description')->setReadonly(true);
            }
            $fields->dataFieldByName('RestrictToLiveMode')->setDescription($this->fieldLabel('RestrictToLiveModeDesc'));
            $positionSrc = [];
            $enumValues  = $this->dbObject('Position')->enumValues();
            foreach ($enumValues as $position) {
                $positionSrc[$position] = _t(self::class . ".Position_{$position}", $position);
            }
            $fields->dataFieldByName('Position')->setSource($positionSrc);
        });
        return parent::getCMSFields();
    }
    
    /**
     * Returns the custom ExternalResourceValidator to use for CMS field validation.
     * 
     * @return ExternalResourceValidator
     */
    public function getCMSValidator() : ExternalResourceValidator
    {
        $validator = ExternalResourceValidator::create();
        $validator->setForExternalResource($this);
        $this->extend('updateCMSValidator', $validator);
        return $validator;
    }
    
    /**
     * On before write.
     * 
     * @return void
     */
    protected function onBeforeWrite() : void
    {
        parent::onBeforeWrite();
        if (empty($this->URLSegment)) {
            $this->generateURLSegment(false);
        }
        if (!$this->exists()) {
            $index    = 2;
            $nameBase = $this->Name;
            do {
                $existing = self::get()
                        ->exclude('ID', $this->ID)
                        ->filter('Name', $this->Name)
                        ->count();
                if ($existing > 0) {
                    $this->Name = "{$nameBase}-{$index}";
                    $index++;
                }
            } while ($existing > 0);
        }
    }
    
    /**
     * Requires the default records.
     * 
     * @return void
     */
    public function requireDefaultRecords() : void
    {
        $siteConfig = SiteConfig::current_site_config();
        foreach ($this->getDefaults() as $default => $data) {
            if (self::get()->filter('Name', $default)->exists()) {
                continue;
            }
            $resource = self::create();
            $resource->Name               = $default;
            $resource->Title              = _t(self::class . ".{$default}_Name", FormField::name_to_label($default));
            $resource->Description        = _t(self::class . ".{$default}_Description", "{$default} description");
            $resource->RestrictToLiveMode = $data['RestrictToLiveMode'];
            $resource->Position           = $data['Position'];
            $resource->Code               = DBMigration::get_field_value_and_remove_field($siteConfig, $default);
            if ($resource->Description === "{$default} description") {
                $resource->Description = '';
            }
            $resource->write();
        }
    }
    
    /**
     * If not happened yet, the SiteConfig DB table column PiwikTrackingCode will be
     * renamed to MatomoTrackingCode.
     * Then all SiteConfig related external resources fields will be migrated to
     * the matching ExternalResource objects.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.09.2018
     */
    public function requireTable() : void
    {
        parent::requireTable();
        DBMigration::rename_fields(SiteConfig::singleton(), [
            'PiwikTrackingCode' => self::RESOURCE_MATOMO_TRACKING_CODE,
        ]);
    }
    
    /**
     * Returns a prepared list of default resources.
     * 
     * @return array
     */
    public function getDefaults() : array
    {
        if (self::$prepared_defaults === null) {
            self::$prepared_defaults = [];
            foreach ($this->config()->defaults as $default => $data) {
                if (is_int($default)) {
                    $default  = $data;
                    $data = [
                        'Position'           => self::POSITION_HTML_BODY,
                        'RestrictToLiveMode' => false,
                    ];
                }
                self::$prepared_defaults[$default] = $data;
            }
        }
        return self::$prepared_defaults;
    }
    
    /**
     * Returns whether this is a default record.
     * 
     * @return bool
     */
    public function isDefault() : bool
    {
        return array_key_exists($this->Name, $this->getDefaults());
    }
    
    /**
     * Returns the code.
     * Adds the possibility to update the code by extension.
     * 
     * @return string
     */
    public function getCode() : string
    {
        $code = (string) $this->getField('Code');
        $this->owner->extend('updateCode', $code);
        return (string) $code;
    }
}