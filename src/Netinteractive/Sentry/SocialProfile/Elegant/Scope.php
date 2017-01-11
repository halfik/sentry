<?php

namespace Netinteractive\Sentry\SocialProfile\Elegant;

use Netinteractive\Elegant\Repository\Repository;
use Netinteractive\Elegant\Repository\RepositoryInterface;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

/**
 * Class Scope
 * @package Netinteractive\Sentry\SocialProfile\Elegant
 */
class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface
     * @param string $type
     * @return \Netinteractive\Elegant\Repository\RepositoryInterface
     */
    public function scopeType(RepositoryInterface $repository, $type)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.type','=',$type);

        return $repository;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface
     * @param string $profileId
     * @return \Netinteractive\Elegant\Repository\RepositoryInterface
     */
    public function scopeProfileId(RepositoryInterface $repository, $profileId)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.profile_id','=',$profileId);

        return $repository;
    }
}