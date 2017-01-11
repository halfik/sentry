<?php

namespace Netinteractive\Sentry\User\Elegant;

use Netinteractive\Elegant\Repository\Repository;
use Netinteractive\Elegant\Repository\RepositoryInterface;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

/**
 * Class Scope
 * @package Netinteractive\Sentry\User\Elegant
 */
class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface
     * @param string $login
     * @return \Netinteractive\Elegant\Repository\RepositoryInterface
     */
    public function scopeLogin(RepositoryInterface $repository, $login)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.'.$blueprint->getLoginName(), '=', $login);

        return $repository;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface
     * @param string $email
     * @return \Netinteractive\Elegant\Repository\RepositoryInterface
     */
    public function scopeEmail(RepositoryInterface $repository, $email)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.email', '=', $email);

        return $repository;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $code
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeActivationCode(DbMapper $repository, $code)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.activation_code', '=', $code);

        return $repository;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $code
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeResetPasswordCode(DbMapper $repository, $code)
    {
        $query = $repository->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.reset_password_code', '=', $code);

        return $repository;
    }


}