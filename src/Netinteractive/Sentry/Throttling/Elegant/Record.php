<?php namespace Netinteractive\Sentry\Throttling\Elegant;


use Netinteractive\Sentry\Throttling\ThrottleInterface;

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
        return $this->user()->get();
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
     * Ban the user.
     *
     * @return bool
     */
    public function ban()
    {
        // TODO: Implement ban() method.
    }

    /**
     * Unban the user.
     *
     * @return void
     */
    public function unban()
    {
        // TODO: Implement unban() method.
    }

    /**
     * Check if user is banned
     *
     * @return void
     */
    public function isBanned()
    {
        // TODO: Implement isBanned() method.
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
        // TODO: Implement check() method.
    }



}