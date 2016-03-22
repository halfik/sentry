<?php namespace Netinteractive\Sentry\User\Elegant;

use Netinteractive\Sentry\User\ProviderInterface;
use Netinteractive\Elegant\Mapper\MapperInterface;
use Netinteractive\Sentry\User\UserNotFoundException;
use Netinteractive\Sentry\Role\RoleInterface;
use Netinteractive\Sentry\User\WrongPasswordException;

class Provider  implements ProviderInterface
{
    /**
     * @var \Netinteractive\Elegant\Mapper\MapperInterface
     */
    protected $mapper;


    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model = 'Netinteractive\Sentry\User\Elegant\Record';



    public function __construct($model=null)
    {
        if (isset($model)) {
            $this->model = $model;
        }

        $this->mapper = \App::make('ni.elegant.mapper.db', array($this->model));
    }

    /**
     * @param MapperInterface $mapper
     * @return $this
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * @return mixed|MapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function createRecord()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
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
            throw new UserNotFoundException( sprintf( _("A user could not be found with ID [%s]."), $id) );
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
        $record = $this->createRecord();

        if ( !$user = $this->getMapper()->where($record->getBlueprint()->getLoginName(), '=', $login)->first()) {
            throw new UserNotFoundException( sprintf( _("A user could not be found with a login value of [%s]."), $login ));
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
            throw new \InvalidArgumentException( sprintf( _("Login attribute [%s] was not provided."), $loginName));
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
            throw new UserNotFoundException( _("A user was not found with the given credentials."));
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
            throw new \InvalidArgumentException( _("No activation code passed.") );
        }

        $result = $this->getMapper()->where('activation_code', '=', $code)->get();


        if (($count = $result->count()) > 1) {
            throw new \RuntimeException( sprintf( _("Found [%s] users with the same activation code."), $code) );
        }

        if ( ! $user = $result->first()) {
            throw new UserNotFoundException( _("A user was not found with the given activation code.") );
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
        $result = $this->getMapper()->where('reset_password_code', '=', $code)->get();

        if (($count = $result->count()) > 1) {
            throw new \RuntimeException( sprintf(_("Found [%s] users with the same reset password code."), $count) );
        }

        if ( ! $user = $result->first()) {
            throw new UserNotFoundException( _("A user was not found with the given reset password code.") );
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
     * Returns all users who belong to
     * a group.
     *
     * @param  string $code
     * @return array
     */
    public function findAllWithRole( $code)
    {
        $privotTable = \Config::get('netinteractive.sentry.user_role_pivot_table');
        $roleTable = \Config::get('netinteractive.sentry.role_table');
        $userTable = \Config::get('netinteractive.sentry.user_table');

        return $this->getMapper()
            ->selectRaw('"'.$userTable.'".*')
            ->join($privotTable, $privotTable.'.user__id', '=', $userTable.'.id')
            ->join($roleTable, $roleTable.'.id', '=', $privotTable.'.role__id')
            ->where($roleTable.'.code', '=', $code)
            ->get()
        ;
    }

    /**
     * Returns all users with access to
     * a permission(s).
     *
     * @param  string|array $permissions
     * @return array
     */
    public function findAllWithAccess($permissions)
    {
        // TODO: Implement findAllWithAccess() method.
    }

    /**
     * Returns all users with access to
     * any given permission(s).
     *
     * @param  array $permissions
     * @return array
     */
    public function findAllWithAnyAccess(array $permissions)
    {
        // TODO: Implement findAllWithAnyAccess() method.
    }

    /**
     * Creates a user.
     *
     * @param  array $credentials
     * @return \Netinteractive\Sentry\User\UserInterface
     */
    public function create(array $credentials)
    {
        // TODO: Implement create() method.
    }

    /**
     * Returns an empty user object.
     *
     * @return \Netinteractive\Sentry\User\UserInterface
     */
    public function getEmptyUser()
    {
        // TODO: Implement getEmptyUser() method.
    }


}