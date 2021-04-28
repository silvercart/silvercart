<?php

namespace SilverCart\Admin\Forms\GridField;

use League\Csv\Writer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderPosition;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_URLHandler;

/**
 * Adds an export "current month", "previous month" and  "penultimate month" button to the bottom of a {@link GridField}.
 * 
 * @package SilverCart
 * @subpackage Admin\Forms\GridField
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.04.2021
 * @copyright 2021 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldOrderPositionExportButton implements GridField_HTMLProvider, GridField_ActionProvider, GridField_URLHandler
{
    use \SilverStripe\Core\Injector\Injectable;
    
    const EXPORT_MODE_CURRENT_MONTH      = 'current-month';
    const EXPORT_MODE_PREVIOUS_MONTH     = 'previous-month';
    const EXPORT_MODE_PENULTIMATE_MONTH  = 'penultimate-month';
    
    /**
     * @var array Map of a property name on the exported objects, with values being the column title in the CSV file.
     * Note that titles are only used when {@link $csvHasHeader} is set to TRUE.
     */
    protected $exportColumns;
    /**
     * @var string
     */
    protected $csvSeparator = ",";
    /**
     * @var string
     */
    protected $csvEnclosure = '"';
    /**
     * @var bool
     */
    protected $csvHasHeader = true;
    /**
     * Fragment to write the button to
     */
    protected $targetFragment;
    /**
     * Set to true to disable XLS sanitisation
     * [SS-2017-007] Ensure all cells with leading [@=+] have a leading tab
     *
     * @config
     * @var bool
     */
    private static $xls_export_disabled = false;
    /**
     * Export mode.
     * 
     * @var string
     */
    protected $exportMode = self::EXPORT_MODE_PREVIOUS_MONTH;

    /**
     * Constructor.
     * 
     * @param string $targetFragment The HTML fragment to write the button into
     * 
     * @return GridFieldOrderPositionExportButton
     */
    public function __construct(string $exportMode = self::EXPORT_MODE_PREVIOUS_MONTH, string $targetFragment = "buttons-before-left")
    {
        $this->setExportMode($exportMode);
        $this->targetFragment = $targetFragment;
        $this->setExportColumns(OrderPosition::singleton()->exportColumns());
    }

    /**
     * Place the export button in a <p> tag below the field
     *
     * @param GridField $gridField GridField
     *
     * @return array
     */
    public function getHTMLFragments($gridField) : array
    {
        $button = GridField_FormAction::create(
            $gridField,
            $this->getExportModeAction(),
            $this->getExportModeLabel(),
            $this->getExportModeAction(),
            null
        );
        $button->addExtraClass("btn btn-secondary no-ajax font-icon-down-circled action_{$this->getExportModeAction()}");
        $button->setForm($gridField->getForm());
        return [
            $this->targetFragment => $button->Field(),
        ];
    }
    
    /**
     * Returns the action dependent on the current export mode.
     * 
     * @return string
     */
    public function getExportModeAction() : string
    {
        return "export-order-{$this->getExportMode()}";
    }
    
    /**
     * Returns the display label dependent on the current export mode.
     * 
     * @return string
     */
    public function getExportModeLabel() : string
    {
        $defaultLabel = str_replace('-', ' ', $this->getExportMode());
        return _t(self::class . ".Export-{$this->getExportMode()}", "Export {$defaultLabel}");
    }
    
    /**
     * Returns the export mode dates.
     * 
     * @return array
     */
    public function getExportModeDates() : array
    {
        $year  = date('Y');
        $month = date('n');
        if ($this->getExportMode() === self::EXPORT_MODE_PREVIOUS_MONTH) {
            $month--;
            if ($month < 1) {
                $month = 12;
                $year--;
            }
        } elseif ($this->getExportMode() === self::EXPORT_MODE_PENULTIMATE_MONTH) {
            $month -= 2;
            if ($month === 0) {
                $month = 12;
                $year--;
            } elseif ($month === -1) {
                $month = 11;
                $year--;
            }
        }
        $paddedMonth    = str_pad($month, 2, '0', STR_PAD_LEFT);
        $startDate      = "{$year}-{$paddedMonth}-01";
        $lastDayOfMonth = date('t', strtotime($startDate));
        $endDate        = "{$year}-{$paddedMonth}-{$lastDayOfMonth}";
        return [
            $startDate,
            $endDate,
        ];
    }

    /**
     * export is an action button
     * 
     * @param GridField $gridField GridField
     *
     * @return array
     */
    public function getActions($gridField) : array
    {
        return [
            'export-order-' . self::EXPORT_MODE_CURRENT_MONTH,
            'export-order-' . self::EXPORT_MODE_PREVIOUS_MONTH,
            'export-order-' . self::EXPORT_MODE_PENULTIMATE_MONTH,
        ];
    }

    /**
     * Handles the action.
     * 
     * @param GridField $gridField  GridField
     * @param string    $actionName Action name
     * @param array     $arguments  Arguments
     * @param array     $data       Data
     * 
     * @return HTTPResponse|null
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        $urlHandlers = $this->getURLHandlers($gridField);
        if (array_key_exists($actionName, $urlHandlers)) {
            return $this->{$urlHandlers[$actionName]}($gridField);
        }
        return null;
    }

    /**
     * it is also a URL
     *
     * @param GridField $gridField GridField
     *
     * @return array
     */
    public function getURLHandlers($gridField) : array
    {
        return [
            'export-order-' . self::EXPORT_MODE_CURRENT_MONTH     => 'handleExportCurrent',
            'export-order-' . self::EXPORT_MODE_PREVIOUS_MONTH    => 'handleExport',
            'export-order-' . self::EXPORT_MODE_PENULTIMATE_MONTH => 'handleExportPenultimate',
        ];
    }
    
    /**
     * Export handler for current mode.
     * 
     * @param GridField   $gridField GridField
     * @param HTTPRequest $request   HTTP request
     * 
     * @return HTTPResponse|null
     */
    public function handleExportCurrent(GridField $gridField, HTTPRequest $request = null) : ?HTTPResponse
    {
        $this->setExportMode(self::EXPORT_MODE_CURRENT_MONTH);
        return $this->handleExport($gridField, $request);
    }
    
    /**
     * Export handler for penultimate mode.
     * 
     * @param GridField   $gridField GridField
     * @param HTTPRequest $request   HTTP request
     * 
     * @return HTTPResponse|null
     */
    public function handleExportPenultimate(GridField $gridField, HTTPRequest $request = null) : ?HTTPResponse
    {
        $this->setExportMode(self::EXPORT_MODE_PENULTIMATE_MONTH);
        return $this->handleExport($gridField, $request);
    }

    /**
     * Handle the export, for both the action button and the URL
     *
     * @param GridField   $gridField GridField
     * @param HTTPRequest $request   HTTP request
     *
     * @return HTTPResponse|null
     */
    public function handleExport(GridField $gridField, HTTPRequest $request = null) : ?HTTPResponse
    {
        $dates     = $this->getExportModeDates();
        $startDate = array_shift($dates);
        $year      = date('Y', strtotime($startDate));
        $month     = date('m', strtotime($startDate));
        $now       = date('d-m-Y-H-i');
        $fileName  = "export-{$year}-{$month}-{$now}.csv";
        if ($fileData = $this->generateExportFileData($gridField)) {
            return HTTPRequest::send_file($fileData, $fileName, 'text/csv');
        }
        return null;
    }

    /**
     * Generate export fields for CSV.
     *
     * @param GridField $gridField GridField
     *
     * @return string
     */
    public function generateExportFileData($gridField) : string
    {
        $csvColumns = $this->getExportColumns();
        $csvWriter  = Writer::createFromFileObject(new \SplTempFileObject());
        $csvWriter->setDelimiter($this->getCsvSeparator());
        $csvWriter->setEnclosure($this->getCsvEnclosure());
        $csvWriter->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
        $csvWriter->setOutputBOM(Writer::BOM_UTF8);
        if (!Config::inst()->get(get_class($this), 'xls_export_disabled')) {
            $csvWriter->addFormatter(function (array $row) {
                foreach ($row as &$item) {
                    // [SS-2017-007] Sanitise XLS executable column values with a leading tab
                    if (preg_match('/^[-@=+].*/', $item)) {
                        $item = "\t" . $item;
                    }
                }
                return $row;
            });
        }

        if ($this->csvHasHeader) {
            $headers = [];
            // determine the CSV headers. If a field is callable (e.g. anonymous function) then use the
            // source name as the header instead
            foreach ($csvColumns as $columnSource => $columnHeader) {
                if (is_array($columnHeader)
                 && array_key_exists('title', $columnHeader)
                ) {
                    $headers[] = $columnHeader['title'];
                } else {
                    $headers[] = (!is_string($columnHeader) && is_callable($columnHeader)) ? $columnSource : $columnHeader;
                }
            }
            $csvWriter->insertOne($headers);
            unset($headers);
        }
        $orderTable         = Order::config()->table_name;
        $orderPositionTable = OrderPosition::config()->table_name;
        $dates              = $this->getExportModeDates();
        $startDate          = array_shift($dates);
        $endDate            = array_shift($dates);

        $items = OrderPosition::get()
                ->leftJoin(Order::config()->table_name, "{$orderTable}.ID = {$orderPositionTable}.OrderID")
                ->where("{$orderTable}.Created BETWEEN CAST('{$startDate}' AS DATE) AND CAST('{$endDate}' AS DATE)");
        foreach ($items->limit(null) as $item) {
            /* @var $item OrderPosition */
            if (!$item->hasMethod('canView')
             || $item->canView()
            ) {
                $columnData = [];
                foreach ($csvColumns as $columnSource => $columnHeader) {
                    if (!is_string($columnHeader)
                     && is_callable($columnHeader)
                    ) {
                        if ($item->hasMethod($columnSource)) {
                            $relObj = $item->{$columnSource}();
                        } else {
                            $relObj = $item->relObject($columnSource);
                        }
                        $value = $columnHeader($relObj);
                    } else {
                        $value = $gridField->getDataFieldValue($item, $columnSource);
                    }
                    $columnData[] = $value;
                }
                $csvWriter->insertOne($columnData);
            }
            if ($item->hasMethod('destroy')) {
                $item->destroy();
            }
        }
        return (string) $csvWriter;
    }

    /**
     * Returns the export columns.
     * 
     * @return array
     */
    public function getExportColumns() : array
    {
        return $this->exportColumns;
    }

    /**
     * Sets the export columns.
     * 
     * @param array $cols Export columns
     *
     * @return $this
     */
    public function setExportColumns(array $cols) : GridFieldOrderPositionExportButton
    {
        $this->exportColumns = $cols;
        return $this;
    }

    /**
     * Returns the CSV separator.
     * 
     * @return string
     */
    public function getCsvSeparator() : string
    {
        return $this->csvSeparator;
    }

    /**
     * Sets the CSV separator.
     * 
     * @param string $separator CSV separator
     *
     * @return $this
     */
    public function setCsvSeparator(string $separator) : GridFieldOrderPositionExportButton
    {
        $this->csvSeparator = $separator;
        return $this;
    }

    /**
     * Returns the CSV enclosure.
     * 
     * @return string
     */
    public function getCsvEnclosure() : string
    {
        return $this->csvEnclosure;
    }

    /**
     * Sets the CSV enclosure.
     * 
     * @param string $enclosure CSV enclosure
     *
     * @return $this
     */
    public function setCsvEnclosure(string $enclosure) : GridFieldOrderPositionExportButton
    {
        $this->csvEnclosure = $enclosure;
        return $this;
    }

    /**
     * Returns whether the CSV has a header.
     * 
     * @return bool
     */
    public function getCsvHasHeader() : bool
    {
        return $this->csvHasHeader;
    }

    /**
     * Sets whether the CSV has a header.
     * 
     * @param bool $bool CSV has header?
     *
     * @return $this
     */
    public function setCsvHasHeader(bool $bool) : GridFieldOrderPositionExportButton
    {
        $this->csvHasHeader = $bool;
        return $this;
    }
    
    /**
     * Returns the export mode.
     * 
     * @return string
     */
    public function getExportMode() : string
    {
        return $this->exportMode;
    }

    /**
     * Sets the export mode.
     * 
     * @param string $exportMode Export mode
     * 
     * @return \SilverCart\Admin\Forms\GridField\GridFieldOrderPositionExportButton
     */
    public function setExportMode(string $exportMode) : GridFieldOrderPositionExportButton
    {
        $this->exportMode = $exportMode;
        return $this;
    }
}