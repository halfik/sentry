<?php

namespace Netinteractive\Sentry\SocialProfile;

/**
 * Interface ProviderInterface
 * @package Netinteractive\Sentry\SocialProfile
 */
interface ProviderInterface
{
    public function findById($id);

    public function findByProfile($profileId, $type);

    public function create(array $data);
}
