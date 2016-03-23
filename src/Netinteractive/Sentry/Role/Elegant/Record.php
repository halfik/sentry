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
     * Returns role code
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }


}