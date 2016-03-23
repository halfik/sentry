<?php namespace Netinteractive\Sentry\Role;
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
	 * Find the role by ID.
	 *
	 * @param  int  $id
	 * @return \Netinteractive\Sentry\Role\RoleInterface  $group
	 * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
	 */
	public function findById($id);

	/**
	 * Find the role by name.
	 *
	 * @param  string  $name
	 * @return \Netinteractive\Sentry\Role\RoleInterface  $group
	 * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
	 */
	public function findByName($name);

    /**
     * Find the role by code.
     *
     * @param  string  $code
     * @return \Netinteractive\Sentry\Role\RoleInterface  $group
     * @throws \Netinteractive\Sentry\Role\RoleNotFoundException
     */
    public function findByCode($code);


    /**
	 * Returns all roles.
	 *
	 * @return array  $groups
	 */
	public function findAll();

	/**
	 * Creates a role.
	 *
	 * @param  array  $attributes
	 * @return \Netinteractive\Sentry\Role\RoleInterface
	 */
	public function create(array $attributes);

}
