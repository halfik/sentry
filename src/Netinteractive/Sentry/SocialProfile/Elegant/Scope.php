<?php namespace Netinteractive\Sentry\SocialProfile\Elegant;

use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $type
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeType(DbMapper $mapper, $type)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.type','=',$type);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $profileId
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeProfileId(DbMapper $mapper, $profileId)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.profile_id','=',$profileId);

        return $mapper;
    }
}