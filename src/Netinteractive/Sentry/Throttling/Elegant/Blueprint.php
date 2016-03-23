<?php namespace Netinteractive\Sentry\Throttling\Elegant;

use Netinteractive\Elegant\Search\Searchable;
use Netinteractive\Elegant\Model\Blueprint AS BaseBlueprint;

class Blueprint extends BaseBlueprint
{
    /**
     * @return mixed
     */
    protected function init()
    {
        $table = \Config::get('netinteractive.sentry.throttle_table');

        $this->setStorageName($table);
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';

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
     * Returns scope object
     * @return null
     */
    public function getScopeObject(){
        return new Scope($this->getStorageName());
    }
}