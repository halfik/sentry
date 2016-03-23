<?php namespace Netinteractive\Sentry\SocialProfile\Elegant;


use Netinteractive\Sentry\SocialProfile\ProviderInterface;
use Netinteractive\Sentry\SocialProfile\SocialProfileNotFoundException;

class Provider implements ProviderInterface
{

	/**
	 * The Eloquent user model.
	 *
	 * @var string
	 */
	protected $model = 'Netinteractive\Sentry\SocialProfile\Elegant\Record';

    /**
     * @var \Netinteractive\Elegant\Mapper\MapperInterface
     */
    protected $mapper;

    /**
     * @param null $model
     */
    public function __construct($model = null)
    {
        if (isset($model)) {
            $this->model = $model;
        }

        $this->mapper = \App::make('ni.elegant.mapper.db', array($this->model));
    }


    /**
     * @param \Netinteractive\Elegant\Mapper\MapperInterface $mapper
     * @return $this
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * @return mixed|\Netinteractive\Elegant\Mapper\MapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * @return mixed
     */
    public function createRecord()
    {
        return \App::make($this->model);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $record = $this->createRecord();
        $record->fill($data);

        $this->getMapper()->save($record);

        return $record;
    }

    /**
     * @param $id
     * @return mixed
     * @throws SocialProfileNotFoundException
     */
	public function findById($id)
	{
        $blueprint = $this->createRecord()->getBlueprint();
        $social = $this->getMapper()->where($blueprint->getStorageName().'.id', '=', $id)->first();


		if ( !$social ) {
			throw new SocialProfileNotFoundException( sprintf( _('Nie odnaleziono profilu socialego o ID [%s].'), $id ) );
		}

		return $social;
	}

    /**
     * @param $profileId
     * @return mixed
     */
    public function findByProfile($profileId, $type)
    {
        $social = $this->getMapper()->profileId($profileId)->type($type)->first();

        if ( ! $social ) {
            throw new SocialProfileNotFoundException( sprintf( _('Nie odnaleziono profilu socialnego o ID [%s].'), $profileId ) );
        }

        return $social;
    }




}
