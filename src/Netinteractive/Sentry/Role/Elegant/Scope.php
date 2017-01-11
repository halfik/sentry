<?php

namespace Netinteractive\Sentry\Role\Elegant;

use Netinteractive\Elegant\Repository\Repository;
use Netinteractive\Elegant\Repository\RepositoryInterface;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

/**
 * Class Scope
 * @package Netinteractive\Sentry\Role\Elegant
 */
class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface
     * @param string $name
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeName(RepositoryInterface $repository, $name)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.name', '=', $name);

        return $repository;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface
     * @param string $code
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeCode(RepositoryInterface $repository, $code)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.code', '=', $code);

        return $repository;
    }

}