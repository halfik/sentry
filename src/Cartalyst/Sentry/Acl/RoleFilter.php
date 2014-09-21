<?php namespace Cartalyst\Sentry\Acl;


class RoleFilter {

    /**
     * filter dla query
     * @param QueryBuilder $q
     * @param string $roleName
     * @param string $resourceName
     */
    public function filter($q, $roleName, $resourceName, $userData){
        $config = \Config::get('acl');

        if (is_string($resourceName) && isSet($config[$roleName]['data'][$resourceName])){
            $config[$roleName]['data'][$resourceName]($q,$userData);
        }
    }
}