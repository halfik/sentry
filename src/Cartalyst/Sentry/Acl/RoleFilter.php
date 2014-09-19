<?php namespace Cartalyst\Sentry\Acl;


class RoleFilter {

    /**
     * filter dla query
     * @param QueryBuilder $q
     * @param User $user
     * @param string $resourceName
     */
    public function filter($q, $user, $resourceName){
        $mainGroup = $user->getMainGroup();
        $config = \Config::get('acl');

        if (isSet($config[$mainGroup->getCode()][$resourceName])){
            $config[$mainGroup->getCode()][$resourceName]($q,$user);
        }
    }
}