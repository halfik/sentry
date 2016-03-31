<?php namespace Netinteractive\Sentry\Throttling\Elegant;

use Netinteractive\Elegant\Search\Searchable;
use Netinteractive\Elegant\Model\Blueprint AS BaseBlueprint;

class Blueprint extends BaseBlueprint
{

    /**
     * Attempt limit.
     *
     * @var int
     */
    protected static $attemptLimit;

    /**
     * Suspensions time in minutes.
     *
     * @var int
     */
    protected static $suspensionTime;

    /**
     * @return mixed
     */
    protected function init()
    {
        $config = \Config::get('packages.netinteractive.sentry.config');
        $table = $config['throttle_table'];

        $this->setStorageName($table);
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';

        $this->getRelationManager()->belongsTo('user',$config['users']['model'], 'user__id', 'id');

        $this->fields = array(
            'id' => array(
                'title' => 'Id',
                'type' => static::TYPE_INT,
                'sortable' => true,
                'rules' => array(
                    'any' => 'integer',
                    'update' => 'required'
                )
            ),
            'user__id' => array(
                'title' => _('Id użytkownika'),
                'type' => static::TYPE_INT,
                'sortable' => true,
                'rules' => array(
                    'any' => 'integer',
                )
            ),
            'ip_address' => array(
                'title' => _('IP'),
                'type' => static::TYPE_STRING,
                'rules' => array(
                    'any' => 'max:255',
                )
            ),
            'attempts' =>  array(
                'title' => _('Próby logowania'),
                'type' => static::TYPE_INT,
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|integer',
                ),
                'filters' => array(
                    'fill' => array('emptyToZero')
                )
            ),
            'suspended' => array(
                'title' => _('Czy zawieszony?'),
                'type' => static::TYPE_BOOL,
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|boolean'
                ),
                'filters' => array(
                    'display' => array('bool'),
                    'fill' => array('emptyToFalse')
                )
            ),
            'banned' => array(
                'title' => _('Czy zbanowany?'),
                'type' => static::TYPE_BOOL,
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|boolean'
                ),
                'filters' => array(
                    'display' => array('bool'),
                    'fill' => array('emptyToFalse')
                )
            ),
            'last_attempt_at' => array(
                'title' => _('Data ostatniej próby'),
                'type' => static::TYPE_DATETIME,
                'searchable' => Searchable::$equal,
                'sortable' => true,
                'rules' => array(
                    'any' => 'date',
                ),
                'filters' => array(
                    'display' => array('date:Y-m-d')
                ),
            ),
            'suspended_at' => array(
                'title' => _('Data zawieszenia'),
                'type' => static::TYPE_DATETIME,
                'searchable' => Searchable::$equal,
                'sortable' => true,
                'rules' => array(
                    'any' => 'date',
                ),
                'filters' => array(
                    'display' => array('date:Y-m-d')
                ),
            ),
            'banned_at' => array(
                'title' => _('Data zbanowania'),
                'type' => static::TYPE_DATETIME,
                'searchable' => Searchable::$equal,
                'sortable' => true,
                'rules' => array(
                    'any' => 'date',
                ),
                'filters' => array(
                    'display' => array('date:Y-m-d')
                ),
            ),
        );

        return parent::init();
    }


    /**
     * returns login attemnt limit
     * @return int
     */
    public static function getAttemptLimit()
    {
        if (!static::$attemptLimit){
            static::$attemptLimit = \Config::get(\Config::get('packages.netinteractive.sentry.config.throttling.attempt_limit'));
        }

        return static::$attemptLimit;
    }


    /**
     * Returns suspension time in minutes
     * @return int
     */
    public static function getSuspensionTime()
    {
        if (!static::$suspensionTime){
            static::$suspensionTime = \Config::get(\Config::get('packages.netinteractive.sentry.config.throttling.suspension_time'));
        }

        return static::$suspensionTime;
    }

    /**
     * Returns scope object
     * @return null
     */
    public function getScopeObject()
    {
        return new Scope($this->getStorageName());
    }
}