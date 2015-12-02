<?php namespace Cartalyst\Sentry\Auth\Providers;

use Cartalyst\Sentry\SocialProfile\SocialProfileAlreadyExistsException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\SocialProfile\SocialProfileNotFoundException;
use Cartalyst\Sentry\SocialProfile\SocialProfileIdNotSetException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\UserNotFoundException;


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

        try{
            $profile=$socialProfileProvider->findByProfile($profileId,$this->getType());
        }
        catch(SocialProfileNotFoundException $e){
            $profile=null;
        }

        if($profile){
            throw new SocialProfileAlreadyExistsException();
        }

        //Sprawdzamy czy nie jest juz zarejestrowany uzytkownik o takim loginie
        try{
            $user=$userProvider->findByLogin($login);
        }
        catch(UserNotFoundException $e){
            $user=null;
        }

        unset($credentials['profileId']);

        \DB::beginTransaction();
        if($user){
            $user=$userProvider->findByEmail(array_get($credentials, 'email', null));
        } else {
            $user=$userProvider->create($credentials);
        }

        $socialProfile=$socialProfileProvider->create(array(
            'profile_id'=>$profileId,
            'type'=>$this->getType(),
            'user__id'=>$user->id
        ));

        \DB::commit();

        if ($activate)
        {
            $user->attemptActivation($user->getActivationCode());
        }


        return $user;
    }
}