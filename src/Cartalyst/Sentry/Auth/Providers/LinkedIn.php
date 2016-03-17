<?php namespace Cartalyst\Sentry\Auth\Providers;

class LinkedInProvider extends SocialProvider{
    public function getType(){
        return 'linkedin';
    }
}