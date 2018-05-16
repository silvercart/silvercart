<?php

/**
 * SiteConfig extension for cookie policy (EU law) settings.
 * 
 * @package SilverCart
 * @subpackage Admin_Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 15.05.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartCookiePolicyConfig extends DataExtension {
    
    /**
     * DB attributes.
     *
     * @var array
     */
    private static $db = [
        'CookiePolicyConfigIsActive'    => 'Boolean(1)',
        'CookiePolicyConfigPosition'    => 'Enum("BannerBottom,BannerTop,BannerTopPushDown,FloatingLeft,FloatingRight","BannerTopPushDown")',
        'CookiePolicyConfigLayout'      => 'Enum("Block,Classic,Edgeless,Wire","Block")',
        'CookiePolicyConfigBgColor'     => 'Varchar(7)',
        'CookiePolicyConfigTxtColor'    => 'Varchar(7)',
        'CookiePolicyConfigBtnColor'    => 'Varchar(7)',
        'CookiePolicyConfigBtnTxtColor' => 'Varchar(7)',
        'CookiePolicyConfigMessageText' => 'Text',
        'CookiePolicyConfigButtonText'  => 'Varchar(64)',
        'CookiePolicyConfigPolicyText'  => 'Varchar(64)',
    ];
    
    /**
     * Defaults for DB attributes.
     *
     * @var array
     */
    private static $defaults = [
        'CookiePolicyConfigIsActive'    => true,
        'CookiePolicyConfigBgColor'     => '#000000',
        'CookiePolicyConfigTxtColor'    => '#ffffff',
        'CookiePolicyConfigBtnColor'    => '#f1d600',
        'CookiePolicyConfigBtnTxtColor' => '#000000',
    ];
    
    /**
     * Updates the CMS fields.
     * 
     * @param FieldList $fields Fields to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2018
     */
    public function updateCMSFields(FieldList $fields) {
        $positionSrc = SilvercartTools::enum_i18n_labels($this->owner, 'CookiePolicyConfigPosition');
        $layoutSrc   = SilvercartTools::enum_i18n_labels($this->owner, 'CookiePolicyConfigLayout');
        
        $colorGroup = new SilvercartFieldGroup('ColorGroup', '', $fields);
        $colorGroup->push(SilvercartColorField::create('CookiePolicyConfigBgColor', $this->owner->fieldLabel('CookiePolicyConfigBgColor')));
        $colorGroup->push(SilvercartColorField::create('CookiePolicyConfigTxtColor', $this->owner->fieldLabel('CookiePolicyConfigTxtColor')));
        $colorGroup->push(SilvercartColorField::create('CookiePolicyConfigBtnColor', $this->owner->fieldLabel('CookiePolicyConfigBtnColor')));
        $colorGroup->push(SilvercartColorField::create('CookiePolicyConfigBtnTxtColor', $this->owner->fieldLabel('CookiePolicyConfigBtnTxtColor')));
        
        $cookyPolicyTab = $fields->findOrMakeTab('Root.CookiePolicy', $this->owner->fieldLabel('CookiePolicy'));
        $cookyPolicyTab->push(CheckboxField::create('CookiePolicyConfigIsActive', $this->owner->fieldLabel('CookiePolicyConfigIsActive')));
        $cookyPolicyTab->push(TextareaField::create('CookiePolicyConfigMessageText', $this->owner->fieldLabel('CookiePolicyConfigMessageText')));
        $cookyPolicyTab->push(TextField::create('CookiePolicyConfigButtonText', $this->owner->fieldLabel('CookiePolicyConfigButtonText')));
        $cookyPolicyTab->push(TextField::create('CookiePolicyConfigPolicyText', $this->owner->fieldLabel('CookiePolicyConfigPolicyText')));
        $cookyPolicyTab->push(DropdownField::create('CookiePolicyConfigPosition', $this->owner->fieldLabel('CookiePolicyConfigPosition'))->setSource($positionSrc));
        $cookyPolicyTab->push(DropdownField::create('CookiePolicyConfigLayout', $this->owner->fieldLabel('CookiePolicyConfigLayout'))->setSource($layoutSrc));
        $cookyPolicyTab->push($colorGroup);
        
        $this->owner->extend('updateCookiePolicyFields', $cookyPolicyTab);
    }
    
    /**
     * Updates the field labels.
     * 
     * @param array &$labels Labels to update
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2018
     */
    public function updateFieldLabels(&$labels) {
        $labels = array_merge(
                $labels,
                SilvercartTools::field_labels_for(get_class($this)),
                SilvercartTools::enum_field_labels_for($this->owner, 'CookiePolicyConfigPosition', get_class($this)),
                SilvercartTools::enum_field_labels_for($this->owner, 'CookiePolicyConfigLayout', get_class($this)),
                [
                    'CookiePolicy' => _t(get_class($this) . '.CookiePolicy', 'Cookie Policy'),
                ]
        );
    }
    
    /**
     * Sets the default values if empty.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2018
     */
    public function requireDefaultRecords() {
        $config = SiteConfig::current_site_config();
        if ($config instanceof SiteConfig &&
            $config->exists() &&
            empty($config->CookiePolicyConfigMessageText)) {
            
            $this->setDefaultValuesFor($config);
        }
    }
    
    /**
     * Sets the default values for the given SiteConfig
     * 
     * @param SiteConfig $config SiteConfig
     * 
     * @return void
     */
    protected function setDefaultValuesFor(SiteConfig $config) {
        $defaults = $config->config()->get('defaults');
        $config->CookiePolicyConfigBgColor     = $defaults['CookiePolicyConfigBgColor'];
        $config->CookiePolicyConfigTxtColor    = $defaults['CookiePolicyConfigTxtColor'];
        $config->CookiePolicyConfigBtnColor    = $defaults['CookiePolicyConfigBtnColor'];
        $config->CookiePolicyConfigBtnTxtColor = $defaults['CookiePolicyConfigBtnTxtColor'];
        $config->CookiePolicyConfigMessageText = _t(get_class($this) . '.CookiePolicyConfigMessageTextDefault');
        $config->CookiePolicyConfigButtonText  = _t(get_class($this) . '.CookiePolicyConfigButtonTextDefault');
        $config->CookiePolicyConfigPolicyText  = _t(get_class($this) . '.CookiePolicyConfigPolicyTextDefault');
        $config->write();
    }

    /**
     * Fills the cookie policy configuration with the default texts for the 
     * current i18n context if they are filled with non i18n defaults.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.05.2018
     */
    protected function checkDefaultText() {
        if ($this->owner->CookiePolicyConfigMessageText == FormField::name_to_label('CookiePolicyConfigMessageTextDefault')) {
            $currentLocale = i18n::get_locale();
            Translatable::disable_locale_filter();
            $configs = SiteConfig::get();
            foreach ($configs as $config) {
                i18n::set_locale($config->Locale);
                $this->setDefaultValuesFor($config);
            }
            Translatable::enable_locale_filter();
            i18n::set_locale($currentLocale);
        }
    }

    /**
     * Returns the basic configuration parameters for cookie consent.
     * 
     * @return array
     */
    public function getCookiePolicyPositionConfig() {
        $this->checkDefaultText();
        $bgColor     = $this->owner->CookiePolicyConfigBgColor;
        $txtColor    = $this->owner->CookiePolicyConfigTxtColor;
        $btnColor    = $this->owner->CookiePolicyConfigBtnColor;
        $btnTxtColor = $this->owner->CookiePolicyConfigBtnTxtColor;
        
        $cfg = [];
        $cfg['palette'] = [
            'popup' => [
                'background' => $bgColor,
                'text'       => $txtColor,
            ],
            'button' => [
                'background' => $btnColor,
                'text'       => $btnTxtColor,
            ],
        ];
        $cfg['content'] = [
            'message' => $this->owner->CookiePolicyConfigMessageText,
            'dismiss' => $this->owner->CookiePolicyConfigButtonText,
            'link'    => $this->owner->CookiePolicyConfigPolicyText,
        ];
        $dataPrivacyPage = SilvercartTools::PageByIdentifierCode('DataPrivacyStatementPage');
        if (!($dataPrivacyPage instanceof Page)) {
            $dataPrivacyPage = SilvercartTools::PageByIdentifierCode('SilvercartDataPrivacyStatementPage');
        }
        if ($dataPrivacyPage instanceof Page) {
            $cfg['content']['href'] = $dataPrivacyPage->Link();
        }
        switch ($this->owner->CookiePolicyConfigPosition) {
            case 'BannerTop':
                $cfg['position'] = 'top';
                break;
            case 'BannerTopPushDown':
                $cfg['position'] = 'top';
                $cfg['static']   = true;
                break;
            case 'FloatingLeft':
                $cfg['position'] = 'bottom-left';
                break;
            case 'FloatingRight':
                $cfg['position'] = 'bottom-right';
                break;
            case 'BannerBottom':
            default:
                break;
        }
        switch ($this->owner->CookiePolicyConfigLayout) {
            case 'Classic':
                $cfg['theme'] = 'classic';
                break;
            case 'Edgeless':
                $cfg['theme'] = 'edgeless';
                break;
            case 'Wire':
                $cfg['palette']['button'] = [
                    'background' => 'transparent',
                    'text'       => $btnColor,
                    'border'     => $btnColor,
                ];
                break;
            case 'Block':
            default:
                break;
        }
        return $cfg;
    }
    
    /**
     * Loads the JS and CSS requirements.
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.05.2018
     */
    public static function load_requirements() {
        $config = SiteConfig::current_site_config();
        $cfg    = $config->getCookiePolicyPositionConfig();
        $json   = json_encode($cfg);
        $js     = 'window.addEventListener("load", function(){window.cookieconsent.initialise(' . $json . ')});';
        Requirements::css('//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css');
        Requirements::javascript('//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js');
        Requirements::customScript($js, 'cookieconsent');
    }
    
}
