<?php namespace Netinteractive\Sentry;
/**
 * Part of the Sentry package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0.0
 * @author     Netinteractive LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Netinteractive LLC
 * @link       http://cartalyst.com
 */
use Illuminate\Support\ServiceProvider;

use Netinteractive\Sentry\Auth\AuthManager;
use Netinteractive\Sentry\Cookies\IlluminateCookie;
use Netinteractive\Sentry\Sessions\IlluminateSession;

use Netinteractive\Sentry\Role\Elegant\Provider as RoleProvider;
use Netinteractive\Sentry\Throttling\Elegant\Provider as ThrottleProvider;
use Netinteractive\Sentry\User\Elegant\Provider as UserProvider;
use Netinteractive\Sentry\SocialProfile\Elegant\Provider as SocialProfileProvider;

/**
 * Class SentryServiceProvider
 * @package Netinteractive\Sentry
 */
class SentryServiceProvider extends ServiceProvider
{

    protected $commands = [
        'Netinteractive\Sentry\Commands\MakeAdmin',
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['sentry.auth.manager']->set('elegant', $this->app['sentry.auth.providers.elegant']);
        $this->app['sentry.auth.manager']->set('facebook', $this->app['sentry.auth.providers.facebook']);
        $this->app['sentry.auth.manager']->set('linkedin', $this->app['sentry.auth.providers.linkedin']);

        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('/packages/netinteractive/sentry/config.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../../migrations/' => $this->app->databasePath().'/migrations',
        ], 'migrations');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->prepareResources();
        $this->registerAuth();
		$this->registerUserProvider();
		$this->registerRoleProvider();
		$this->registerThrottleProvider();
        $this->registerSocialProvider();
		$this->registerSession();
		$this->registerCookie();
		$this->registerSentry();

        $this->commands($this->commands);
	}

	/**
	 * Prepare the package resources.
	 *
	 * @return void
	 */
	protected function prepareResources()
	{
        $config = realpath(__DIR__.'/../../config/config.php');;

		$this->mergeConfigFrom($config, 'packages.netinteractive.sentry.config');
	}


    /**
     * Register Auth Manager
     */
    protected function registerAuth()
    {

        $this->app['sentry.auth.manager'] = $this->app->share(function($app)
        {
            return new AuthManager();
        });

        $this->app->singleton('AuthManager', function($app)
        {
            return $app['sentry.auth.manager'];
        });


        /**
         * Dodajemy domyslnegogo auth providera do managera
         *
         * @return void
         */
        $this->app['sentry.auth.providers.elegant'] = $this->app->share(function($app)
        {
            return 'ElegantProvider';
        });

        $this->app['sentry.auth.providers.facebook'] = $this->app->share(function($app)
        {
            return 'FacebookProvider';
        });

        $this->app['sentry.auth.providers.linkedin'] = $this->app->share(function($app)
        {
            return 'LinkedInProvider';
        });
    }

	/**
	 * Register the user provider used by Sentry.
	 *
	 * @return void
	 */
	protected function registerUserProvider()
	{
		$this->app['sentry.user'] = $this->app->share(function($app)
		{
			$config = $app['config']->get('packages.netinteractive.sentry.config');

			$model = array_get($config, 'users.model');

			return new UserProvider($model);
		});
	}

	/**
	 * Register the group provider used by Sentry.
	 *
	 * @return void
	 */
	protected function registerRoleProvider()
	{
		$this->app['sentry.role'] = $this->app->share(function($app)
		{
            $config = $app['config']->get('packages.netinteractive.sentry.config');
			$model = array_get($config, 'role.model');

			return new RoleProvider($model);
		});
	}

	/**
	 * Register the throttle provider used by Sentry.
	 *
	 * @return void
	 */
	protected function registerThrottleProvider()
	{
		$this->app['sentry.throttle'] = $this->app->share(function($app)
		{
            $config = $app['config']->get('packages.netinteractive.sentry.config');

			$model = array_get($config, 'throttling.model');

			$throttleProvider = new ThrottleProvider($app['sentry.user'], $model);

			if (array_get($config, 'throttling.enabled') === false){
				$throttleProvider->disable();
			}

			return $throttleProvider;
		});
	}

    /**
     * Register social profile provider
     *
     * @return void
     */
    protected function registerSocialProvider()
    {
        $this->app['sentry.social'] = $this->app->share(function($app) {
            $config = $app['config']->get('packages.netinteractive.sentry.config');

            $model = array_get($config, 'social_profile.model');

            return new SocialProfileProvider($model);
        });
    }

	/**
	 * Register the session driver used by Sentry.
	 *
	 * @return void
	 */
	protected function registerSession()
	{
		$this->app['sentry.session'] = $this->app->share(function($app)
		{
            $config = $app['config']->get('packages.netinteractive.sentry.config');
			$key = $config['cookie']['key'];

			return new IlluminateSession($app['session.store'], $key);
		});
	}

	/**
	 * Register the cookie driver used by Sentry.
	 *
	 * @return void
	 */
	protected function registerCookie()
	{
		$this->app['sentry.cookie'] = $this->app->share(function($app)
		{
            $config = $app['config']->get('packages.netinteractive.sentry.config');
            $key = $config['cookie']['key'];

			/**
			 * We'll default to using the 'request' strategy, but switch to
			 * 'jar' if the Laravel version in use is 4.0.*
			 */

			$strategy = 'request';

			if (preg_match('/^4\.0\.\d*$/D', $app::VERSION)) {
				$strategy = 'jar';
			}

			return new IlluminateCookie($app['request'], $app['cookie'], $key, $strategy);
		});
	}

	/**
	 * Takes all the components of Sentry and glues them
	 * together to create Sentry.
	 *
	 * @return void
	 */
	protected function registerSentry()
	{
		$this->app['sentry'] = $this->app->share(function($app)
		{
			return new Sentry(
				$app['sentry.user'],
				$app['sentry.role'],
				$app['sentry.throttle'],
                $app['sentry.social'],
				$app['sentry.session'],
				$app['sentry.cookie'],
				$app['request']->getClientIp()
			);
		});

		$this->app->alias('sentry', 'Netinteractive\Sentry\Sentry');
	}
}
