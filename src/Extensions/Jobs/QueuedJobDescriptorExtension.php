<?php

namespace SilverCart\Extensions\Jobs;

use Moo\HasOneSelector\Form\Field as HasOneSelector;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;
use Symbiote\QueuedJobs\DataObjects\QueuedJobDescriptor;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use function singleton;

/**
 * Extension for Symbiote QueuedJobDescriptor.
 * 
 * @package SilverCart
 * @subpackage Extensions\Jobs
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.06.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property QueuedJobDescriptor $owner Owner
 */
class QueuedJobDescriptorExtension extends Extension
{
    public function updateCMSFields_(FieldList $fields) : void
    {
        if (class_exists(HasOneSelector::class)) {
            $runAsField = HasOneSelector::create('RunAs2', $this->owner->fieldLabel('RunAs'), $this->owner, Member::class)->setLeftTitle($this->owner->fieldLabel('RunAs'));
            $runAsField->removeAddable();
            $fields->addFieldToTab('Root.Main', $runAsField);
            $fields->dataFieldByName('RunAsID')->setReadonly(true);
            //$fields->replaceField('RunAsID', $runAsField);
            //$fields->insertAfter('RunAs', \SilverStripe\Forms\HiddenField::create('RunAsID', 'RunAsID', $this->owner->RunAsID));
        }
    }
    /**
     * Requires all default jobs.
     * 
     * @return void
     */
    public function onAfterBuild() : void
    {
        $descendants = (array) ClassInfo::subclassesFor(AbstractQueuedJob::class);
        foreach ($descendants as $class) {
            if (!class_exists($class)
             || !method_exists($class, 'requireDefaultJob')
            ) {
                continue;
            }
            singleton($class)->requireDefaultJob();
        }
    }
}
