<?php

namespace SilverCart\Jobs;

use SilverCart\Services\ResetStockService;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\FieldType\DBDatetime;
use Symbiote\QueuedJobs\DataObjects\QueuedJobDescriptor;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use Symbiote\QueuedJobs\Services\QueuedJob;
use Symbiote\QueuedJobs\Services\QueuedJobService;
use function _t;

if (!class_exists(QueuedJobDescriptor::class)) {
    return;
}

/**
 * Job to reset the stock of SilverCart products from the actually assigned value
 * to the dynamically calculated value by related StockItemEntries.
 * 
 * @package SilverCart
 * @subpackage IPLocator\API
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.16.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ResetStockJob extends AbstractQueuedJob
{
    use Configurable;
    use Injectable;
    /**
     * Number of seconds between job runs. Defaults to 1 day.
     *
     * @var int
     */
    private static int $job_interval = 86400;

    /**
     * Returns the job title.
     * 
     * @return string
     */
    public function getTitle() : string
    {
        return _t(__CLASS__ . '.Title', 'Stock Corrections');
    }

    /**
     * Processes the job.
     * 
     * @return void
     */
    public function process() : void
    {
        $this->totalSteps  = 1;
        $this->currentStep = 1;
        $this->queueNextJob();
        $this->addMessage("Starting stock corrections.");
        ResetStockService::singleton()
                ->setJob($this)
                ->run();
        $this->addMessage("Done.");
        $this->isComplete = true;
    }

    /**
     * Requires the default job.
     * 
     * @return void
     */
    public function requireDefaultJob(): void
    {
        $filter = [
            'Implementation' => self::class,
            'JobStatus'      => [
                QueuedJob::STATUS_NEW,
                QueuedJob::STATUS_INIT,
                QueuedJob::STATUS_RUN
            ]
        ];
        if (QueuedJobDescriptor::get()->filter($filter)->count() > 0) {
            return;
        }
        $this->queueNextJob();
    }

    /**
     * Queues the next job.
     * 
     * @return void
     */
    private function queueNextJob() : void
    {
        $startTime = time() + self::config()->job_interval;
        QueuedJobService::singleton()->queueJob(
            self::create(),
            DBDatetime::create()->setValue($startTime)->Rfc2822()
        );
    }
}
