<?php namespace Cartalyst\Sentry\Acl;


class Filter {

    /**
     * filter dla query
     * @param QueryBuilder $q
     * @param string $roleName
     * @param string $resourceName
     */
    public function dataFilter($q, $roleName, $resourceName, $userData){
        $config = \Config::get('acl');

        if (is_string($resourceName) && isSet($config[$roleName]['data'][$resourceName])){
            $config[$roleName]['data'][$resourceName]($q,$userData);
        }
    }

    /**
     * filtr widokow
     * @param stdObject $view
     * @param string $roleName
     * @return string
     */
    public function viewFilter($viewObj, $roleName){
        $config = \Config::get('acl');

        $view = $viewObj->view;
        if ( isSet($config[$roleName]['view'][$view]) ){
            $view = $config[$roleName]['view'][$view];

            if ( isSet($config[$roleName]['skin']) && !empty($config[$roleName]['skin'])){
                $view = $config[$roleName]['skin'].'.'.$view;
            }
        }

        $viewObj->view = $view;
    }
}