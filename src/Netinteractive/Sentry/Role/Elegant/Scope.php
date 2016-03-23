<?php namespace Netinteractive\Sentry\Role\Elegant;

use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;


class Scope extends  BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $name
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeName(DbMapper $mapper, $name)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.name', '=', $name);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $code
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeCode(DbMapper $mapper, $code)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.code', '=', $code);

        return $mapper;
    }

}