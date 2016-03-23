<?php namespace Netinteractive\Sentry\Auth\Providers;


class LinkedInProvider extends SocialProvider
{
    public function getType(){
        return 'linkedin';
    }
}