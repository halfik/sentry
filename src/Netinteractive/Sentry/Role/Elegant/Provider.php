<?php
namespace Netinteractive\Sentry\Role\Elegant;

use Netinteractive\Sentry\Role\RoleNotFoundException;
use Netinteractive\Sentry\Role\ProviderInterface;

class Provider implements ProviderInterface
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
    protected $model = 'Netinteractive\Sentry\Role\Elegant\Record';



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
        return \App::make($this->model);
    }


    /**
     * Find the role by ID.
     *
     * @param  int $id
     * @return \Netinteractive\Sentry\Role\RoleInterface  $role
     * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
     */
    public function findById($id)
    {
        if ( !$role = $this->getMapper()->find($id) ) {
            throw new RoleNotFoundException( sprintf(_("Nie odnaleziono roli o ID [%s]."), $id) );
        }

        return $role;
    }

    /**
     * Find the role by name.
     *
     * @param  string $name
     * @return \Netinteractive\Sentry\Role\RoleInterface  $group
     * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
     */
    public function findByName($name)
    {
        if ( !$role = $this->getMapper()->name($name)->first()) {
            throw new RoleNotFoundException( sprintf(_("Nie odnaleziono roli o nazwie [%s]."), $name));
        }

        return $role;
    }

    /**
     * Find the role by code.
     *
     * @param  string  $code
     * @return \Netinteractive\Sentry\Role\RoleInterface  $group
     * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
     */
    public function findByCode($code)
    {
        if ( !$role = $this->getMapper()->code($code)->first()) {
            throw new RoleNotFoundException( sprintf(_("Nie odnaleziono roli o kodzie [%s]."), $code));
        }

        return $role;
    }

    /**
     * Returns all roles.
     *
     * @return array  $roles
     */
    public function findAll()
    {
        return $this->getMapper()->get();
    }

    /**
     * Creates a group.
     *
     * @param  array $attributes
     * @return \Netinteractive\Sentry\Role\RoleInterface
     */
    public function create(array $attributes)
    {
        $role = $this->createRecord();
        $role->fill($attributes);

        return $role;
    }

}