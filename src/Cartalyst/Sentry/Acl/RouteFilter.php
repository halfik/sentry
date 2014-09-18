<?php namespace Cartalyst\Sentry\Acl;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class RouteFilter {

    /**
     * filtruje prawa dostepu do zasobu
     * @param $route
     * @param $request
     * @return bool
     */
    public function filter($route)
    {
        $roles = $this->getRoles();

        /**
         * sprawdzamy czy mamy uprawnienia (po nazwie akcji lub aliasie)
         */
        foreach ($roles AS $role){
            if ( $role->hasAccess($route->getActionName(), false) ){
                return true;
            }
            elseif ($role->hasAccess($route->getName(), false)){
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRoles(){
        $user = \App::make('sentry')->getUser();

        if ($user){
            $userRoles = $user->getGroups();
            if ($userRoles){
                return $userRoles;
            }
        }

        $groupProvider = \App::make('sentry')->getGroupProvider();
        $defaultRole= $groupProvider->findByCode('guest');

        return array($defaultRole);
    }

}