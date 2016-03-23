<?php namespace Netinteractive\Sentry\User\Elegant;

use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

class Scope extends  BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $login
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeLogin(DbMapper $mapper, $login)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.'.$blueprint->getLoginName(), '=', $login);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $email
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeEmail(DbMapper $mapper, $email)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.email', '=', $email);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $code
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeActivationCode(DbMapper $mapper, $code)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.activation_code', '=', $code);

        return $mapper;
    }

    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param string $code
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeResetPasswordCode(DbMapper $mapper, $code)
    {
        $query = $mapper->getQuery();
        $blueprint = $query->getRecord()->getBlueprint();

        $query->where($blueprint->getStorageName().'.reset_password_code', '=', $code);

        return $mapper;
    }


}