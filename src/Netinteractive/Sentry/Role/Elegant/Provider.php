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
     * Find the group by ID.
     *
     * @param  int $id
     * @return \Netinteractive\Sentry\Role\RoleInterface  $role
     * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
     */
    public function findById($id)
    {
        if (  $role = $this->getMapper()->find($id) ) {
            throw new RoleNotFoundException( sprintf(_("A role could not be found with ID [%s]."), $id) );
        }

        return $role;
    }

    /**
     * Find the group by name.
     *
     * @param  string $name
     * @return \Netinteractive\Sentry\Role\RoleInterface  $group
     * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
     */
    public function findByName($name)
    {
        // TODO: Implement findByName() method.
    }

    /**
     * Returns all groups.
     *
     * @return array  $groups
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * Creates a group.
     *
     * @param  array $attributes
     * @return \Netinteractive\Sentry\Role\RoleInterface
     */
    public function create(array $attributes)
    {
        // TODO: Implement create() method.
    }

}