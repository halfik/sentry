<?php namespace Netinteractive\Sentry\SocialProfile\Elegant;

use Netinteractive\Elegant\Search\Searchable;
use Netinteractive\Elegant\Model\Blueprint AS BaseBlueprint;


class Blueprint extends BaseBlueprint
{
    /**
     * @return mixed
     */
    protected function init()
    {
        $config = \Config::get('packages.netinteractive.sentry.config');
        $table = $config['social_profile_table'];

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
                    'any' => 'required|integer',
                )
            ),
            'profile_id' => array(
                'title' => _('Id profilu'),
                'type' => static::TYPE_STRING,
                'rules' => array(
                    'any' => 'required|max:255',
                )
            ),
            'type' => array(
                'title' => _('Typ'),
                'type' => static::TYPE_STRING,
                'rules' => array(
                    'any' => 'required|max:255',
                )
            ),
            'created_at' => array(
                'title'=> _('Data utworzenia'),
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
                'title'=> _('Data modyfikacji'),
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

    /**
     * Returns scope object
     * @return null
     */
    public function getScopeObject()
    {
        return new Scope($this->getStorageName());
    }
}