<?php namespace Netinteractive\Sentry\User\Elegant;


class Record extends \Netinteractive\Elegant\Model\Record
{
    /**
     * The user merged permissions.
     *
     * @var array
     */
    protected $mergedPermissions;

    /**
     * @return $this
     */
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }

    /**
     * See if a user has access to the passed permission(s).
     * Permissions are merged from all groups the user belongs to
     * and then are checked against the passed permission(s).
     *
     * If multiple permissions are passed, the user must
     * have access to all permissions passed through, unless the
     * "all" flag is set to false.
     *
     * Super users have access no matter what.
     *
     * @param  string|array  $permissions
     * @param  bool  $all
     * @return bool
     */
    public function hasAccess($permissions, $all = true)
    {
        if ($this->isSuperUser()) {
            return true;
        }

        return $this->hasPermission($permissions, $all);
    }

    /**
     * Checks if the user is a super user - has
     * access to everything regardless of permissions.
     *
     * @return bool
     */
    public function isSuperUser()
    {
        $roles = $this->getRoles();

        foreach ($roles AS $role){
            if ($role->getCode() == \Netinteractive\Sentry\Role\Elegant\Blueprint::$superUserCode){
                return true;
            }
        }


        return false;
    }

    /**
     * See if a user has access to the passed permission(s).
     * Permissions are merged from all groups the user belongs to
     * and then are checked against the passed permission(s).
     *
     * If multiple permissions are passed, the user must
     * have access to all permissions passed through, unless the
     * "all" flag is set to false.
     *
     * Super users DON'T have access no matter what.
     *
     * @param  string|array  $permissions
     * @param  bool  $all
     * @return bool
     */
    public function hasPermission($permissions, $all = true)
    {
        $mergedPermissions = $this->getMergedPermissions();

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

                foreach ($mergedPermissions as $mergedPermission => $value) {
                    // Strip the '*' off the end of the permission.
                    $checkPermission = substr($permission, 0, -1);

                    // We will make sure that the merged permission does not
                    // exactly match our permission, but starts with it.
                    if ($checkPermission != $mergedPermission and starts_with($mergedPermission, $checkPermission) and $value == 1)
                    {
                        $matched = true;
                        break;
                    }
                }
            }
            elseif ((strlen($permission) > 1) and starts_with($permission, '*')) {
                $matched = false;

                foreach ($mergedPermissions as $mergedPermission => $value) {
                    // Strip the '*' off the beginning of the permission.
                    $checkPermission = substr($permission, 1);

                    // We will make sure that the merged permission does not
                    // exactly match our permission, but ends with it.
                    if ($checkPermission != $mergedPermission and ends_with($mergedPermission, $checkPermission) and $value == 1) {
                        $matched = true;
                        break;
                    }
                }
            }
            else {
                $matched = false;

                foreach ($mergedPermissions as $mergedPermission => $value) {
                    // This time check if the mergedPermission ends in wildcard "*" symbol.
                    if ((strlen($mergedPermission) > 1) and ends_with($mergedPermission, '*')) {
                        $matched = false;

                        // Strip the '*' off the end of the permission.
                        $checkMergedPermission = substr($mergedPermission, 0, -1);

                        // We will make sure that the merged permission does not
                        // exactly match our permission, but starts with it.
                        if ($checkMergedPermission != $permission and starts_with($permission, $checkMergedPermission) and $value == 1)
                        {
                            $matched = true;
                            break;
                        }
                    }

                    // Otherwise, we'll fallback to standard permissions checking where
                    // we match that permissions explicitly exist.
                    elseif ($permission == $mergedPermission and $mergedPermissions[$permission] == 1) {
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

        if ($all === false) {
            return false;
        }

        return true;
    }


    /**
     * Returns an array of groups which the given
     * user belongs to.
     *
     * @return array
     */
    public function getRoles()
    {
        if (!$this->roles) {
            $this->setRelated('roles',  $this->roles()->get());
        }

        return $this->roles;
    }



    /**
     * Returns an array of merged permissions for each
     * group the user is in.
     *
     * @return array
     */
    public function getMergedPermissions()
    {
        if (!$this->mergedPermissions) {
            $permissions = array();

            foreach ($this->getRoles() as $role){
                $permissions = array_merge($permissions, $role->getPermissions());
            }

            $this->mergedPermissions = array_merge($permissions, $this->getPermissions());
        }

        return $this->mergedPermissions;
    }

    /**
     * Returns permissions for the user.
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
} 