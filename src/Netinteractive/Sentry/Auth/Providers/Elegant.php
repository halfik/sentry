<?php namespace Netinteractive\Sentry\Auth\Providers;


use Netinteractive\Sentry\User\LoginRequiredException;
use Netinteractive\Sentry\User\PasswordRequiredException;
use Netinteractive\Sentry\User\UserNotFoundException;

class ElegantProvider implements AuthProviderInferface
{
    /**
     * metoda autoryzacyjna
     * @param array $credentianls
     * @return User
     */
    public function authenticate(array $credentials, $remember=false)
    {
        $userProvider = \App::make('sentry')->getUserProvider();
        $throttleProvider = \App::make('sentry')->getThrottleProvider();

        // We'll default to the login name field, but fallback to a hard-coded
        // 'login' key in the array that was passed.
        $loginName = $userProvider->getEmptyUser()->getBlueprint()->getLoginName();
        $loginCredentialKey = (isset($credentials[$loginName])) ? $loginName : 'login';

        if (empty($credentials[$loginCredentialKey])) {
            throw new LoginRequiredException(sprintf( _('Pole [%s] jest wymagane.'), $loginCredentialKey ));
        }

        if (empty($credentials['password'])) {
            throw new PasswordRequiredException( _('Hasło jest wymagane.'));
        }

        // If the user did the fallback 'login' key for the login code which
        // did not match the actual login name, we'll adjust the array so the
        // actual login name is provided.
        if ($loginCredentialKey !== $loginName) {
            $credentials[$loginName] = $credentials[$loginCredentialKey];
            unset($credentials[$loginCredentialKey]);
        }

        // If throttling is enabled, we'll firstly check the throttle.
        // This will tell us if the user is banned before we even attempt
        // to authenticate them
        if ($throttlingEnabled = $throttleProvider->isEnabled()) {
            if ( $throttle = $throttleProvider->findByUserLogin($credentials[$loginName], \App::make('sentry')->getIpAddress()) ) {
                $throttle->check();
            }
        }

        try {
            $user = $userProvider->findByCredentials($credentials);
        }
        catch (UserNotFoundException $e) {
            if ($throttlingEnabled and isset($throttle)) {
                $throttleProvider->addLoginAttempt($throttle);
            }

            throw $e;
        }

        if ($throttlingEnabled and isset($throttle)) {
            $throttleProvider->clearLoginAttempts($throttle);
        }

        $user->clearResetPassword();
        $userProvider->getMapper()->save($user);

        return $user;
    }


    public function register(array $credentials, $activate=false)
    {

        $userProvider = \App::make('sentry')->getUserProvider();
        $user = $userProvider->create($credentials);

        if ($activate) {
            $user->attemptActivation($user->getActivationCode());
        }

        return $user;
    }
}