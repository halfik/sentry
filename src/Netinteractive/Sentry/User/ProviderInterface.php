<?php namespace Netinteractive\Sentry\User;
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

use Netinteractive\Sentry\Role\RoleInterface;

interface ProviderInterface
{

	/**
	 * Finds a user by the given user ID.
	 *
	 * @param  mixed  $id
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findById($id);

	/**
	 * Finds a user by the login value.
	 *
	 * @param  string  $login
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findByLogin($login);


    /**
     * Finds a user by the email value.
     *
     * @param  string  $email
     * @return \Netinteractive\Sentry\User\UserInterface
     * @throws \Netinteractive\Sentry\User\UserNotFoundException
     */
    public function findByEmail($email);


	/**
	 * Finds a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findByCredentials(array $credentials);

	/**
	 * Finds a user by the given activation code.
	 *
	 * @param  string  $code
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function findByActivationCode($code);

	/**
	 * Finds a user by the given reset password code.
	 *
	 * @param  string  $code
	 * @return \Netinteractive\Sentry\User\UserInterface
	 * @throws \RuntimeException
	 * @throws \Netinteractive\Sentry\User\UserNotFoundException
	 */
	public function findByResetPasswordCode($code);

	/**
	 * Returns an all users.
	 *
	 * @return array
	 */
	public function findAll();

	/**
	 * Returns all users who belong to
	 * a group.
	 *
	 * @param  string  $code
	 * @return array
	 */
	public function findAllWithRole($code);


	/**
	 * Creates a user.
	 *
	 * @param  array  $credentials
	 * @return \Netinteractive\Sentry\User\UserInterface
	 */
	public function create(array $credentials);

	/**
	 * Returns an empty user object.
	 *
	 * @return \Netinteractive\Sentry\User\UserInterface
	 */
	public function getEmptyUser();

}
