<?php

namespace Netinteractive\Sentry\Throttling\Elegant;

use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

/**
 * Class Scope
 * @package Netinteractive\Sentry\Throttling\Elegant
 */
class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param int $id
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeUserId(DbMapper $mapper, $id)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.user__id', '=', $id);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $ip
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeIp(DbMapper $mapper, $ip)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.ip_address', '=', $ip);
        $query->orWhereNull($blueprint->getStorageName().'.ip_address');

        return $mapper;
    }
}