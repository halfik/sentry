<?php namespace Cartalyst\Sentry\Auth\Providers;

use Cartalyst\Sentry\SocialProfile\SocialProfileAlreadyExistsException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\SocialProfile\SocialProfileNotFoundException;




class FacebookProvider extends SocialAbstract{


    public function authorize(array $credentials, $remember=false){
        /**@var $socialProfileProvider \Cartalyst\Sentry\SocialProfile\ProviderInterface*/
        $socialProfileProvider = \App::make('sentry')->getSocialProfileProvider();

        /**@var $userProvider \Cartalyst\Sentry\Users\ProviderInterface*/
        $userProvider = \App::make('sentry')->getUserProvider();

        $profileId=array_get($credentials, 'id');
        $profile=$socialProfileProvider->findByProfileId($profileId);

        $user=$userProvider->findById($profile->user__id);

        return $user;
    }

    public function register(array $credentials){
        /**@var $socialProfileProvider \Cartalyst\Sentry\SocialProfile\ProviderInterface*/
        $socialProfileProvider = \App::make('sentry')->getSocialProfileProvider();

        /**@var $userProvider \Cartalyst\Sentry\Users\ProviderInterface*/
        $userProvider = \App::make('sentry')->getUserProvider();



        $profileId=array_get($credentials, 'id');
        $login=array_get($credentials, 'email');
        $name=array_get($credentials, 'name');

        $profile=$socialProfileProvider->findByProfileId($profileId);

        if($profile){
            throw new SocialProfileAlreadyExistsException();
        }

        $user=$userProvider->findByLogin($login);

        if($user){
            throw new UserExistsException();
        }

        $user=$userProvider->create(array(
            'login'=>$login,
            'email'=>$login,
        ));

        $data=array(
            'profile_id'=>$profileId,
            'type'=>'facebook',
            'user__id'=>$user->id
        );

        $socialProfile=$socialProfileProvider->create($data);

        return $user;

    }
}