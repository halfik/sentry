<?php

namespace Netinteractive\Sentry\User\Elegant;

use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

/**
 * Class Scope
 * @package Netinteractive\Sentry\User\Elegant
 */
class Scope extends  BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $login
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeLogin(DbMapper $mapper, $login)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.'.$blueprint->getLoginName(), '=', $login);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $email
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeEmail(DbMapper $mapper, $email)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.email', '=', $email);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $code
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeActivationCode(DbMapper $mapper, $code)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.activation_code', '=', $code);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param string $code
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeResetPasswordCode(DbMapper $mapper, $code)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.reset_password_code', '=', $code);

        return $mapper;
    }


}