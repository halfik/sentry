<?php namespace Netinteractive\Sentry\User\Elegant;

use Netinteractive\Elegant\Search\Searchable;
use Netinteractive\Elegant\Model\Blueprint AS BaseBlueprint;

class Blueprint extends BaseBlueprint
{

    /**
     * Allowed permissions values.
     *
     * Possible options:
     *   -1 => Deny (adds to array, but denies regardless of user's group).
     *    0 => Remove.
     *    1 => Add.
     *
     * @var array
     */
    protected $allowedPermissionsValues = array(-1, 0, 1);

    /**
     * The login attribute.
     *
     * @var string
     */
    protected static $loginAttribute = 'email';

    /**
     * @return mixed
     */
    protected function init()
    {
        $table = \Config::get('netinteractive.sentry.user_table');

        $this->setStorageName($table);
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';

        $config = \Config::get('netinteractive.sentry');
        $this->getRelationManager()
            ->belongsToMany('roles', $config['role']['model'],$config['user_role_pivot_table'], 'user__id', array('role__id') )
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
            'login' => array(
                'title' =>_('Login'),
                'type' => static::TYPE_STRING,
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules' => array(
                    'update' => 'required',
                    'insert' => 'required|unique:user'
                )
            ),
            'email'=>array(
                'title' =>_('E-mail'),
                'type' => static::TYPE_STRING,
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules' => array(
                    'update' => 'required|email',
                    'insert' => 'required|email|unique:user'
                )
            ),
            'password'=>array(
                'title' => _('Password'),
                'type'=> static::TYPE_PASSWORD,
                'hashable' => true,
                'rules' => array(
                    'insert'=>'required'
                )
            ),

            'first_name' => array(
                'title' => _('First name'),
                'type' => static::TYPE_STRING,
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'last_name' => array(
                'title'=> _('Last name'),
                'type'=>static::TYPE_STRING,
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'permissions' => array(
                'title' => _('Permissions'),
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
            'activated' => array(
                'title' => _('Is active'),
                'type' => static::TYPE_BOOL,
                'sortable' => true,
                'rules' => array(
                    'any' => 'boolean'
                ),
                'filters' => array(
                    'display' => array('bool'),
                )
            ),
            'activation_code' => array(
                'title' => _('Activation code'),
                'type' => static::TYPE_STRING
            ),
            'activated_at' => array(
                'title' => _('Activation date'),
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
            'last_login' => array(
                'title' => _('Last login date'),
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
            'persist_code' => array(
                'title' => _('Persist code'),
                'hashable' => true,
                'type' => static::TYPE_STRING
            ),
            'reset_password_code' => array(
                'title' => _('Password reset code'),
                'type' => static::TYPE_STRING
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

    /**
     * Returns the name for the user's login.
     *
     * @return string
     */
    public function getLoginName()
    {
        return static::$loginAttribute;
    }

    /**
     * Returns the name for the user's password.
     *
     * @return string
     */
    public function getPasswordName()
    {
        return 'password';
    }


} 