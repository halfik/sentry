<?php namespace Cartalyst\Sentry\SocialProfile;


interface ProviderInterface {
    public function findById($id);

    public function findByProfileId($socialId);

    public function create(array $data);
}
