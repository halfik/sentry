<?php

namespace Netinteractive\Sentry\Role\Elegant;

use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

/**
 * Class Scope
 * @package Netinteractive\Sentry\Role\Elegant
 */
class Scope extends  BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $name
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeName(DbMapper $mapper, $name)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.name', '=', $name);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $code
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeCode(DbMapper $mapper, $code)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.code', '=', $code);

        return $mapper;
    }

}