<?php namespace Netinteractive\Sentry\Facades\Kohana;
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

use Netinteractive\Sentry\Cookies\KohanaCookie;
use Netinteractive\Sentry\Facades\Facade;
use Netinteractive\Sentry\Groups\Kohana\Provider as GroupProvider;
use Netinteractive\Sentry\Sessions\KohanaSession;
use Netinteractive\Sentry\Sentry as BaseSentry;
use Netinteractive\Sentry\Throttling\Kohana\Provider as ThrottleProvider;
use Netinteractive\Sentry\Users\Kohana\Provider as UserProvider;

class Sentry extends Facade {

	/**
	 * Creates a new instance of Sentry.
	 *
	 * @return \Netinteractive\Sentry\Sentry
	 */
	public static function createSentry()
	{
		$config = \Kohana::$config->load('sentry')->as_array();

		//If the user hasn't defined a config file offer defaults
		if ( count($config) == 0 )
		{
			$config = array(
				'session_driver' => 'native',
				'session_key' => 'cartalyst_sentry',
				'cookie_key' => 'cartalyst_sentry',
				'hasher' => 'Bcrypt'
			);
		}

		//Choose the hasher
		switch ( $config['hasher'] )
		{
			default:
			case 'Bcrypt':
				$hasher = new \Netinteractive\Sentry\Hashing\BcryptHasher;
				break;
			case 'Native':
				$hasher = new \Netinteractive\Sentry\Hashing\NativeHasher;
				break;
			case 'Sha256':
				$hasher = new \Netinteractive\Sentry\Hashing\Sha256Hasher;
				break;
		}

		return new BaseSentry(
			$userProvider = new UserProvider($hasher),
			new GroupProvider,
			new ThrottleProvider($userProvider),
			new KohanaSession(\Session::instance($config['session_driver']), $config['session_key']),
			new KohanaCookie($config['cookie_key']),
			\Request::$client_ip
		);
	}

}
