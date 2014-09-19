<?php namespace Cartalyst\Sentry\Acl;


class RoleFilter {

    /**
     * filter dla query
     * @param QueryBuilder $q
     * @param string $roleName
     * @param string $resourceName
     */
    public function filter($q, $roleName, $resourceName){
        $config = \Config::get('acl');

        if (isSet($config[$roleName][$resourceName])){
            $config[$roleName][$resourceName]($q,\App::make('sentry')->getUser());
        }
    }
}