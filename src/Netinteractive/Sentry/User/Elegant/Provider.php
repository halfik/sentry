<?php namespace Netinteractive\Sentry\User\Elegant;

use Netinteractive\Sentry\User\ProviderInterface;
use Netinteractive\Elegant\Mapper\MapperInterface;
use Netinteractive\Sentry\Hashing\HasherInterface;
use Netinteractive\Sentry\User\UserNotFoundException;
use Netinteractive\Sentry\Role\RoleInterface;

class Provider  implements ProviderInterface
{
    /**
     * @var \Netinteractive\Elegant\Mapper\MapperInterface
     */
    protected $mapper;

    /**
     * The hasher for the password.
     *
     * @var \Netinteractive\Sentry\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model = 'Netinteractive\Sentry\User\Elegant\Record';



    public function __construct(HasherInterface $hasher, $model=null)
    {
        if (isset($model)) {
            $this->model = $model;
        }

        $this->mapper = \App::make('ni.elegant.mapper.db', array(array($this->model)));
        $this->hasher = $hasher;
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
    public function getMapper(){
        return $this->mapper;
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
        // TODO: Implement findByLogin() method.
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
        // TODO: Implement findByCredentials() method.
    }

    /**
     * Finds a user by the given activation code.
     *
     * @param  string $code
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function findByActivationCode($code)
    {
        // TODO: Implement findByActivationCode() method.
    }

    /**
     * Finds a user by the given reset password code.
     *
     * @param  string $code
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws RuntimeException
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     */
    public function findByResetPasswordCode($code)
    {
        // TODO: Implement findByResetPasswordCode() method.
    }

    /**
     * Returns an all users.
     *
     * @return array
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * Returns all users who belong to
     * a group.
     *
     * @param  \Netinteractive\Sentry\Role\RoleInterface $group
     * @return array
     */
    public function findAllInGroup(RoleInterface $group)
    {
        // TODO: Implement findAllInGroup() method.
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