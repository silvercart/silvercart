<?php

namespace SilverCart\Dev\Tasks;

use SilverCart\Dev\ExtendedBuildTask;
use SilverCart\Dev\Tools;
use SilverCart\Model\Translation\TranslatableDataObjectExtension;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\BuildTask;
use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataObjectSchema;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBComposite;
use function singleton;

class LocaleToolsTask extends BuildTask
{
    use ExtendedBuildTask;
    /**
     * Set a custom url segment (to follow dev/tasks/)
     *
     * @var string
     */
    private static $segment = 'sc-locale-tools';
    /**
     * Shown in the overview on the {@link TaskRunner}.
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     * 
     * @var string
     */
    protected $title = 'Locale Tools';
    /**
     * Describe the implications the task has, and the changes it makes. Accepts 
     * HTML formatting.
     * 
     * @var string
     */
    protected $description = 'Task to provide some locale base dev tools.';
    /**
     * 
     *
     * @var array
     */
    private static $allowed_actions = [
        'addMissingLocaleEntries',
        'repair_translatable_data_objects',
    ];

    /****************************************************************************/
    /****************************************************************************/
    /**                                                                        **/
    /**                             ACTION SECTION                             **/
    /**                                                                        **/
    /****************************************************************************/
    /****************************************************************************/
    
    /**
     * Main action to run this task.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     */
    public function run($request) : void
    {
        $this->handleAction($request);
    }
    
    /**
     * Default action to run this task.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     */
    public function runDefault(HTTPRequest $request) : void
    {
        $this->printLine();
        $this->printMessage($this->getDescription());
        $this->printLine();
        $this->renderAddMissingLocaleEntriesForm($request);
        $this->printLine();
    }
    
    /**
     * Action to add missing locale entries.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     */
    public function addMissingLocaleEntries(HTTPRequest $request) : void
    {
        $source = $request->postVar('SourceLocale');
        $target = $request->postVar('TargetLocale');
        Tools::set_current_locale($source);
        i18n::config()->merge('default_locale', $source);
        i18n::set_locale($source);
        if (empty($source)
         || empty($target)
        ) {
            echo "<strong>Please enter a valid locale as source and target.</strong>";
            $this->printLine();
            $this->renderAddMissingLocaleEntriesForm($request);
            return;
        }
        $this->printLine();
        $this->printMessage($this->getDescription());
        $this->printLine();
        echo "<div style=\"font-family: monospace;\">";
        echo "Add missing locale entries <strong>{$source}</strong>.<br/>";
        echo "Using <strong>{$target}</strong> as source...";
        
        
        $allClasses        = ClassInfo::allClasses();
        $translatedClasses = [];
        foreach ($allClasses as $class) {
            $extensions = Config::forClass($class)->extensions;
            if (is_array($extensions)
             && in_array(TranslatableDataObjectExtension::class, $extensions)
            ) {
                $translatedClasses[] = $class;
            }
        }
        foreach ($translatedClasses as $translatedClass) {
            $dataObjects = DataObject::get($translatedClass);
            $this->printLine();
            $this->printMessage("processing <i>{$translatedClass}</i>...");
            $this->printMessage("found <i>{$dataObjects->count()}</i> data objects...");
            foreach ($dataObjects as $dataObject) {
                echo "<pre>";
                $this->printMessage("- processing #{$dataObject->ID}: {$dataObject->Title}.");
                $sourceTranslation = $dataObject->getTranslationFor($source);
                $targetTranslation = $dataObject->getTranslationFor($target);
                /* @var $sourceTranslation DataObject */
                /* @var $targetTranslation DataObject */
                if (!$sourceTranslation) {
                    $this->printMessage("  There is no translation for {$source}.");
                } else {
                    $this->printMessage("  {$source}: {$sourceTranslation->Title}");
                }
                if (!$targetTranslation) {
                    $this->printMessage("  copying {$source} to {$target}.");
                    $targetTranslation = $sourceTranslation->duplicate(false);
                    $targetTranslation->Locale = $target;
                    $targetTranslation->write();
                } else {
                    $this->printMessage("  {$target}: {$targetTranslation->Title} <i>[EXISTS]</i>");
                }
                echo "</pre>";
            }
        }
        echo "</div>";
    }
    
    /**
     * Action to repair translatable DataObjects.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return HTTPResponse
     */
    public function repair_translatable_data_objects(HTTPRequest $request) : HTTPResponse
    {
        $allClasses        = ClassInfo::allClasses();
        $translatedClasses = [];
        foreach ($allClasses as $class) {
            $extensions = Config::forClass($class)->extensions;
            if (is_array($extensions)
             && in_array(TranslatableDataObjectExtension::class, $extensions)
            ) {
                $translatedClasses[] = $class;
            }
        }
        $tables = DB::table_list();
        foreach ($translatedClasses as $translatedClass) {
            /* @var $translatedObject DataObject */
            $translatedObject   = singleton($translatedClass);
            $translatedTable    = $translatedObject->config()->table_name;
            $translationClass   = $translatedObject->getTranslationClassName();
            $translationObject  = singleton($translationClass);
            $translationTable   = $translationObject->config()->table_name;
            $relationFieldName  = $translatedObject->getRelationFieldName();
            $tRelationFieldName = $translationObject->getRelationFieldName();
            if (!in_array($translatedTable, $tables)
             || !in_array($translationTable, $tables)
            ) {
                continue;
            }
            $brokenObjects      = DB::query("SELECT Original.* FROM {$translatedTable} AS Original"
                    . " LEFT JOIN {$translationTable} AS Translation ON (Original.ID = Translation.{$relationFieldName})"
                    . " WHERE Original.ID IS NOT NULL AND Translation.ID IS NULL");
            $this->printMessage("found {$brokenObjects->numRecords()} broken record(s) for {$translatedClass}.");
            foreach ($brokenObjects as $index => $brokenObject) {
                $skip = false;
                $this->extend('updateRepairTranslatableDataObject_BrokenObject', $brokenObject, $skip);
                if ($skip) {
                    continue;
                }
                $this->printMessage(" - adding {$translationClass} with {$tRelationFieldName}: {$brokenObject['ID']}");
                $translation = new $translationClass();
                $translation->{$tRelationFieldName} = $brokenObject['ID'];
                $translation->write();
                $this->extend('onAfterRepairTranslatableDataObject_BrokenObject', $brokenObject, $translation);
            }
        }
        return HTTPResponse::create();
    }
    
    /**
     * Renders the AddMissingLocaleEntriesForm.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     */
    protected function renderAddMissingLocaleEntriesForm(HTTPRequest $request) : void
    {
        $source = $request->postVar('SourceLocale');
        $target = $request->postVar('TargetLocale');
        echo "<form name=\"AddMissingLocaleEntriesForm\" method=\"post\" action=\"{$this->BaseLink('addMissingLocaleEntries')}\" style=\"font-family: monospace;\">";
        echo "<strong>Add missing locale entries</strong>";
        echo "<hr/>";
        echo "<label for=\"SourceLocale\">Source Locale:</label> ";
        echo "<input type=\"text\" name=\"SourceLocale\" id=\"SourceLocale\" value=\"{$source}\" placeholder=\"e.g. 'de_DE'\">";
        echo "<br/>";
        echo "<label for=\"TargetLocale\">Target Locale:</label> ";
        echo "<input type=\"text\" name=\"TargetLocale\" id=\"TargetLocale\" value=\"{$target}\" placeholder=\"e.g. 'en_US'\">";
        echo "<hr/>";
        echo "<button type=\"submit\">Submit</button>";
        echo "</form>";
    }
}