<?php

namespace Netinteractive\Sentry\Auth\Providers;


/**
 * Class FacebookProvider
 * @package Netinteractive\Sentry\Auth\Providers
 */
class FacebookProvider extends SocialProvider
{
    public function getType(){
        return 'facebook';
    }
}
