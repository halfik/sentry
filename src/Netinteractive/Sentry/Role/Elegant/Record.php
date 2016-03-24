<?php
namespace Netinteractive\Sentry\Role\Elegant;

use \Netinteractive\Elegant\Model\Record AS BaseRecord;
use \Netinteractive\Sentry\Role\RoleInterface as RoleInterface;

class Record extends BaseRecord implements RoleInterface
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }

    /**
     * Returns the role's ID.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the group's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns permissions for the group.
     *
     * @return array
     */
    public function getPermissions()
    {
        if (!$this->permissions){
            return array();
        }
        return $this->permissions;
    }


    /**
     * Mutator for taking permissions.
     *
     * @param  array  $permissions
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setPermissionsAttribute(array $permissions, $overwrite=false)
    {
        // Merge permissions
        if ($overwrite == false){
            $permissions = array_merge($this->getPermissions(), $permissions);
        }

        // Loop through and adjust permissions as needed
        foreach ($permissions as $permission => &$value) {
            // Lets make sure their is a valid permission value
            if ( ! in_array($value = (int) $value, $this->getBlueprint()->getAllowedPermissionsValues())) {
                throw new \InvalidArgumentException( sprintf(_("Błędna wartość [%s] dla uprawnienia [%s]."), $value, $permission ) );
            }

            // If the value is 0, delete itif ($value === 0)
            if ($value === 0) {
                unset($permissions[$permission]);
            }
        }

        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Returns role code
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Checks if role has access to $permissions
     * @param $permissions
     * @param bool $all
     * @return bool
     */
    public function hasAccess($permissions, $all = true)
    {
        $groupPermissions = $this->getPermissions();

        if ( ! is_array($permissions)) {
            $permissions = (array) $permissions;
        }

        foreach ($permissions as $permission) {
            // We will set a flag now for whether this permission was
            // matched at all.
            $matched = true;

            // Now, let's check if the permission ends in a wildcard "*" symbol.
            // If it does, we'll check through all the merged permissions to see
            // if a permission exists which matches the wildcard.
            if ((strlen($permission) > 1) and ends_with($permission, '*')) {
                $matched = false;

                foreach ($groupPermissions as $groupPermission => $value) {
                    // Strip the '*' off the end of the permission.
                    $checkPermission = substr($permission, 0, -1);

                    // We will make sure that the merged permission does not
                    // exactly match our permission, but starts with it.
                    if ($checkPermission != $groupPermission and starts_with($groupPermission, $checkPermission) and $value == 1) {
                        $matched = true;
                        break;
                    }
                }
            }
            // Now, let's check if the permission starts in a wildcard "*" symbol.
            // If it does, we'll check through all the merged permissions to see
            // if a permission exists which matches the wildcard.
            elseif ((strlen($permission) > 1) and starts_with($permission, '*')) {
                $matched = false;

                foreach ($groupPermissions as $groupPermission => $value) {
                    // Strip the '*' off the start of the permission.
                    $checkPermission = substr($permission, 1);

                    // We will make sure that the merged permission does not
                    // exactly match our permission, but ends with it.
                    if ($checkPermission != $groupPermission and ends_with($groupPermission, $checkPermission) and $value == 1) {
                        $matched = true;
                        break;
                    }
                }
            }
            else {
                $matched = false;

                foreach ($groupPermissions as $groupPermission => $value) {
                    // This time check if the groupPermission ends in wildcard "*" symbol.
                    if ((strlen($groupPermission) > 1) and ends_with($groupPermission, '*')) {
                        $matched = false;

                        // Strip the '*' off the end of the permission.
                        $checkGroupPermission = substr($groupPermission, 0, -1);

                        // We will make sure that the merged permission does not
                        // exactly match our permission, but starts wtih it.
                        if ($checkGroupPermission != $permission and starts_with($permission, $checkGroupPermission) and $value == 1) {
                            $matched = true;
                            break;
                        }
                    }

                    // Otherwise, we'll fallback to standard permissions checking where
                    // we match that permissions explicitly exist.
                    elseif ($permission == $groupPermission and $groupPermissions[$permission] == 1) {
                        $matched = true;
                        break;
                    }
                }
            }

            // Now, we will check if we have to match all
            // permissions or any permission and return
            // accordingly.
            if ($all === true and $matched === false) {
                return false;
            }
            elseif ($all === false and $matched === true) {
                return true;
            }
        }

        return $all;

    }


}