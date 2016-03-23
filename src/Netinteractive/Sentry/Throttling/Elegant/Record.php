<?php namespace Netinteractive\Sentry\Throttling\Elegant;


use Netinteractive\Sentry\Throttling\ThrottleInterface;
use Netinteractive\Sentry\Throttling\UserBannedException;
use Netinteractive\Sentry\Throttling\UserSuspendedException;

class Record extends \Netinteractive\Elegant\Model\Record implements ThrottleInterface
{
    /**
     * @return $this
     */
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }

    /**
     * Returns the associated user with the throttler.
     *
     * @return \Netinteractive\Sentry\User\UserInterface
     */
    public function getUser()
    {
        return $this->user()->first();
    }

    /**
     * Get the current amount of attempts.
     *
     * @return int
     */
    public function getLoginAttempts()
    {
        return $this->attempts;
    }



    /**
     * Check if the user is suspended.
     *
     * @return bool
     */
    public function isSuspended()
    {
        if ($this->suspended and $this->suspended_at) {
            return (bool) $this->suspended;
        }

        return false;
    }



    /**
     * Check if user is banned
     *
     * @return void
     */
    public function isBanned()
    {
        return $this->banned;
    }

    /**
     * Check user throttle status.
     *
     * @return bool
     * @throws \Netinteractive\Sentry\Throttling\UserBannedException
     * @throws \Netinteractive\Sentry\Throttling\UserSuspendedException
     */
    public function check()
    {
        if ($this->isBanned()) {
            throw new UserBannedException(sprintf(
                'Użytkownik [%s] jest zbanowany.',
                $this->getUser()->getLogin()
            ));
        }

        if ($this->isSuspended()) {
            throw new UserSuspendedException(sprintf(
                'Użytkownik [%s] został zawieszony.',
                $this->getUser()->getLogin()
            ));
        }

        return true;
    }



}