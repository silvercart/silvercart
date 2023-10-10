<?php

namespace SilverCart\Extensions\Jobs;

use Moo\HasOneSelector\Form\Field as HasOneSelector;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
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
    /**
     * Updates the GridFieldDetailForm.
     *
     * @param GridFieldDetailForm $form Form to update
     * 
     * @return void
     */
    public function updateGridFieldDetailForm(GridFieldDetailForm $form) : void
    {
        if (class_exists(HasOneSelector::class)) {
            $fields     = $this->owner->getCMSFields();
            $runAsField = HasOneSelector::create('RunAs', $this->owner->fieldLabel('RunAs'), $this->owner, Member::class)->setLeftTitle($this->owner->fieldLabel('RunAs'));
            $runAsField->removeAddable();
            $fields->replaceField('RunAsID', $runAsField);
            $form->setFields($fields);
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
