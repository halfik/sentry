<?php namespace Netinteractive\Sentry\User\Elegant;

use Netinteractive\Sentry\User\ProviderInterface;
use Netinteractive\Elegant\Mapper\MapperInterface;
use Netinteractive\Elegant\Model\Provider AS BusinessProvider;
use Netinteractive\Sentry\User\UserNotFoundException;
use Netinteractive\Sentry\Role\RoleInterface;
use Netinteractive\Sentry\User\WrongPasswordException;

class Provider extends BusinessProvider implements ProviderInterface
{
    /**
     * @param null|string $record
     */
    public function __construct($record=null)
    {
        if (!$record){
            $record = 'Netinteractive\Sentry\User\Elegant\Record';
        }
        parent::__construct($record);
    }

    /**
     * Creates a user.
     *
     * @param  array  $credentials
     * @return \Netinteractive\Sentry\User\UserInterface
     */
    public function create(array $credentials)
    {
        return parent::create($credentials);
    }

    /**
     * Returns an empty user object.
     *
     * @return \Netinteractive\Sentry\User\UserInterface
     */
    public function getEmptyUser()
    {
        return $this->createRecord();
    }


    /**
     * Finds a user by the given user ID.
     *
     * @param  mixed $id
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     */
    public function findById($id)
    {
        if ( ! $user = $this->getMapper()->find($id))
        {
            throw new UserNotFoundException( sprintf( _("Nie znaleziono użytkownika o ID [%s]."), $id) );
        }

        return $user;
    }

    /**
     * Finds a user by the login value.
     *
     * @param  string $login
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     */
    public function findByLogin($login)
    {
        if ( !$user = $this->getMapper()->login($login)->first()) {
            throw new UserNotFoundException( sprintf( _("Nie znaleziono użytkownika o loginie [%s]."), $login ));
        }

        return $user;
    }


    /**
     * Finds a user by the email value.
     *
     * @param  string  $email
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     */
    public function findByEmail($email)
    {
        if ( !$user = $this->getMapper()->email($email)->first()) {
            throw new UserNotFoundException( sprintf( _("Nie znaleziono użytkownika z adresem email [%s]."), $email ));
        }

        return $user;
    }

    /**
     * Finds a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     */
    public function findByCredentials(array $credentials)
    {
        $record     = $this->createRecord();
        $loginName = $record->getBlueprint()->getLoginName();

        if ( ! array_key_exists($loginName, $credentials)) {
            throw new \InvalidArgumentException( sprintf( _("Nie dostarczono pola login [%s]."), $loginName));
        }

        $passwordName = $record->getBlueprint()->getPasswordName();


        $hashableAttributes = $record->getBlueprint()->getHashableAttributes();
        $hashedCredentials  = array();

        // build query from given credentials
        foreach ($credentials as $credential => $value) {
            // Remove hashed attributes to check later as we need to check these
            // values after we retrieved them because of salts
            if (in_array($credential, $hashableAttributes)) {
                $hashedCredentials = array_merge($hashedCredentials, array($credential => $value));
            }
            else {
                $this->getMapper()->where($credential, '=', $value);
            }
        }

        if ( ! $user = $this->getMapper()->first()){
            throw new UserNotFoundException( _("Nie znaleziono użytkownika."));
        }

        // Now check the hashed credentials match ours
        foreach ($hashedCredentials as $credential => $value) {
            if ( ! $record->getBlueprint()->getHasher()->checkhash($value, $user->{$credential})) {
                $message = sprintf( _("A user was found to match all plain text credentials however hashed credential [%s] did not match."), $credential);

                if ($credential == $passwordName) {
                    throw new WrongPasswordException($message);
                }

                throw new UserNotFoundException($message);
            }
            else if ($credential == $passwordName) {
                if (method_exists($record->getBlueprint()->getHasher(), 'needsRehashed') &&
                    $record->getBlueprint()->getHasher()->needsRehashed($user->{$credential}))
                {
                    // The algorithm used to create the hash is outdated and insecure.
                    // Rehash the password and save.
                    $user->{$credential} = $value;
                    $this->getMapper()->save($user);
                }
            }
        }

        return $user;
    }

    /**
     * Finds a user by the given activation code.
     *
     * @param  string $code
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function findByActivationCode($code)
    {
        if ( !$code) {
            throw new \InvalidArgumentException( _("Nie przekazano kodu aktywacyjnego.") );
        }

        $result = $this->getMapper()->activationCode($code)->get();


        if (($count = $result->count()) > 1) {
            throw new \RuntimeException( sprintf( _("Znaleziono [%s] użytkowników z identycznym kodem aktywacyjnym."), $code) );
        }

        if ( ! $user = $result->first()) {
            throw new UserNotFoundException( _("Nie znaleziono użytkownika.") );
        }

        return $user;
    }

    /**
     * Finds a user by the given reset password code.
     *
     * @param  string $code
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws \RuntimeException
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     */
    public function findByResetPasswordCode($code)
    {
        $result = $this->getMapper()->resetPasswordCode($code)->get();

        if (($count = $result->count()) > 1) {
            throw new \RuntimeException( sprintf(_("Znaleziono [%s] użytkowników z identycznym kodem resetującym hasło."), $count) );
        }

        if ( ! $user = $result->first()) {
            throw new UserNotFoundException( _("Nie znaleziono użytkownika.") );
        }

        return $user;
    }

    /**
     * Returns an all users.
     *
     * @return \Netinteractive\Elegant\Model\Collection
     */
    public function findAll()
    {
        return $this->getMapper()->get();
    }

    /**
     * Returns all users with access to
     * a permission(s).
     *
     * @param  string|array  $permissions
     * @return array
     */
    public function findAllWithAccess($permissions)
    {
        return array_filter($this->findAll(), function($user) use ($permissions)
        {
            return $user->hasAccess($permissions);
        });
    }

    /**
     * Returns all users with access to
     * any given permission(s).
     *
     * @param  array  $permissions
     * @return array
     */
    public function findAllWithAnyAccess(array $permissions)
    {
        return array_filter($this->findAll(), function($user) use ($permissions)
        {
            return $user->hasAnyAccess($permissions);
        });
    }


    /**
     * Returns all users who belong to
     * a group.
     *
     * @param  string $code
     * @return array
     */
    public function findAllWithRole( $code)
    {
        $config =  \Config::get('packages.netinteractive.sentry.config');

        $privotTable = $config['user_role_pivot_table'];
        $roleTable = $config['role_table'];
        $userTable = $config['user_table'];

        return $this->getMapper()
            ->selectRaw('"'.$userTable.'".*')
            ->join($privotTable, $privotTable.'.user__id', '=', $userTable.'.id')
            ->join($roleTable, $roleTable.'.id', '=', $privotTable.'.role__id')
            ->where($roleTable.'.code', '=', $code)
            ->get()
        ;
    }
}