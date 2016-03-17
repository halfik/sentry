<?php namespace Cartalyst\Sentry\SocialProfile\Elegant;


use Cartalyst\Sentry\SocialProfile\ProviderInterface;
use Cartalyst\Sentry\SocialProfile\SocialProfileNotFoundException;

class Provider implements ProviderInterface {

	/**
	 * The Eloquent user model.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\SocialProfile\Elegant\SocialProfile';

    /**
     * @param null $model
     */
    public function __construct($model = null)
    {
        if (isset($model))
        {
            $this->model = $model;
        }
    }

    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    public function create(array $data)
    {
        $model = $this->createModel();
        $model->fill($data);

        $model->save();

        return $model;
    }

    /**
     * @param $id
     * @return mixed
     * @throws SocialProfileNotFoundException
     */
	public function findById($id)
	{
		$model = $this->createModel();

        $model->allowQueryAcl(false);
        $social = $model->find($id);
        $model->allowQueryAcl(true);

		if ( ! $social )
		{
			throw new SocialProfileNotFoundException( sprintf( _('Nie odnaleziono profilu socialego o ID [%s].'), $id ) );
		}

		return $social;
	}

    /**
     * @param $profileId
     * @return mixed
     */
    public function findByProfile($profileId, $type){
        $model = $this->createModel();



        $model->allowQueryAcl(false);
        $social = $model->where('profile_id','=',$profileId)->where('type','=',$type)->first();
        $model->allowQueryAcl(true);

        if ( ! $social )
        {
            throw new SocialProfileNotFoundException( sprintf( _('Nie odnaleziono profilu socialnego o ID [%s].'), $profileId ) );
        }


        return $social;
    }




}
