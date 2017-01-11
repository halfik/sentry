<?php

namespace Netinteractive\Sentry\SocialProfile\Elegant;

use Netinteractive\Elegant\Domain\ServiceProvider AS BusinessProvider;
use Netinteractive\Sentry\SocialProfile\ProviderInterface;
use Netinteractive\Sentry\SocialProfile\SocialProfileNotFoundException;

/**
 * Class Provider
 * @package Netinteractive\Sentry\SocialProfile\Elegant
 */
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
        $social = $this->getRepository()->where($blueprint->getStorageName().'.id', '=', $id)->first();


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
        $social = $this->getRepository()->profileId($profileId)->type($type)->first();

        if ( ! $social ) {
            throw new SocialProfileNotFoundException( sprintf( _('Nie odnaleziono profilu socialnego o ID [%s].'), $profileId ) );
        }

        return $social;
    }




}
