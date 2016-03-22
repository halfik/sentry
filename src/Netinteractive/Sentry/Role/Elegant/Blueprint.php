<?php

namespace Netinteractive\Sentry\Role\Elegant;

use Netinteractive\Elegant\Search\Searchable;
use Netinteractive\Elegant\Model\Blueprint AS BaseBlueprint;

class Blueprint extends  BaseBlueprint
{
    public static $superUserCode = 'admin';

    /**
     * @return mixed
     */
    protected function init()
    {
        $table = \Config::get('netinteractive.sentry.role_table');

        $this->setStorageName($table);
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';

        $config = \Config::get('netinteractive.sentry');
        $this->getRelationManager()
            ->belongsToMany('users', $config['users']['model'], $config['user_role_pivot_table'], 'role__id', array('user__id')  )
        ;


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
            'name' => array(
                'title' =>_('Nazwa'),
                'type' => static::TYPE_STRING,
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules' => array(
                    'any' => 'required|max:255',
                )
            ),
            'code'=>array(
                'title' =>_('Kod'),
                'type' => static::TYPE_STRING,
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules' => array(
                    'any' => 'required||max:255',
                )
            ),
            'permissions' => array(
                'title' => _('Uprawnienia'),
                'type'=> static::TYPE_STRING,
                'filters' => array(
                    'save' => array(
                        'jsonEncode'
                    ),
                    'fill' => array(
                        'jsonDecode'
                    )
                )
            ),
            'is_hidden' => array(
                'title' => _('Rola ukryta'),
                'type' => static::TYPE_BOOL,
                'sortable' => true,
                'rules' => array(
                    'any' => 'boolean'
                ),
                'filters' => array(
                    'display' => array('bool'),
                )
            ),
            'weight' => array(
                'title' => _('Waga'),
                'type'=> static::TYPE_INT,
            ),
            'created_at' => array(
                'title'=> _('created_at'),
                'type' => static::TYPE_DATETIME,
                'sortable' => true,
                'rules' => array(
                    'any' => 'date',
                ),
                'filters' => array(
                    'display' => array('date:Y-m-d')
                ),
            ),
            'updated_at' => array(
                'title'=> _('updated_at'),
                'type' => static::TYPE_DATETIME,
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
}