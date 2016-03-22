<?php
namespace Netinteractive\Sentry\Role\Elegant;


class Record extends \Netinteractive\Elegant\Model\Record implements \Netinteractive\Sentry\Role\RoleInterface
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }

    /**
     * Returns the group's ID.
     *
     * @return mixed
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

    /**
     * Returns the group's name.
     *
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
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

    /**
     * Saves the group.
     *
     * @return bool
     */
    public function save()
    {
        // TODO: Implement save() method.
    }

    /**
     * Delete the group.
     *
     * @return bool
     */
    public function delete()
    {
        // TODO: Implement delete() method.
    }


}