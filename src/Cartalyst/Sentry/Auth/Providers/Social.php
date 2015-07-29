<?php namespace Cartalyst\Sentry\Auth\Providers;

use Cartalyst\Sentry\SocialProfile\SocialProfileAlreadyExistsException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\SocialProfile\SocialProfileNotFoundException;
use Cartalyst\Sentry\SocialProfile\SocialProfileIdNotSetException;
use Cartalyst\Sentry\Users\LoginRequiredException;




abstract class SocialProvider implements  AuthProviderInferface{



    abstract public function getType();

    public function authenticate(array $credentials, $remember=false){
        /**@var $socialProfileProvider \Cartalyst\Sentry\SocialProfile\ProviderInterface*/
        $socialProfileProvider = \App::make('sentry')->getSocialProfileProvider();

        /**@var $userProvider \Cartalyst\Sentry\Users\ProviderInterface*/
        $userProvider = \App::make('sentry')->getUserProvider();

        $profileId=array_get($credentials, 'profileId');
        if(!$profileId){
            throw new SocialProfileIdRequiredException();
        }
        $profile=$socialProfileProvider->findByProfile($profileId, $this->getType());

        $user=$userProvider->findById($profile->user__id);

        return $user;
    }

    public function register(array $credentials, $activate=true){
        /**@var $socialProfileProvider \Cartalyst\Sentry\SocialProfile\ProviderInterface*/
        $socialProfileProvider = \App::make('sentry')->getSocialProfileProvider();

        /**@var $userProvider \Cartalyst\Sentry\Users\ProviderInterface*/
        $userProvider = \App::make('sentry')->getUserProvider();

        $profileId=array_get($credentials, 'profileId');
        $login=array_get($credentials,'login');

        if(!$login){
            throw new LoginRequiredException();
        }

        if(!$profileId){
            throw new SocialProfileIdRequiredException();
        }

        $profile=$socialProfileProvider->findByProfileId($profileId);

        if($profile){
            throw new SocialProfileAlreadyExistsException();
        }

        //Sprawdzamy czy nie jest juz zarejestrowany uzytkownik o takim loginie
        $user=$userProvider->findByLogin($login);

        if($user){
            throw new UserExistsException();
        }

        unset($credentials['profileId']);
        $user=$userProvider->create($credentials);


        $socialProfile=$socialProfileProvider->create(array(
            'profile_id'=>$profileId,
            'type'=>$this->getType(),
            'user__id'=>$user->id
        ));

        return $user;
    }
}