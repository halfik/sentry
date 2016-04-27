<?php namespace Netinteractive\Sentry\Role\Elegant;

use Netinteractive\Elegant\Search\Searchable;
use Netinteractive\Elegant\Model\Blueprint AS BaseBlueprint;

class Blueprint extends  BaseBlueprint
{
    public static $superUserCode = 'admin';

    /**
     * Allowed permissions values.
     *
     * Possible options:
     *    0 => Remove.
     *    1 => Add.
     *
     * @var array
     */
    public static $allowedPermissionsValues = array(0, 1);

    /**
     * @return mixed
     */
    protected function init()
    {
        $config = \Config::get('packages.netinteractive.sentry.config');
        $table = $config['role_table'];

        $this->setStorageName($table);
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';
        $this->timestamps = true;


        $this->getRelationManager()
            ->belongsToMany('users', $config['users']['model'], $config['user_role_pivot_table'], 'role__id', array('user__id')  )
        ;


        $this->fields = array(
            'id' => array(
                'title' => 'Id',
                'type' => static::TYPE_INT,
                'searchable' => Searchable::$equal,
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
                'protected' =>  [
                    static::PROTECT_CREATE,
                    static::PROTECT_VIEW,
                    static::PROTECT_UPDATE,
                    static::PROTECT_DELETE
                ],
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
                'searchable' => Searchable::$equal,
                'rules' => array(
                    'any' => 'required|boolean'
                ),
                'filters' => array(
                    'display' => array('bool'),
                )
            ),
            'weight' => array(
                'title' => _('Waga'),
                'sortable' => true,
                'searchable' => Searchable::$equal,
                'rules' => array(
                    'any' => 'required|int'
                ),
                'type'=> static::TYPE_INT,
            ),
            'created_at' => array(
                'title'=> _('Data utworzenia'),
                'type' => static::TYPE_DATETIME,
                'searchable' => Searchable::$equal,
                'protected' =>  [
                    static::PROTECT_CREATE,
                    static::PROTECT_UPDATE,
                    static::PROTECT_DELETE
                ],
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
                'searchable' => Searchable::$equal,
                'protected' =>  [
                    static::PROTECT_CREATE,
                    static::PROTECT_UPDATE,
                    static::PROTECT_DELETE
                ],
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

    /**
     * Returns allowed values for permission
     * @return array
     */
    public function getAllowedPermissionsValues()
    {
        return static::$allowedPermissionsValues;
    }
}