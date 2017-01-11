<?php

namespace Netinteractive\Sentry\Role\Elegant;

use Netinteractive\Elegant\Domain\ServiceProvider AS BusinessProvider;
use Netinteractive\Sentry\Role\RoleNotFoundException;
use Netinteractive\Sentry\Role\ProviderInterface;

/**
 * Class Provider
 * @package Netinteractive\Sentry\Role\Elegant
 */
class Provider extends  BusinessProvider implements ProviderInterface
{
    private $table;
    /**
     * @param null|string $record
     */
    public function __construct($record=null)
    {
        if (!$record){
            $record = 'Netinteractive\Sentry\Role\Elegant\Record';
        }
        parent::__construct($record);
        $this->table = $this->getRepository()->getRecord()->getBlueprint()->getStorageName();
    }


    /**
     * Creates a role.
     *
     * @param  array $attributes
     * @return \Netinteractive\Sentry\Role\RoleInterface
     */
    public function create(array $attributes)
    {
        return parent::create($attributes);
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
        if ( !$role = $this->getRepository()->find($id) ) {
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
        if ( !$role = $this->getRepository()->name($name)->first()) {
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
        if ( !$role = $this->getRepository()->code($code)->first()) {
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
        return $this->getRepository()
            ->getQuery()
            ->orderBy($this->table.'.name')
            ->get()
            ;
    }

}