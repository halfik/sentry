<?php

namespace Netinteractive\Sentry\SocialProfile\Elegant;

use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

/**
 * Class Scope
 * @package Netinteractive\Sentry\SocialProfile\Elegant
 */
class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $type
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeType(DbMapper $mapper, $type)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.type','=',$type);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $profileId
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeProfileId(DbMapper $mapper, $profileId)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.profile_id','=',$profileId);

        return $mapper;
    }
}