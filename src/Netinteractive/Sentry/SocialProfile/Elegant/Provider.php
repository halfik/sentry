<?php namespace Netinteractive\Sentry\SocialProfile\Elegant;


use Netinteractive\Elegant\Model\Provider AS BusinessProvider;
use Netinteractive\Sentry\SocialProfile\ProviderInterface;
use Netinteractive\Sentry\SocialProfile\SocialProfileNotFoundException;

class Provider extends  BusinessProvider implements ProviderInterface
{

    /**
     * @param null|string $record
     */
    public function __construct($record=null)
    {
        if (!$record){
            $record = 'Netinteractive\Sentry\SocialProfile\Elegant\Record';
        }
        parent::__construct($record);
    }


    /**
     * Creates a social profile.
     *
     * @param  array  $credentials
     * @return \Netinteractive\Sentry\User\UserInterface
     */
    public function create(array $credentials)
    {
        return parent::create($credentials);
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
