<?php namespace Netinteractive\Sentry\Throttling\Elegant;


use Carbon\Carbon;
use Netinteractive\Elegant\Model\Provider AS BusinessProvider;
use Netinteractive\Sentry\Throttling\ProviderInterface;
use Netinteractive\Sentry\User\UserInterface;
use Netinteractive\Sentry\User\ProviderInterface as UserProviderInterface;

class Provider extends BusinessProvider implements ProviderInterface
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
     * Creates a new throttle provider.
     *
     * @param \Netinteractive\Sentry\User\ProviderInterface $userProvider
     * @param  string $recordClass
     * @return void
     */
    public function __construct(UserProviderInterface $userProvider, $recordClass = null)
    {
        $this->userProvider = $userProvider;

        if (!$recordClass){
            $recordClass = 'Netinteractive\Sentry\Throttling\Elegant\Record';
        }

        parent::__construct($recordClass);
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
            $throttle = $this->createRecord();
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
        return $this->findByUser($this->userProvider->findById($id),$ipAddress);
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
        return $this->findByUser($this->userProvider->findByLogin($login),$ipAddress);
    }

    /**
     * Enable throttling.
     *
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disable throttling.
     *
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Check if throttling is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Inspects the last attempt vs the suspension time
     * (the time in which attempts must space before the
     * account is suspended). If we can clear our attempts
     * now, we'll do so and save.
     *
     * @return void
     */
    public function clearLoginAttempts(\Netinteractive\Sentry\Throttling\ThrottleInterface $record)
    {
        $blueprint = $record->getBlueprint();
        $suspensionTime  = $blueprint->getSuspensionTime();
        $lastAttempt = null;
        $now = new Carbon();
        $clearAttemptsAt = new Carbon();


        if ( $record->last_attempt_at != null){
            $lastAttempt = new Carbon($record->last_attempt_at);
            $clearAttemptsAt = $lastAttempt->modify("+{$suspensionTime} minutes");
        }


        if  ($lastAttempt == null || $clearAttemptsAt <= $now) {
            $record->attempts = 0;
            $this->getMapper()->save($record);
        }

        unset($lastAttempt);
        unset($clearAttemptsAt);
        unset($now);
    }

    /**
     * Add a new login attempt.
     *
     * @return void
     */
    public function addLoginAttempt(\Netinteractive\Sentry\Throttling\ThrottleInterface $record)
    {
        $blueprint = $record->getBlueprint();
        $record->attempts++;
        $record->last_attempt_at = new Carbon();

        if ($record->getLoginAttempts() >= $blueprint->getAttemptLimit()) {
            $this->suspend($record);
        }
        else {
            $this->getMapper()->save($record);
        }
    }

    /**
     * Suspend the user associated with the throttle
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return void
     */
    public function suspend(\Netinteractive\Sentry\Throttling\ThrottleInterface $record)
    {
        if (!$record->suspended) {
            $record->suspended = true;
            $record->suspended_at = new Carbon();
            $this->getMapper()->save($record);
        }
    }

    /**
     * Unsuspend the user.
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return void
     */
    public function unsuspend(\Netinteractive\Sentry\Throttling\ThrottleInterface $record)
    {
        if ($record->suspended) {
            $record->attempts        = 0;
            $record->last_attempt_at = null;
            $record->suspended       = false;
            $record->suspended_at    = null;
            $this->getMapper()->save($record);
        }
    }


    /**
     * Ban the user.
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return bool
     */
    public function ban(\Netinteractive\Sentry\Throttling\ThrottleInterface $record)
    {
        if (!$record->banned) {
            $record->banned = true;
            $record->banned_at = new Carbon();
            $this->getMapper()->save($record);
        }
    }

    /**
     * Unban the user.
     *
     * @param \Netinteractive\Sentry\Throttling\ThrottleInterface $record
     * @return void
     */
    public function unban(\Netinteractive\Sentry\Throttling\ThrottleInterface $record)
    {
        if ($record->banned) {
            $record->banned = false;
            $record->banned_at = null;
            $this->getMapper()->save($record);
        }
    }

}