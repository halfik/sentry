<?php namespace Cartalyst\Sentry\Auth\Providers;


class FacebookProvider extends SocialProvider{
    public function getType(){
        return 'facebook';
    }
}