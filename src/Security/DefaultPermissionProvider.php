<?php

namespace SilverCart\Security;

use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use function _t;

/**
 * Trait to add the default permissions following a custom schema.
 * Required are the class constants:
 * <code>
 * public const PERMISSION_CREATE = '<PERMISSION-HOLDER-NAME>_CREATE';
 * public const PERMISSION_DELETE = '<PERMISSION-HOLDER-NAME>_DELETE';
 * public const PERMISSION_EDIT   = '<PERMISSION-HOLDER-NAME>_EDIT';
 * public const PERMISSION_VIEW   = '<PERMISSION-HOLDER-NAME>_VIEW';
 * </code>
 * To implement a proper i18n support, you should add these keys to your lang files:
 * <code>
 * en:
 *   SilverCart\CMSFieldManager\Model\CMSFieldSettings:
 *     SILVERCART_<PERMISSION-HOLDER-NAME>_CREATE: 'Create <SINGULAR-NAME>'
 *     SILVERCART_<PERMISSION-HOLDER-NAME>_CREATE_HELP: 'Allows an user to create <PLURAL-NAME>.'
 *     SILVERCART_<PERMISSION-HOLDER-NAME>_DELETE: 'Delete <SINGULAR-NAME>'
 *     SILVERCART_<PERMISSION-HOLDER-NAME>_DELETE_HELP: 'Allows an user to delete <PLURAL-NAME>.'
 *     SILVERCART_<PERMISSION-HOLDER-NAME>_EDIT: 'Edit <SINGULAR-NAME>'
 *     SILVERCART_<PERMISSION-HOLDER-NAME>_EDIT_HELP: 'Allows an user to edit <PLURAL-NAME>.'
 *     SILVERCART_<PERMISSION-HOLDER-NAME>_VIEW: 'View <SINGULAR-NAME>'
 *     SILVERCART_<PERMISSION-HOLDER-NAME>_VIEW_HELP: 'Allows an user to view <PLURAL-NAME>.'
 * </code>
 * 
 * @package SilverCart
 * @subpackage Security
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 15.05.2023
 * @copyright 2023 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait DefaultPermissionProvider
{
    /**
     * Set permissions.
     *
     * @return array
     */
    public function providePermissions() : array
    {
        $permissions = [
            self::PERMISSION_VIEW   => [
                'name'     => $this->fieldLabel(self::PERMISSION_VIEW),
                'help'     => $this->fieldLabel(self::PERMISSION_VIEW . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 10,
            ],
            self::PERMISSION_EDIT   => [
                'name'     => $this->fieldLabel(self::PERMISSION_EDIT),
                'help'     => $this->fieldLabel(self::PERMISSION_EDIT . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 20,
            ],
            self::PERMISSION_CREATE => [
                'name'     => $this->fieldLabel(self::PERMISSION_CREATE),
                'help'     => $this->fieldLabel(self::PERMISSION_CREATE . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 30,
            ],
            self::PERMISSION_DELETE => [
                'name'     => $this->fieldLabel(self::PERMISSION_DELETE),
                'help'     => $this->fieldLabel(self::PERMISSION_DELETE . '_HELP'),
                'category' => $this->i18n_singular_name(),
                'sort'     => 40,
            ],
        ];
        $this->extend('updateProvidePermissions', $permissions);
        return $permissions;
    }

    /**
     * Indicates wether the current user can view this object.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return bool
     */
    public function canView($member = null) : bool
    {
        if ($member === null) {
            $member = Security::getCurrentUser();
        }
        $can     = Permission::checkMember($member, self::PERMISSION_VIEW);
        $results = $this->extend('canView', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $can = false;
            }
        }
        return $can;
    }

    /**
     * Indicates wether the current user can edit this object.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return bool
     */
    public function canEdit($member = null) : bool
    {
        if ($member === null) {
            $member = Security::getCurrentUser();
        }
        $can     = Permission::checkMember($member, self::PERMISSION_EDIT);
        $results = $this->extend('canView', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $can = false;
            }
        }
        return $can;
    }

    /**
     * Indicates wether the current user can create this object.
     * 
     * @param Member $member  Member to check permission for.
     * @param array  $context Context
     *
     * @return bool
     */
    public function canCreate($member = null, $context = []) : bool
    {
        if ($member === null) {
            $member = Security::getCurrentUser();
        }
        $can     = Permission::checkMember($member, self::PERMISSION_CREATE);
        $results = $this->extend('canView', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $can = false;
            }
        }
        return $can;
    }

    /**
     * Indicates wether the current user can delete this object.
     * 
     * @param Member $member Member to check permission for.
     *
     * @return bool
     */
    public function canDelete($member = null) : bool
    {
        if ($member === null) {
            $member = Security::getCurrentUser();
        }
        $can     = Permission::checkMember($member, self::PERMISSION_DELETE);
        $results = $this->extend('canView', $member);
        if ($results
         && is_array($results)
        ) {
            if(!min($results)) {
                $can = false;
            }
        }
        return $can;
    }

    /**
     * Updates the field labels.
     *
     * @param array &$labels Labels to update
     *
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        array_merge($labels, [
            self::PERMISSION_CREATE           => _t(self::class . '.' . self::PERMISSION_CREATE, 'Create item'),
            self::PERMISSION_CREATE . '_HELP' => _t(self::class . '.' . self::PERMISSION_CREATE . '_HELP', 'Allows an user to create items.'),
            self::PERMISSION_DELETE           => _t(self::class . '.' . self::PERMISSION_DELETE, 'Delete items'),
            self::PERMISSION_DELETE . '_HELP' => _t(self::class . '.' . self::PERMISSION_DELETE . '_HELP', 'Allows an user to delete items.'),
            self::PERMISSION_EDIT             => _t(self::class . '.' . self::PERMISSION_EDIT, 'Edit items'),
            self::PERMISSION_EDIT . '_HELP'   => _t(self::class . '.' . self::PERMISSION_EDIT . '_HELP', 'Allows an user to edit items.'),
            self::PERMISSION_VIEW             => _t(self::class . '.' . self::PERMISSION_VIEW, 'View items'),
            self::PERMISSION_VIEW . '_HELP'   => _t(self::class . '.' . self::PERMISSION_VIEW . '_HELP', 'Allows an user to view items.'),
        ]);
    }
}