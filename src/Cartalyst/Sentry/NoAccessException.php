<?php
namespace Cartalyst\Sentry;

/**
 * Class NoAccessException
 * ACL no access Exception
 */

use \Illuminate\Support\Facades\Route as Route;

class NoAccessException extends \Exception {
    public function __construct($message="", $code=E_USER_ERROR, $previous=null) {
        $route= Route::getCurrentRoute();
        $message.="\ndenied with route ".$route->getUri();
        $message.="\n".$route->getActionName()."[".$route->getName()."]";
        $user = \App::make('sentry')->getUser();
        //var_dump($user);
        //\Log::info($user);
        if ( !empty($user) && $user->isActivated()){
            $message.="\nwith rights: ".print_r($user->getMergedPermissions(),1);
	}else{
            $message.=' without active user';
        }
        parent::__construct($message, $code, $previous);
    }
}