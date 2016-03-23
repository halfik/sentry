<?php namespace Netinteractive\Sentry\Auth\Providers;


Interface AuthProviderInferface
{

    /**
     * metoda autoryzacyjna
     * @param array $credentianls
     * @param boolean $remember
     * @return mixed
     */
    public function authenticate(array $credentials, $remember=false);

    /**
     * zarajestrowac nowego uzytkownika
     * @param array $credentials
     * @param bool $activate
     * @return mixed
     */
    public function register(array $credentials, $activate=false);
}