<?php namespace Netinteractive\Sentry\User\Elegant;


use Carbon\Carbon;
use Netinteractive\Sentry\Role\RoleInterface;
use Netinteractive\Sentry\User\UserInterface;
use Netinteractive\Sentry\User\UserAlreadyActivatedException;

class Record extends \Netinteractive\Elegant\Model\Record implements UserInterface
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

    /**
     * Returns the user's ID.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the user's password (hashed).
     *
     * @return string
     */
    public function getPassword()
    {
        $passwordField = $this->getBlueprint()->getPasswordName();

        return $this->{$passwordField};
    }

    /**
     * Check if the user is activated.
     *
     * @return bool
     */
    public function isActivated()
    {
        return $this->activated;
    }

    /**
     * Gets a code for when the user is
     * persisted to a cookie or session which
     * identifies the user.
     *
     * @return string
     */
    public function getPersistCode()
    {
        $config =  \Config::get('packages.netinteractive.sentry.config');

        if( !$this->persist_code || !$config['multiple_login'] ){
            $this->persist_code = $this->getRandomString();
        }

        return $this->persist_code;
    }

    /**
     * Gets user login
     *
     * @return string
     */
    public function getLogin()
    {
        $loginName = $this->getBlueprint()->getLoginName();
        return $this->{$loginName};
    }


    /**
     * Checks the given persist code.
     *
     * @param  string $persistCode
     * @return bool
     */
    public function checkPersistCode($persistCode)
    {
        if ( !$persistCode) {
            return false;
        }

        return $persistCode == $this->persist_code;
    }

    /**
     * Get an activation code for the given user.
     *
     * @return string
     */
    public function getActivationCode()
    {
        if( !$this->activation_code ){
            $this->activation_code = $this->getRandomString();
        }

        return  $this->activation_code;
    }

    /**
     * Attempts to activate the given user by checking
     * the activate code. If the user is activated already,
     * an Exception is thrown.
     *
     * @param  string $activationCode
     * @return bool
     * @throws \Netinteractive\Sentry\User\UserAlreadyActivatedException
     */
    public function attemptActivation($activationCode)
    {
        if ($this->activated == true) {
            throw new UserAlreadyActivatedException( _('Nie można aktywować już aktywnego użytkownika') );
        }

        if ($activationCode == $this->activation_code){
            $this->activation_code = null;
            $this->activated       = true;
            $this->activated_at    = new Carbon();
        }

        return false;
    }

    /**
     * Checks the password passed matches the user's password.
     *
     * @param  string $password
     * @return bool
     */
    public function checkPassword($password)
    {
        return $this->checkHash($password, $this->getPassword());
    }

    /**
     * Get a reset password code for the given user.
     *
     * @return string
     */
    public function getResetPasswordCode()
    {
        if( !$this->reset_password_code ) {
            $this->reset_password_code = $this->getRandomString();
        }

        return  $this->reset_password_code;
    }

    /**
     * Checks if the provided user reset password code is
     * valid without actually resetting the password.
     *
     * @param  string $resetCode
     * @return bool
     */
    public function checkResetPasswordCode($resetCode)
    {
        return ($this->reset_password_code == $resetCode);
    }

    /**
     * Attempts to reset a user's password by matching
     * the reset code generated with the user's.
     *
     * @param  string $resetCode
     * @param  string $newPassword
     * @return bool
     */
    public function attemptResetPassword($resetCode, $newPassword)
    {
        $passwordField = $this->getBlueprint()->getPasswordName();

        if ($this->checkResetPasswordCode($resetCode)) {
            $this->{$passwordField} = $newPassword;
            $this->reset_password_code = null;
        }

        return false;
    }

    /**
     * Wipes out the data associated with resetting
     * a password.
     *
     * @return $this
     */
    public function clearResetPassword()
    {
        if ($this->reset_password_code) {
            $this->reset_password_code = null;
        }

        return $this;
    }

    /**
     * Adds role to user
     *
     * @param  \Netinteractive\Sentry\Role\RoleInterface $role
     * @return $this
     */
    public function addRole(RoleInterface $role)
    {
        $this->addRelated('roles', $role);
        return $this;
    }


    /**
     * Zwraca role o najnizszej wadze (glowna role)
     * @return null|\Netinteractive\Sentry\Role\RoleInterface $role
     */
    public function getMainRole()
    {
        $mainRole = null;
        $roles = $this->getRoles();

        foreach ($roles AS $role){
            if ($mainRole == null){
                $mainRole = $role;
            }
            elseif ($mainRole->weight > $role->weight){
                $mainRole = $role;
            }
        }

        return $mainRole;
    }

    /**
     * Removes role from user
     *
     * @param  \Netinteractive\Sentry\Role\RoleInterface $role
     * @return $this
     */
    public function removeRole(RoleInterface $role)
    {
        if ($this->hasRole($role)){
            $roles = $this->getRoles();
            $newRoles = \App::make('ni.elegant.model.collection');

            foreach ($roles AS $_role){
                if ($_role->getCode() != $role->getCode()){
                    $newRoles->add($_role);
                }
            }

            $this->setRelated('roles', $newRoles);
        }

        return $this;
    }

    /**
     * Updates the user roles
     *
     * @param  \Illuminate\Database\Eloquent\Collection $roles
     * @param  bool $remove
     * @return bool
     */
    public function updateRoles($roles, $remove = false)
    {
        $this->setRelated('roles', $roles);
        return $this;
    }

    /**
     * See if the user has role
     *
     * @param  \Netinteractive\Sentry\Role\RoleInterface $role
     * @return bool
     */
    public function hasRole(RoleInterface $role)
    {
        foreach ($this->getRoles() as $_role) {
            if ($_role->getId() == $_role->getId()){
                return true;
            }
        }
    }

    /**
     * See if the user is in the given group.
     *
     * @param  string $code
     * @return bool
     */
    public function hasRoleByCode($code)
    {
        foreach ($this->getRoles() as $_role) {
            if ($_role->getCode() == $_role->getCode()){
                return true;
            }
        }
    }

    /**
     * Returns if the user has access to any of the
     * given permissions.
     *
     * @param  array $permissions
     * @return bool
     */
    public function hasAnyAccess(array $permissions)
    {
        return $this->hasAccess($permissions, false);
    }

    /**
     * Records a login for the user.
     *
     * @return $this
     */
    public function recordLogin()
    {
        $this->last_login = new Carbon();
        return $this;
    }


    /**
     * Generate a random string.
     *
     * @return string
     */
    public function getRandomString($length = 42)
    {
        // We'll check if the user has OpenSSL installed with PHP. If they do
        // we'll use a better method of getting a random string. Otherwise, we'll
        // fallback to a reasonably reliable method.
        if (function_exists('openssl_random_pseudo_bytes'))
        {
            // We generate twice as many bytes here because we want to ensure we have
            // enough after we base64 encode it to get the length we need because we
            // take out the "/", "+", and "=" characters.
            $bytes = openssl_random_pseudo_bytes($length * 2);

            // We want to stop execution if the key fails because, well, that is bad.
            if ($bytes === false)
            {
                throw new \RuntimeException('Unable to generate random string.');
            }

            return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
        }

        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    /**
     * Check string against hashed string.
     *
     * @param  string  $string
     * @param  string  $hashedString
     * @return bool
     * @throws RuntimeException
     */
    public function checkHash($string, $hashedString)
    {
        return $this->getBlueprint()->getHasher()->checkHash($string, $hashedString);
    }

} 