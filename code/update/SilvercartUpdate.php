<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Update
 */

/**
 * Handles the updates of SilverCart which can't be handled by SilverStipes
 * default logic.
 * Provides the default logic and db fields for all updates.
 * Translations for status and status messages have to follow the convention:
 * <code>
 * $lang['en_US']['SilvercartUpdate']['STATUS_DONE'] = 'Done';
 * $lang['en_US']['SilvercartUpdate']['STATUSMESSAGE_DONE'] = 'This update was successfully completed.';
 * </code>
 * The key to get a translation of a status is 'STATUS_{STATUSTEXT}'.
 * The key to get a translation of a status message is 'STATUSMESSAGE_{STATUSTEXT}'.
 *
 * @package Silvercart
 * @subpackage Update
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 25.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate extends DataObject {

    public static $db = array(
        'SilvercartVersion' => 'VarChar(8)',
        'SilvercartUpdateVersion' => 'VarChar(8)',
        'Status' => 'VarChar(16)',
        'StatusMessage' => 'VarChar(255)',
        'Description' => 'Text',
    );

    /**
     * The default status is remaining.
     *
     * @var array
     */
    public static $defaults = array(
        'Status' => 'remaining',
    );

    public static $casting = array(
        'TranslatedStatus' => 'VarChar',
    );


    /**
     * Sets the default sort order.
     *
     * @var string
     */
    public static $default_sort = 'SilvercartVersion DESC, SilvercartUpdateVersion DESC';

    /**
     * Sets the required updates for a specific update version.
     *
     * @example Update 0.9 - 7 requires specific changes done by update 0.9 - 5.
     * <code>
     * |public static $required_updates = array(
     * |    '0.9' => '5'
     * |);
     * </code>
     * @example Update 0.9 - 8 requires specific changes done by update 0.9 - 5 and 0.9 - 7.
     * <code>
     * |public static $required_updates = array(
     * |    '0.9' => array(
     * |        '5',
     * |        '7',
     * |    )
     * |);
     * </code>
     * @example Update 1.0 - 3 requires specific changes done by update 0.9 - 8, 1.0 - 1 and 1.0 - 2.
     * <code>
     * |public static $required_updates = array(
     * |    '0.9' => '8',
     * |    '1.0' => array(
     * |        '1',
     * |        '2'
     * |    )
     * |);
     * </code>
     *
     *
     * @var array
     */
    public static $required_updates = array();

    /**
     * Construct a new DataObject.
     *
     * @param array $record      Array of field values.
     * @param bool  $isSingleton This this to true if this is a singleton() object.
     */
    public function __construct($record = false, $isSingleton = false) {
        parent::__construct($record, $isSingleton);
        $className = $this->ClassName;
        if ($className == 'SilvercartUpdate' || $this->isInDB()) {
            $error = '';
        } elseif (!method_exists($this, 'executeUpdate')) {
            // method executeUpdate does not exist, trigger error
            $error = 'Method executeUpdate not found in class ' . $className;
        } elseif ($record === false) {
            if (!DataObject::get_one($className)) {
                $updateDefaults = $className::$defaults;
                if (!array_key_exists('SilvercartVersion', $updateDefaults)
                 || empty ($updateDefaults['SilvercartVersion'])) {
                    // default value for SilvercartVersion is not set, trigger error
                    $error = 'Default value for SilvercartVersion not set in class ' . $className;
                } elseif (!array_key_exists('SilvercartUpdateVersion', $updateDefaults)
                 || empty ($updateDefaults['SilvercartUpdateVersion'])) {
                    // default value for SilvercartUpdateVersion is not set, trigger error
                    $error = 'Default value for SilvercartUpdateVersion not set in class ' . $className;
                } elseif (DataObject::get_one('SilvercartUpdate', sprintf("`SilvercartVersion`='%s' AND `SilvercartUpdateVersion`='%s'", $updateDefaults['SilvercartVersion'], $updateDefaults['SilvercartUpdateVersion']))) {
                    // update already exists, trigger error
                    $error = 'SilvercartUpdateVersion ' . $updateDefaults['SilvercartUpdateVersion'] . ' already exists for SilvercartVersion ' . $updateDefaults['SilvercartVersion'] . ' (defined in class ' . $className . ')';
                }
            }
        }

        if (!empty($error)) {
            trigger_error($error, E_USER_ERROR);
            exit();
        }
    }

    /**
     * Remove permission to edit for all members.
     *
     * @param Member $member Member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function canEdit($member = null) {
        return false;
    }

    /**
     * Remove permission to delete for all members.
     *
     * @param Member $member Member
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function canDelete($member = null) {
        return false;
    }

    /**
     * Builds the default records for the update classes (singleton). The default
     * values should be specified in the extended update classes.
     *
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function  requireDefaultRecords() {
        if ($this->ClassName == 'SilvercartUpdate'
         || DataObject::get_one($this->ClassName)) {
            return;
        }
        parent::requireDefaultRecords();
        $requiredUpdate = new $this->ClassName();
        $requiredUpdate->StatusMessage = _t('SilvercartUpdate.STATUSMESSAGE_REMAINING');
        $requiredUpdate->write();
        $config = SilvercartConfig::getConfig();
        $config->SilvercartVersion;
        $config->SilvercartUpdateVersion;
        if (version_compare($config->SilvercartVersion, $requiredUpdate->SilvercartVersion, '>')
         || (version_compare($config->SilvercartVersion, $requiredUpdate->SilvercartVersion, '==')
          && $config->SilvercartUpdateVersion >= $requiredUpdate->SilvercartUpdateVersion)) {
            $requiredUpdate->skip();
        }
    }

    /**
     * Field labels for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function fieldLabels() {
        return array_merge(
                parent::fieldLabels(),
                array(
                    'SilvercartVersion'         => _t('SilvercartUpdate.SILVERCARTVERSION'),
                    'SilvercartUpdateVersion'   => _t('SilvercartUpdate.SILVERCARTUPDATEVERSION'),
                    'Status'                    => _t('SilvercartUpdate.STATUS'),
                    'StatusMessage'             => _t('SilvercartUpdate.STATUSMESSAGE'),
                    'Description'               => _t('SilvercartUpdate.DESCRIPTION'),
                    'TranslatedStatus'          => _t('SilvercartUpdate.STATUS'),
                )
        );
    }

    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function summaryFields() {
        return array(
            'SilvercartVersion'         => _t('SilvercartUpdate.SILVERCARTVERSION'),
            'SilvercartUpdateVersion'   => _t('SilvercartUpdate.SILVERCARTUPDATEVERSION'),
            'TranslatedStatus'          => _t('SilvercartUpdate.STATUS'),
            'StatusMessage'             => _t('SilvercartUpdate.STATUSMESSAGE'),
            'Description'               => _t('SilvercartUpdate.DESCRIPTION'),
        );
    }

    /**
     * Returns the translation for the updates status.
     *
     * @return string
     */
    public function getTranslatedStatus() {
        return _t('SilvercartUpdate.STATUS_' . strtoupper($this->Status), $this->Status);
    }

    /**
     * Sets the update status to done.
     * Optionally, you can put a status message.
     *
     * @param string $statusMessage The status message to write with the status.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function done($statusMessage = '') {
        $this->updateStatus('done', $statusMessage);
        $config = SilvercartConfig::getConfig();
        $config->SilvercartVersion = $this->SilvercartVersion;
        $config->SilvercartUpdateVersion = $this->SilvercartUpdateVersion;
        $config->write();
    }

    /**
     * Sets the update status to skipped.
     * Optionally, you can put a status message.
     *
     * @param string $statusMessage The status message to write with the status.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function skip($statusMessage = '') {
        $this->updateStatus('skipped', $statusMessage);
    }

    /**
     * Sets the update status to skipped to prevent damage on existing data.
     * Optionally, you can put a status message.
     *
     * @param string $statusMessage The status message to write with the status.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function skipToPreventDamage($statusMessage = '') {
        if (empty ($statusMessage)) {
            $statusMessage = _t('SilvercartUpdate.STATUSMESSAGE_SKIPPED_TO_PREVENT_DAMAGE');
        }
        $this->updateStatus('skipped', $statusMessage);
        $config = SilvercartConfig::getConfig();
        $config->SilvercartVersion = $this->SilvercartVersion;
        $config->SilvercartUpdateVersion = $this->SilvercartUpdateVersion;
        $config->write();
    }

    /**
     * Sets the update status to error.
     * Optionally, you can put a status message.
     *
     * @param string $statusMessage The status message to write with the status.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function error($statusMessage = '') {
        $this->updateStatus('error', $statusMessage);
    }

    /**
     * Sets the update status.
     * Optionally, you can put a status message.
     *
     * @param string $status        The status to write.
     * @param string $statusMessage The status message to write with the status.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function updateStatus($status, $statusMessage = '') {
        if (empty($statusMessage)) {
            $statusMessage = _t('SilvercartUpdate.STATUSMESSAGE_' . strtoupper($status));
        }
        $this->Status = $status;
        $this->StatusMessage = $statusMessage;
        $this->write();
    }

    /**
     * Executes the update for a specific (update) version.
     * Base check whether the update is already done, skipped or still remaining.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.03.2011
     */
    public function doUpdate() {
        if (DataObject::get_one($this->ClassName, "`Status`='remaining'")) {
            $config = SilvercartConfig::getConfig();
            $config->SilvercartVersion;
            $config->SilvercartUpdateVersion;
            if (version_compare($config->SilvercartVersion, $this->SilvercartVersion, '>')
             || (version_compare($config->SilvercartVersion, $this->SilvercartVersion, '==')
              && $config->SilvercartUpdateVersion >= $this->SilvercartUpdateVersion)) {
                $this->skip();
            } elseif (!empty (self::$required_updates)) {
                ksort(self::$required_updates);
                foreach (self::$required_updates as $version => $updateVersions) {
                    if (is_array($updateVersions)) {
                        sort($updateVersions);
                    } else {
                        $updateVersions = array($updateVersions);
                    }
                    foreach ($updateVersions as $updateVersion) {
                        if (is_numeric($updateVersion)) {
                            $requiredUpdate = DataObject::get('SilvercartUpdate', sprintf("`Status`='remaining' AND `SilvercartVersion`='%s' AND `SilvercartUpdateVersion`='%s'", $version, $updateVersion));
                            if ($requiredUpdate) {
                                $requiredUpdate->doUpdate();
                            }
                        }
                    }
                }
                if ($this->executeUpdate()) {
                    $this->done();
                }
            } elseif ($this->executeUpdate()) {
                $this->done();
            } elseif ($this->Status == 'remaining') {
                $this->error();
            }

        }
    }

    /**
     * Executes all remaining updates.
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public static function doAllUpdates() {
        foreach (DataObject::get('SilvercartUpdate', "`Status`='remaining'") as $update) {
            $update->doUpdate();
        }
    }

}