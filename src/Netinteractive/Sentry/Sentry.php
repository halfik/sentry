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
 * @author     Netinteractive LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Netinteractive LLC
 * @link       http://cartalyst.com
 */

use Netinteractive\Sentry\Cookies\CookieInterface;
use Netinteractive\Sentry\Cookies\NativeCookie;
use Netinteractive\Sentry\Role\Elegant\Provider as RoleProvider;
use Netinteractive\Sentry\Role\ProviderInterface as RoleProviderInterface;
use Netinteractive\Sentry\Sessions\NativeSession;
use Netinteractive\Sentry\Sessions\SessionInterface;
use Netinteractive\Sentry\Throttling\Elegant\Provider as ThrottleProvider;
use Netinteractive\Sentry\Throttling\ProviderInterface as ThrottleProviderInterface;
use Netinteractive\Sentry\User\Elegant\Provider as UserProvider;
use Netinteractive\Sentry\User\ProviderInterface as UserProviderInterface;
use Netinteractive\Sentry\SocialProfile\Elegant\Provider as SocialProfileProvider;
use Netinteractive\Sentry\SocialProfile\ProviderInterface as SocialProviderInterface;
use Netinteractive\Sentry\User\UserInterface;
use Netinteractive\Sentry\User\UserNotFoundException;
use Netinteractive\Sentry\User\UserNotActivatedException;

class Sentry
{

	/**
	 * The user that's been retrieved and is used
	 * for authentication. Authentication methods
	 * are available for finding the user to set
	 * here.
	 *
	 * @var \Netinteractive\Sentry\User\UserInterface
	 */
	protected $user;

	/**
	 * The session driver used by Sentry.
	 *
	 * @var \Netinteractive\Sentry\Sessions\SessionInterface
	 */
	protected $session;

	/**
	 * The cookie driver used by Sentry.
	 *
	 * @var \Netinteractive\Sentry\Cookies\CookieInterface
	 */
	protected $cookie;

	/**
	 * The user provider, used for retrieving
	 * objects which implement the Sentry user
	 * interface.
	 *
	 * @var \Netinteractive\Sentry\User\ProviderInterface
	 */
	protected $userProvider;

	/**
	 * The group provider, used for retrieving
	 * objects which implement the Sentry group
	 * interface.
	 *
	 * @var \Netinteractive\Sentry\Role\ProviderInterface
	 */
	protected $roleProvider;

	/**
	 * The throttle provider, used for retrieving
	 * objects which implement the Sentry throttling
	 * interface.
	 *
	 * @var \Netinteractive\Sentry\Throttling\ProviderInterface
	 */
	protected $throttleProvider;

    /**
     * @var \Netinteractive\Sentry\SocialProfile\ProviderInterface
     */
    protected $socialProfileProvider;

	/**
	 * The client's IP address associated with Sentry.
	 *
	 * @var string
	 */
	protected $ipAddress = '0.0.0.0';

	/**
	 * Create a new Sentry object.
	 *
	 * @param  \Netinteractive\Sentry\User\ProviderInterface $userProvider
	 * @param  \Netinteractive\Sentry\Role\ProviderInterface $roleProvider
	 * @param  \Netinteractive\Sentry\Throttling\ProviderInterface $throttleProvider
	 * @param  \Netinteractive\Sentry\Sessions\SessionInterface $session
	 * @param  \Netinteractive\Sentry\Cookies\CookieInterface $cookie
	 * @param  string $ipAddress
	 * @return void
	 */
	public function __construct(
		UserProviderInterface $userProvider = null,
		RoleProviderInterface $roleProvider = null,
		ThrottleProviderInterface $throttleProvider = null,
        SocialProviderInterface $socialProfileProvider = null,
		SessionInterface $session = null,
		CookieInterface $cookie = null,
		$ipAddress = null
	)
	{
		$this->userProvider     = $userProvider ?: new UserProvider();
		$this->roleProvider    = $roleProvider ?: new RoleProvider();
		$this->throttleProvider = $throttleProvider ?: new ThrottleProvider($this->userProvider);
        $this->socialProfileProvider   = $socialProfileProvider ?: new SocialProfileProvider();

		$this->session          = $session ?: new NativeSession;
		$this->cookie           = $cookie ?: new NativeCookie;

		if (isset($ipAddress)){
			$this->ipAddress = $ipAddress;
		}
	}

	/**
	 * Registers a user by giving the required credentials
	 * and an optional flag for whether to activate the user.
	 *
	 * @param  array  $credentials
	 * @param  bool   $activate
	 * @return \Netinteractive\Sentry\User\UserInterface
	 */
	public function register(array $credentials, $activate = false)
	{
        $authManager =  \App::make('sentry.auth.manager');
        $authProvider = $authManager->getCurrent();
        $user = $authProvider->register($credentials, $activate);

        \Event::fire('sentry.register', array($user, $credentials));

        return $user;
	}


	/**
	 * Attempts to authenticate the given user
	 * according to the passed credentials.
	 *
	 * @param  array  $credentials
	 * @param  bool   $remember
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \Netinteractive\Sentry\Throttling\UserBannedException
	 * @throws \Netinteractive\Sentry\Throttling\UserSuspendedException
	 * @throws \Netinteractive\Sentry\User\LoginRequiredException
	 * @throws \Netinteractive\Sentry\User\PasswordRequiredException
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function authenticate(array $credentials, $remember = false)
	{
        $authManager =  \App::make('sentry.auth.manager');
        $authProvider = $authManager->getCurrent();

        return $authProvider->authenticate($credentials);
	}

	/**
	 * Alias for authenticating with the remember flag checked.
	 *
	 * @param  array  $credentials
	 * @return \Netinteractive\Sentry\User\UserInterface
	 */
	public function authenticateAndRemember(array $credentials)
	{
		return $this->authenticate($credentials, true);
	}

	/**
	 * Check to see if the user is logged in and activated, and hasn't been banned or suspended.
	 *
	 * @return bool
	 */
	public function check()
	{
		if (is_null($this->user))
		{
			// Check session first, follow by cookie
			if ( ! $userArray = $this->session->get() and ! $userArray = $this->cookie->get())
			{
				return false;
			}

			// Now check our user is an array with two elements,
			// the username followed by the persist code
			if ( ! is_array($userArray) or count($userArray) !== 2)
			{
				return false;
			}

			list($id, $persistCode) = $userArray;

			// Let's find our user
			try
			{
				$user = $this->getUserProvider()->findById($id);
			}
			catch (UserNotFoundException $e)
			{
				return false;
			}

			// Great! Let's check the session's persist code
			// against the user. If it fails, somebody has tampered
			// with the cookie / session data and we're not allowing
			// a login
			if ( ! $user->checkPersistCode($persistCode))
			{
				return false;
			}

			// Now we'll set the user property on Sentry
			$this->user = $user;
		}

		// Let's check our cached user is indeed activated
		if ( ! $user = $this->getUser() or ! $user->isActivated())
		{
			return false;
		}
		// If throttling is enabled we check it's status
		if( $this->getThrottleProvider()->isEnabled())
		{
			// Check the throttle status
			$throttle = $this->getThrottleProvider()->findByUser( $user );

			if( $throttle->isBanned() or $throttle->isSuspended())
			{
				$this->logout();
				return false;
			}
		}

		return true;
	}

	/**
	 * Logs in the given user and sets properties
	 * in the session.
	 *
	 * @param  \Netinteractive\Sentry\User\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 * @throws \Netinteractive\Sentry\User\UserNotActivatedException
	 */
	public function login(UserInterface $user, $remember = false)
	{
		if ( ! $user->isActivated())
		{
			$login = $user->getLogin();
			throw new UserNotActivatedException("Cannot login user [$login] as they are not activated.");
		}

		$this->user = $user;

		// Create an array of data to persist to the session and / or cookie
		$toPersist = array($user->getId(), $user->getPersistCode());

		// Set sessions
		$this->session->put($toPersist);

		if ($remember)
		{
			$this->cookie->forever($toPersist);
		}

		// The user model can attach any handlers
		// to the "recordLogin" event.
		$user->recordLogin();
        $this->getUserProvider()->getMapper()->save($user);
	}

	/**
	 * Alias for logging in and remembering.
	 *
	 * @param  \Netinteractive\Sentry\User\UserInterface  $user
	 */
	public function loginAndRemember(UserInterface $user)
	{
		$this->login($user, true);
	}

	/**
	 * Logs the current user out.
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->user = null;

		$this->session->forget();
		$this->cookie->forget();
	}

	/**
	 * Sets the user to be used by Sentry.
	 *
	 * @param  \Netinteractive\Sentry\User\UserInterface
	 * @return void
	 */
	public function setUser(UserInterface $user)
	{
		$this->user = $user;
	}

	/**
	 * Returns the current user being used by Sentry, if any.
	 *
	 * @return \Netinteractive\Sentry\User\UserInterface
	 */
	public function getUser()
	{
		// We will lazily attempt to load our user
		if (is_null($this->user))
		{
			$this->check();
		}

		return $this->user;
	}

	/**
	 * Sets the session driver for Sentry.
	 *
	 * @param  \Netinteractive\Sentry\Sessions\SessionInterface  $session
	 * @return void
	 */
	public function setSession(SessionInterface $session)
	{
		$this->session = $session;
	}

	/**
	 * Gets the session driver for Sentry.
	 *
	 * @return \Netinteractive\Sentry\Sessions\SessionInterface
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Sets the cookie driver for Sentry.
	 *
	 * @param  \Netinteractive\Sentry\Cookies\CookieInterface  $cookie
	 * @return void
	 */
	public function setCookie(CookieInterface $cookie)
	{
		$this->cookie = $cookie;
	}

	/**
	 * Gets the cookie driver for Sentry.
	 *
	 * @return \Netinteractive\Sentry\Cookies\CookieInterface
	 */
	public function getCookie()
	{
		return $this->cookie;
	}

	/**
	 * Sets the role provider for Sentry.
	 *
	 * @param  \Netinteractive\Sentry\Role\ProviderInterface
	 * @return void
	 */
	public function setRoleProvider(RoleProviderInterface $roleProvider)
	{
		$this->roleProvider = $roleProvider;
	}

	/**
	 * Gets the role provider for Sentry.
	 *
	 * @return \Netinteractive\Sentry\Role\ProviderInterface
	 */
	public function getRoleProvider()
	{
		return $this->roleProvider;
	}

	/**
	 * Sets the user provider for Sentry.
	 *
	 * @param  \Netinteractive\Sentry\User\ProviderInterface
	 * @return void
	 */
	public function setUserProvider(UserProviderInterface $userProvider)
	{
		$this->userProvider = $userProvider;
	}

	/**
	 * Gets the user provider for Sentry.
	 *
	 * @return \Netinteractive\Sentry\User\ProviderInterface
	 */
	public function getUserProvider()
	{
		return $this->userProvider;
	}

	/**
	 * Sets the throttle provider for Sentry.
	 *
	 * @param  \Netinteractive\Sentry\Throttling\ProviderInterface
	 * @return void
	 */
	public function setThrottleProvider(ThrottleProviderInterface $throttleProvider)
	{
		$this->throttleProvider = $throttleProvider;
	}

	/**
	 * Gets the throttle provider for Sentry.
	 *
	 * @return \Netinteractive\Sentry\Throttling\ProviderInterface
	 */
	public function getThrottleProvider()
	{
		return $this->throttleProvider;
	}

    /**
     * Sets the social profile provider
     *
     * @param SocialProviderInterface $socialProfileProvider
     */
    public function setSocialProfileProvider(SocialProviderInterface $socialProfileProvider)
    {
        $this->socialProfileProvider = $socialProfileProvider;
    }

    /**
     * Returns social profile provider
     * @return SocialProfileProvider|SocialProviderInterface
     */
    public function getSocialProfileProvider()
    {
        return $this->socialProfileProvider;
    }

	/**
	 * Sets the IP address Sentry is bound to.
	 *
	 * @param  string  $ipAddress
	 * @return void
	 */
	public function setIpAddress($ipAddress)
	{
		$this->ipAddress = $ipAddress;
	}

	/**
	 * Gets the IP address Sentry is bound to.
	 *
	 * @return string
	 */
	public function getIpAddress()
	{
		return $this->ipAddress;
	}

	/**
	 * Find the role by ID.
	 *
	 * @param  int  $id
	 * @return \Netinteractive\Sentry\Role\RoleInterface  $role
	 * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
	 */
	public function findRoleById($id)
	{
		return $this->roleProvider->findById($id);
	}

	/**
	 * Find the role by name.
	 *
	 * @param  string  $name
	 * @return \Netinteractive\Sentry\Role\RoleInterface  $group
	 * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
	 */
	public function findRoleByName($name)
	{
		return $this->roleProvider->findByName($name);
	}

    /**
     * Find the role by code.
     *
     * @param  string  $code
     * @return \Netinteractive\Sentry\Role\RoleInterface  $group
     * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
     */
    public function findRoleByCode($code)
    {
        return $this->roleProvider->findByCode($code);
    }

	/**
	 * Returns all groups.
	 *
	 * @return array  $groups
	 */
	public function findAllRoles()
	{
		return $this->roleProvider->findAll();
	}

	/**
	 * Creates a role.
	 *
	 * @param  array  $attributes
	 * @return \Netinteractive\Sentry\Role\RoleInterface
	 */
	public function createRole(array $attributes)
	{
		return $this->roleProvider->create($attributes);
	}


	/**
	 * Finds a user by the given user ID.
	 *
	 * @param  mixed  $id
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findUserById($id)
	{
		return $this->userProvider->findById($id);
	}

	/**
	 * Finds a user by the login value.
	 *
	 * @param  string  $login
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findUserByLogin($login)
	{
		return $this->userProvider->findByLogin($login);
	}

	/**
	 * Finds a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findUserByCredentials(array $credentials){
		return $this->userProvider->findByCredentials($credentials);
	}

	/**
	 * Finds a user by the given activation code.
	 *
	 * @param  string  $code
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \RuntimeException
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findUserByActivationCode($code)
	{
		return $this->userProvider->findByActivationCode($code);
	}

	/**
	 * Finds a user by the given reset password code.
	 *
	 * @param  string  $code
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \RuntimeException
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findUserByResetPasswordCode($code)
	{
		return $this->userProvider->findByResetPasswordCode($code);
	}

	/**
	 * Returns an all users.
	 *
	 * @return array
	 */
	public function findAllUsers()
	{
		return $this->userProvider->findAll();
	}

	/**
	 * Returns all users with role
	 * a group.
	 *
	 * @param  \Netinteractive\Sentry\Role\RoleInterface  $role
	 * @return array
	 */
	public function findAllUsersWithRole($role)
	{
		return $this->userProvider->findAllWithRole($role);
	}

	/**
	 * Returns all users with access to
	 * a permission(s).
	 *
	 * @param  string|array  $permissions
	 * @return array
	 */
	public function findAllUsersWithAccess($permissions)
	{
		return $this->userProvider->findAllWithAccess($permissions);
	}

	/**
	 * Returns all users with access to
	 * any given permission(s).
	 *
	 * @param  array  $permissions
	 * @return array
	 */
	public function findAllUsersWithAnyAccess(array $permissions)
	{
		return $this->userProvider->findAllWithAnyAccess($permissions);
	}

	/**
	 * Creates a user.
	 *
	 * @param  array  $credentials
	 * @return \Netinteractive\Sentry\User\UserInterface
	 */
	public function createUser(array $credentials)
	{
		return $this->userProvider->create($credentials);
	}

	/**
	 * Returns an empty user object.
	 *
	 * @return \Netinteractive\Sentry\User\UserInterface
	 */
	public function getEmptyUser()
	{
		return $this->userProvider->getEmptyUser();
	}

	/**
	 * Finds a throttler by the given user ID.
	 *
	 * @param  mixed   $id
	 * @param  string  $ipAddress
	 * @return \Netinteractive\Sentry\Throttling\ThrottleInterface
	 */
	public function findThrottlerByUserId($id, $ipAddress = null)
	{
		return $this->throttleProvider->findByUserId($id,$ipAddress);
	}

	/**
	 * Finds a throttling interface by the given user login.
	 *
	 * @param  string  $login
	 * @param  string  $ipAddress
	 * @return \Netinteractive\Sentry\Throttling\ThrottleInterface
	 */
	public function findThrottlerByUserLogin($login, $ipAddress = null)
	{
		return $this->throttleProvider->findByUserLogin($login,$ipAddress);
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if (isset($this->user))
		{
			return call_user_func_array(array($this->user, $method), $parameters);
		}

		throw new \BadMethodCallException("Method [$method] is not supported by Sentry or no User has been set on Sentry to access shortcut method.");
	}

}

