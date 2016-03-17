<?php namespace Cartalyst\Sentry;
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
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Auth\AuthManager;
use Cartalyst\Sentry\Cookies\CookieInterface;
use Cartalyst\Sentry\Cookies\NativeCookie;
use Cartalyst\Sentry\Groups\Elegant\Provider as GroupProvider;
use Cartalyst\Sentry\Groups\ProviderInterface as GroupProviderInterface;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Sessions\SessionInterface;
use Cartalyst\Sentry\Throttling\Elegant\Provider as ThrottleProvider;
use Cartalyst\Sentry\Throttling\ProviderInterface as ThrottleProviderInterface;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\Elegant\Provider as UserProvider;
use Cartalyst\Sentry\SocialProfile\ProviderInterface as SocialProfileProviderInterface;
use Cartalyst\Sentry\SocialProfile\Elegant\Provider as SocialProfileProvider;
use Cartalyst\Sentry\Users\ProviderInterface as UserProviderInterface;
use Cartalyst\Sentry\Resources\Elegant\Provider as ResourceProvider;
use Cartalyst\Sentry\Resources\ProviderInterface as ResourceProviderInterface;
use Cartalyst\Sentry\Users\UserInterface;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\Sentry\Users\UserNotActivatedException;

class Sentry {

	/**
	 * The user that's been retrieved and is used
	 * for authentication. Authentication methods
	 * are available for finding the user to set
	 * here.
	 *
	 * @var \Cartalyst\Sentry\Users\UserInterface
	 */
	protected $user;

	/**
	 * The session driver used by Sentry.
	 *
	 * @var \Cartalyst\Sentry\Sessions\SessionInterface
	 */
	protected $session;

	/**
	 * The cookie driver used by Sentry.
	 *
	 * @var \Cartalyst\Sentry\Cookies\CookieInterface
	 */
	protected $cookie;

	/**
	 * The user provider, used for retrieving
	 * objects which implement the Sentry user
	 * interface.
	 *
	 * @var \Cartalyst\Sentry\Users\ProviderInterface
	 */
	protected $userProvider;

	/**
	 * The group provider, used for retrieving
	 * objects which implement the Sentry group
	 * interface.
	 *
	 * @var \Cartalyst\Sentry\Groups\ProviderInterface
	 */
	protected $groupProvider;

    /**
     * @var \Cartalyst\Sentry\SocialProfile\ProviderInterface
     */
    protected $socialProfileProvider;

	/**
	 * The throttle provider, used for retrieving
	 * objects which implement the Sentry throttling
	 * interface.
	 *
	 * @var \Cartalyst\Sentry\Throttling\ProviderInterface
	 */
	protected $throttleProvider;

    /**
     * @var \Cartalyst\Sentry\Resources\ProviderInterface
     */
    protected $resourceProvider;

	/**
	 * The client's IP address associated with Sentry.
	 *
	 * @var string
	 */
	protected $ipAddress = '0.0.0.0';

	/**
	 * Create a new Sentry object.
	 *
	 * @param  \Cartalyst\Sentry\Users\ProviderInterface $userProvider
	 * @param  \Cartalyst\Sentry\Groups\ProviderInterface $groupProvider
	 * @param  \Cartalyst\Sentry\Throttling\ProviderInterface $throttleProvider
	 * @param  \Cartalyst\Sentry\Sessions\SessionInterface $session
	 * @param  \Cartalyst\Sentry\Cookies\CookieInterface $cookie
	 * @param  string $ipAddress
	 * @return void
	 */
	public function __construct(
		UserProviderInterface $userProvider = null,
		GroupProviderInterface $groupProvider = null,
		ThrottleProviderInterface $throttleProvider = null,
        ResourceProviderInterface $resourceProvider = null,
		SessionInterface $session = null,
		CookieInterface $cookie = null,
		$ipAddress = null,
        SocialProfileProviderInterface $socialProfileProvider = null
	)
	{
		$this->userProvider     = $userProvider ?: new UserProvider(new NativeHasher);
		$this->groupProvider    = $groupProvider ?: new GroupProvider;
        $this->socialProfileProvider   = $socialProfileProvider ?: new SocialProfileProvider;
		$this->throttleProvider = $throttleProvider ?: new ThrottleProvider($this->userProvider);
        $this->resourceProvider = $resourceProvider ?: new ResourceProvider();

		$this->session          = $session ?: new NativeSession;
		$this->cookie           = $cookie ?: new NativeCookie;

		if (isset($ipAddress))
		{
			$this->ipAddress = $ipAddress;
		}
	}

	/**
	 * Registers a user by giving the required credentials
	 * and an optional flag for whether to activate the user.
	 *
	 * @param  array  $credentials
	 * @param  bool   $activate
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 */
	public function register(array $credentials, $activate = false)
	{
        $authManager =  \App::make('AuthManager');
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
     * @return \Cartalyst\Sentry\Users\UserInterface
     * @throws \Cartalyst\Sentry\Throttling\UserBannedException
     * @throws \Cartalyst\Sentry\Throttling\UserSuspendedException
     * @throws \Cartalyst\Sentry\Users\LoginRequiredException
     * @throws \Cartalyst\Sentry\Users\PasswordRequiredException
     * @throws \Cartalyst\Sentry\Users\UserNotFoundException
     */
    public function authenticate(array $credentials)
    {
        $authManager =  \App::make('AuthManager');
        $authProvider = $authManager->getCurrent();
        return $authProvider->authenticate($credentials);
    }

	/**
	 * Alias for authenticating with the remember flag checked.
	 *
	 * @param  array  $credentials
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 */
	public function authenticateAndRemember(array $credentials)
	{
		return $this->authenticate($credentials, true);
	}
     //$this->session->put('mainRoleCode', $user->getMainGroup()->code);
        //$this->session->put('userData', $user->toArray());
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
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 * @throws \Cartalyst\Sentry\Users\UserNotActivatedException
	 */
	public function login(UserInterface $user, $remember = false)
	{
		if ( ! $user->isActivated())
		{
			$login = $user->getLogin();
            throw new UserNotActivatedException(sprintf( _('Nie udało się zalogować użytkownika [%s] ponieważ konto nie jest aktywne.'), $login ));
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

        \Session::put('mainRoleCode', $user->getMainGroup()->code);
        \Session::put('userData', $user->getSessionData());

		// The user model can attach any handlers
		// to the "recordLogin" event.
		$user->recordLogin();
	}

	/**
	 * Alias for logging in and remembering.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
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
                \Session::flush();
		$this->session->forget();
		$this->cookie->forget();
	}

	/**
	 * Sets the user to be used by Sentry.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface
	 * @return void
	 */
	public function setUser(UserInterface $user)
	{
		$this->user = $user;
	}

	/**
	 * Returns the current user being used by Sentry, if any.
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface
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
	 * @param  \Cartalyst\Sentry\Sessions\SessionInterface  $session
	 * @return void
	 */
	public function setSession(SessionInterface $session)
	{
		$this->session = $session;
	}

	/**
	 * Gets the session driver for Sentry.
	 *
	 * @return \Cartalyst\Sentry\Sessions\SessionInterface
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Sets the cookie driver for Sentry.
	 *
	 * @param  \Cartalyst\Sentry\Cookies\CookieInterface  $cookie
	 * @return void
	 */
	public function setCookie(CookieInterface $cookie)
	{
		$this->cookie = $cookie;
	}

	/**
	 * Gets the cookie driver for Sentry.
	 *
	 * @return \Cartalyst\Sentry\Cookies\CookieInterface
	 */
	public function getCookie()
	{
		return $this->cookie;
	}

	/**
	 * Sets the group provider for Sentry.
	 *
	 * @param  \Cartalyst\Sentry\Groups\ProviderInterface
	 * @return void
	 */
	public function setGroupProvider(GroupProviderInterface $groupProvider)
	{
		$this->groupProvider = $groupProvider;
	}

	/**
	 * Gets the group provider for Sentry.
	 *
	 * @return \Cartalyst\Sentry\Groups\ProviderInterface
	 */
	public function getGroupProvider()
	{
		return $this->groupProvider;
	}

	/**
	 * Sets the user provider for Sentry.
	 *
	 * @param  \Cartalyst\Sentry\Users\ProviderInterface
	 * @return void
	 */
	public function setUserProvider(UserProviderInterface $userProvider)
	{
		$this->userProvider = $userProvider;
	}

    /**
     * Sets the social profile provider
     *
     * @param SocialProfileProviderInterface $socialProfileProvider
     */
    public function setSocialProfileProvider(SocialProfileProviderInterface $socialProfileProvider)
    {
        $this->socialProfileProvider = $socialProfileProvider;
    }

	/**
	 * Gets the user provider for Sentry.
	 *
	 * @return \Cartalyst\Sentry\Users\ProviderInterface
	 */
	public function getUserProvider()
	{
		return $this->userProvider;
	}

    /**
     * Gets the social profile for Sentry.
     *
     *  @return \Cartalyst\Sentry\SocialProfile\ProviderInterface
     */
    public function getSocialProfileProvider()
    {
        return $this->socialProfileProvider;
    }

	/**
	 * Sets the throttle provider for Sentry.
	 *
	 * @param  \Cartalyst\Sentry\Throttling\ProviderInterface
	 * @return void
	 */
	public function setThrottleProvider(ThrottleProviderInterface $throttleProvider)
	{
		$this->throttleProvider = $throttleProvider;
	}

	/**
	 * Gets the throttle provider for Sentry.
	 *
	 * @return \Cartalyst\Sentry\Throttling\ProviderInterface
	 */
	public function getThrottleProvider()
	{
		return $this->throttleProvider;
	}

    /**
     * Sets the throttle provider for Sentry.
     *
     * @param  \Cartalyst\Sentry\Throttling\ProviderInterface
     * @return void
     */
    public function setResourceProvider(ResourceProviderInterface $resourceProvider)
    {
        $this->resourceProvider = $resourceProvider;
    }


    /**
     * Gets the resource provider for Sentry.
     *
     * @return \Cartalyst\Sentry\Resources\ProviderInterface
     */
    public function getResourceProvider(){
        return $this->resourceProvider;
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
     * wyszukuje po id roli
     * @param $id
     * @return Groups\GroupInterface
     */
    public function findRoleById($id){
        return $this->findGroupById($id);
    }

    /**
     * wyszukuje roli po nazwie
     * @param string $name
     * @return Groups\GroupInterface
     */
    public function findRoleByName($name){
        return $this->findGroupByName($name);
    }

    /**
     * zwraca wszystkie role
     * @return array
     */
    public function findAllRoles(){
        return $this->findAllGroups();
    }

    /**
     * tworzy role
     * @param array $attributes
     * @return Groups\GroupInterface
     */
    public function createRole(array $attributes){
        return $this->createGroup($attributes);
    }

	/**
	 * Find the group by ID.
	 *
	 * @param  int  $id
	 * @return \Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @throws \Cartalyst\Sentry\Groups\GroupNotFoundException
	 */
	public function findGroupById($id)
	{
		return $this->groupProvider->findById($id);
	}

	/**
	 * Find the group by name.
	 *
	 * @param  string  $name
	 * @return \Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @throws \Cartalyst\Sentry\Groups\GroupNotFoundException
	 */
	public function findGroupByName($name)
	{
		return $this->groupProvider->findByName($name);
	}

    /**
     * Find the group by code.
     *
     * @param  string  $code
     * @return \Cartalyst\Sentry\Groups\GroupInterface  $group
     * @throws \Cartalyst\Sentry\Groups\GroupNotFoundException
     */
    public function findGroupByCode($code)
    {
        return $this->groupProvider->findByCode($code);
    }

	/**
	 * Returns all groups.
	 *
	 * @return array  $groups
	 */
	public function findAllGroups()
	{
		return $this->groupProvider->findAll();
	}

	/**
	 * Creates a group.
	 *
	 * @param  array  $attributes
	 * @return \Cartalyst\Sentry\Groups\GroupInterface
	 */
	public function createGroup(array $attributes)
	{
		return $this->groupProvider->create($attributes);
	}


	/**
	 * Finds a user by the given user ID.
	 *
	 * @param  mixed  $id
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 * @throws \Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function findUserById($id)
	{
		return $this->userProvider->findById($id);
	}

	/**
	 * Finds a user by the login value.
	 *
	 * @param  string  $login
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 * @throws \Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function findUserByLogin($login)
	{
		return $this->userProvider->findByLogin($login);
	}

	/**
	 * Finds a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 * @throws \Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function findUserByCredentials(array $credentials){
		return $this->userProvider->findByCredentials($credentials);
	}

	/**
	 * Finds a user by the given activation code.
	 *
	 * @param  string  $code
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 * @throws \RuntimeException
	 * @throws \Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function findUserByActivationCode($code)
	{
		return $this->userProvider->findByActivationCode($code);
	}

	/**
	 * Finds a user by the given reset password code.
	 *
	 * @param  string  $code
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 * @throws \RuntimeException
	 * @throws \Cartalyst\Sentry\Users\UserNotFoundException
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
	 * Returns all users who belong to
	 * a group.
	 *
	 * @param  \Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return array
	 */
	public function findAllUsersInGroup($group)
	{
		return $this->userProvider->findAllInGroup($group);
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
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 */
	public function createUser(array $credentials)
	{
		return $this->userProvider->create($credentials);
	}

	/**
	 * Returns an empty user object.
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface
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
	 * @return \Cartalyst\Sentry\Throttling\ThrottleInterface
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
	 * @return \Cartalyst\Sentry\Throttling\ThrottleInterface
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

    /**
     * Metoda wyszukująca użytkowników po roleCode
     * @param $roleName
     * @return mixed
     */
    public function findUsersByRole($roleCode)
    {
        $q = App('User')->getQuery();
        $q->select('user.*')
            ->join('users_roles','users_roles.user_id','=','user.id')
            ->join('roles','roles.id','=','users_roles.role_id')
        ;

        if(!empty($roleCode)){
            $q->where('roles.code','=',$roleCode);
        }

        return $q->get();
    }

}

