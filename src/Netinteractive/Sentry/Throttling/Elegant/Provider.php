<?php namespace Netinteractive\Sentry\Throttling\Elegant;


use Netinteractive\Sentry\Throttling\ProviderInterface;
use Netinteractive\Sentry\User\UserInterface;
use Netinteractive\Sentry\User\ProviderInterface as UserProviderInterface;

class Provider  implements ProviderInterface
{

    /**
     * The user provider used for finding users
     * to attach throttles to.
     *
     * @var \Netinteractive\Sentry\User\UserInterface
     */
    protected $userProvider;

    /**
     * Throttling status.
     *
     * @var bool
     */
    protected $enabled = true;

    /**
     * The  throttle model.
     *
     * @var string
     */
    protected $model = 'Netinteractive\Sentry\Throttling\Elegant\Record';

    /**
     * @var \Netinteractive\Elegant\Mapper\MapperInterface
     */
    protected $mapper;

    /**
     * Creates a new throttle provider.
     *
     * @param \Netinteractive\Sentry\User\ProviderInterface $userProvider
     * @param  string $model
     * @return void
     */
    public function __construct(UserProviderInterface $userProvider, $model = null)
    {
        $this->userProvider = $userProvider;

        if (isset($model)) {
            $this->model = $model;
        }

        $this->mapper = \App::make('ni.elegant.mapper.db', array($this->model));
    }

    /**
     * @param MapperInterface $mapper
     * @return $this
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * @return mixed|MapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function createRecord()
    {
        return \App::make($this->model);
    }

    /**
     * Finds a throttler by the given user ID.
     *
     * @param  \Netinteractive\Sentry\User\UserInterface $user
     * @param  string $ipAddress
     * @return \Netinteractive\Sentry\Throttling\ThrottleInterface
     */
    public function findByUser(UserInterface $user, $ipAddress = null)
    {
        $this->getMapper()->userId($user->getId());

        if ($ipAddress) {
            $this->getMapper()->ip($ipAddress);
        }

        if ( !$throttle = $this->getMapper()->first()) {
            $throttle = $this->createModel();
            $throttle->user__id = $user->getId();
            if ($ipAddress){
                $throttle->ip_address = $ipAddress;
            }

            $this->getMapper()->save($throttle);
        }

        return $throttle;
    }

    /**
     * Finds a throttler by the given user ID.
     *
     * @param  mixed $id
     * @param  string $ipAddress
     * @return \Netinteractive\Sentry\Throttling\ThrottleInterface
     */
    public function findByUserId($id, $ipAddress = null)
    {
        // TODO: Implement findByUserId() method.
    }

    /**
     * Finds a throttling interface by the given user login.
     *
     * @param  string $login
     * @param  string $ipAddress
     * @return \Netinteractive\Sentry\Throttling\ThrottleInterface
     */
    public function findByUserLogin($login, $ipAddress = null)
    {
        // TODO: Implement findByUserLogin() method.
    }

    /**
     * Enable throttling.
     *
     * @return void
     */
    public function enable()
    {
        // TODO: Implement enable() method.
    }

    /**
     * Disable throttling.
     *
     * @return void
     */
    public function disable()
    {
        // TODO: Implement disable() method.
    }

    /**
     * Check if throttling is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        // TODO: Implement isEnabled() method.
    }

}