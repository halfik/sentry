<?php namespace Netinteractive\Sentry\Throttling;

use Netinteractive\Sentry\User\UserInterface;

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

interface ProviderInterface {


	/**
	 * Finds a throttler by the given user ID.
	 *
	 * @param  \Netinteractive\Sentry\User\UserInterface   $user
	 * @param  string  $ipAddress
	 * @return \Netinteractive\Sentry\Throttling\ThrottleInterface
	 */
	public function findByUser(UserInterface $user, $ipAddress = null);

	/**
	 * Finds a throttler by the given user ID.
	 *
	 * @param  mixed   $id
	 * @param  string  $ipAddress
	 * @return \Netinteractive\Sentry\Throttling\ThrottleInterface
	 */
	public function findByUserId($id, $ipAddress = null);

	/**
	 * Finds a throttling interface by the given user login.
	 *
	 * @param  string  $login
	 * @param  string  $ipAddress
	 * @return \Netinteractive\Sentry\Throttling\ThrottleInterface
	 */
	public function findByUserLogin($login, $ipAddress = null);

	/**
	 * Enable throttling.
	 *
	 * @return void
	 */
	public function enable();

	/**
	 * Disable throttling.
	 *
	 * @return void
	 */
	public function disable();

	/**
	 * Check if throttling is enabled.
	 *
	 * @return bool
	 */
	public function isEnabled();

    /**
     * Inspects the last attempt vs the suspension time
     * (the time in which attempts must space before the
     * account is suspended). If we can clear our attempts
     * now, we'll do so and save.
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return void
     */
    public function clearLoginAttempts(\Netinteractive\Sentry\Throttling\ThrottleInterface $record);

    /**
     * Add a new login attempt.
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return void
     */
    public function addLoginAttempt(\Netinteractive\Sentry\Throttling\ThrottleInterface $record);

    /**
     * Suspend the user associated with the throttle
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return void
     */
    public function suspend(\Netinteractive\Sentry\Throttling\ThrottleInterface $record);


    /**
     * Unsuspend the user.
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return void
     */
    public function unsuspend(\Netinteractive\Sentry\Throttling\ThrottleInterface $record);

    /**
     * Ban the user.
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return bool
     */
    public function ban(\Netinteractive\Sentry\Throttling\ThrottleInterface $record);

    /**
     * Unban the user.
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return void
     */
    public function unban(\Netinteractive\Sentry\Throttling\ThrottleInterface $record);
}
