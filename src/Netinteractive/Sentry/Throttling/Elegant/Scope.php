<?php namespace Netinteractive\Sentry\Throttling\Elegant;

use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param int $id
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeUserId(DbMapper $mapper, $id)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.user__id', '=', $id);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $ip
     * @return \Netinteractive\Elegant\Mapper\DbMapper
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