<?php namespace Netinteractive\Sentry\Auth\Providers;

use Netinteractive\Sentry\SocialProfile\SocialProfileAlreadyExistsException;
use Netinteractive\Sentry\SocialProfile\SocialProfileNotFoundException;
use Netinteractive\Sentry\SocialProfile\SocialProfileIdRequiredException;
use Netinteractive\Sentry\User\LoginRequiredException;
use Netinteractive\Sentry\User\UserNotFoundException;

abstract class SocialProvider implements AuthProviderInferface
{
    /**
     * @param array $credentials
     * @param bool $remember
     * @return mixed
     * @throws SocialProfileIdRequiredException
     */
    public function authenticate(array $credentials, $remember=false)
    {
        /**
         * @var $socialProfileProvider \Netinteractive\Sentry\SocialProfile\ProviderInterface
         */
        $socialProfileProvider = \App::make('sentry')->getSocialProfileProvider();

        /**
         * @var $userProvider \Netinteractive\Sentry\User\ProviderInterface
         */
        $userProvider = \App::make('sentry')->getUserProvider();

        $profileId = array_get($credentials, 'profileId');
        if(!$profileId){
            throw new SocialProfileIdRequiredException();
        }

        $profile = $socialProfileProvider->findByProfile($profileId, $this->getType());

        $user = $userProvider->findById($profile->user__id);

        return $user;
    }

    /**
     * @param array $credentials
     * @param bool $activate
     * @return \Netinteractive\Sentry\User\UserInterface|null
     */
    public function register(array $credentials, $activate=true)
    {
        /**
         * @var $socialProfileProvider \Netinteractive\Sentry\SocialProfile\ProviderInterface
         */
        $socialProfileProvider = \App::make('sentry')->getSocialProfileProvider();

        /**
         * @var $userProvider \Netinteractive\Sentry\User\ProviderInterface
         */
        $userProvider = \App::make('sentry')->getUserProvider();

        $profileId = array_get($credentials, 'profileId');
        $login = array_get($credentials,'login');

        if(!$login){
            throw new LoginRequiredException();
        }

        if(!$profileId){
            throw new SocialProfileIdRequiredException();
        }

        try{
            $profile = $socialProfileProvider->findByProfile($profileId,$this->getType());
        }
        catch(SocialProfileNotFoundException $e){
            $profile = null;
        }

        if($profile){
            throw new SocialProfileAlreadyExistsException();
        }

        //Sprawdzamy czy nie jest juz zarejestrowany uzytkownik o takim loginie
        try{
            $user = $userProvider->findByLogin($login);
        }
        catch(UserNotFoundException $e){
            $user = null;
        }

        unset($credentials['profileId']);

        \DB::beginTransaction();
        if($user){
            $user = $userProvider->findByEmail(array_get($credentials, 'email', null));
        }
        else {
            $user = $userProvider->create($credentials);
        }

       $socialProfileProvider->create(array(
            'profile_id' => $profileId,
            'type' => $this->getType(),
            'user__id' => $user->id
        ));

        \DB::commit();

        if ($activate && $user->isActivated() == false) {
            $user->attemptActivation($user->getActivationCode());
            $userProvider->getMapper()->save($user);
        }


        return $user;
    }
}