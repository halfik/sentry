<?php

namespace Netinteractive\Sentry\Throttling\Elegant;

use Netinteractive\Elegant\Repository\Repository;
use Netinteractive\Elegant\Repository\RepositoryInterface;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

/**
 * Class Scope
 * @package Netinteractive\Sentry\Throttling\Elegant
 */
class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface
     * @param int $id
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeUserId(RepositoryInterface $repository, $id)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.user__id', '=', $id);

        return $repository;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface
     * @param string $ip
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeIp(RepositoryInterface $repository, $ip)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.ip_address', '=', $ip);
        $query->orWhereNull($blueprint->getStorageName().'.ip_address');

        return $repository;
    }
}