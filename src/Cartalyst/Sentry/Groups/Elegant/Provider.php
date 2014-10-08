<?php namespace Cartalyst\Sentry\Groups\Elegant;
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

use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\ProviderInterface;

class Provider implements ProviderInterface {

	/**
	 * The Eloquent group model.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Groups\Elegant\Group';

	/**
	 * Create a new Eloquent Group provider.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function __construct($model = null)
	{
        if (isset($model))
        {
            $this->model = $model;
        }
	}

	/**
	 * Find the group by ID.
	 *
	 * @param  int  $id
	 * @return \Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @throws \Cartalyst\Sentry\Groups\GroupNotFoundException
	 */
	public function findById($id)
	{
		$model = $this->createModel();

        $model->allowQueryAcl(false);
        $group = $model->newQuery()->find($id);
        $model->allowQueryAcl(true);

		if ( !$group)
		{
			throw new GroupNotFoundException("A group could not be found with ID [$id].");
		}

		return $group;
	}

	/**
	 * Find the group by name.
	 *
	 * @param  string  $name
	 * @return \Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @throws \Cartalyst\Sentry\Groups\GroupNotFoundException
	 */
	public function findByName($name)
	{
		$model = $this->createModel();

        $model->allowQueryAcl(false);
        $group = $model->newQuery()->where('name', '=', $name)->first();
        $model->allowQueryAcl(true);

		if ( ! $group )
		{
			throw new GroupNotFoundException("A group could not be found with the name [$name].");
		}

		return $group;
	}

    /**
     * Find the group by code
     * @param string $code
     * @return mixed
     * @throws \Cartalyst\Sentry\Groups\GroupNotFoundException
     */
    public function findByCode($code){
        $model = $this->createModel();

        $model->allowQueryAcl(false);
        $group = $model->newQuery()->where('code', '=', $code)->first();
        $model->allowQueryAcl(true);

        if ( ! $group)
        {
            throw new GroupNotFoundException("A group could not be found with the code [$code].");
        }

        return $group;
    }

	/**
	 * Returns all groups.
	 *
	 * @return array  $groups
	 */
	public function findAll()
	{
		$model = $this->createModel();

		return $model->newQuery()->get()->all();
	}

	/**
	 * Creates a group.
	 *
	 * @param  array  $attributes
	 * @return \Cartalyst\Sentry\Groups\GroupInterface
	 */
	public function create(array $attributes)
	{
		$group = $this->createModel();
		$group->fill($attributes);
		$group->save();
		return $group;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

	/**
	 * Sets a new model class name to be used at
	 * runtime.
	 *
	 * @param  string  $model
	 */
	public function setModel($model)
	{
		$this->model = $model;
	}

}
