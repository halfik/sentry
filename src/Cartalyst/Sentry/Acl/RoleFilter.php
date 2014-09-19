<?php namespace Cartalyst\Sentry\Acl;


class RoleFilter {

    /**
     * filter dla query
     * @param QueryBuilder $q
     * @param User $user
     * @param string $routeName
     */
    public function filter($q, $user, $routeName){
        $mainGroup = $user->getMainGroup();
        $config = \Config::get('acl');

        if (isSet($config[$mainGroup->getCode()][$routeName])){
            $config[$mainGroup->getCode()][$routeName]($q,$user);
        }
    }
}