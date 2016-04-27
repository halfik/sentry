<?php namespace Netinteractive\Sentry\User\Elegant;

use Netinteractive\Elegant\Search\Searchable;
use Netinteractive\Elegant\Model\Blueprint AS BaseBlueprint;

class Blueprint extends BaseBlueprint
{
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
        $config = \Config::get('packages.netinteractive.sentry.config');
        $table = $config['user_table'];

        $this->setStorageName($table);
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';


        $this->getRelationManager()->belongsToMany(
            'roles',
            $config['role']['model'],$config['user_role_pivot_table'],
            'user__id',
            'role__id'
        );

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
                'type' => static::TYPE_EMAIL,
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules' => array(
                    'update' => 'required|email',
                    'insert' => 'required|email|unique:user'
                )
            ),
            'password'=>array(
                'title' => _('Hasło'),
                'type'=> static::TYPE_PASSWORD,
                'hashable' => true,
                'protected' => [
                    static::PROTECT_VIEW,
                ],
                'rules' => array(
                    'insert'=>'required'
                )
            ),

            'first_name' => array(
                'title' => _('Imię'),
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
                'title'=> _('Nazwisko'),
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
            'activated' => array(
                'title' => _('Czy aktywny?'),
                'type' => static::TYPE_BOOL,
                'searchable' => Searchable::$equal,
                'sortable' => true,
                'rules' => array(
                    'any' => 'boolean'
                ),
                'filters' => array(
                    'display' => array('bool'),
                )
            ),
            'activation_code' => array(
                'title' => _('Kod aktywacyjny'),
                'type' => static::TYPE_STRING,
                'protected' =>  [
                    static::PROTECT_CREATE,
                    static::PROTECT_VIEW,
                    static::PROTECT_UPDATE,
                    static::PROTECT_DELETE
                ],
            ),
            'activated_at' => array(
                'title' => _('Data aktywacji'),
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
            'last_login' => array(
                'title' => _('Data ostatniego logowania'),
                'type' => static::TYPE_DATETIME,
                'protected' =>  [
                    static::PROTECT_CREATE,
                    static::PROTECT_UPDATE,
                    static::PROTECT_DELETE
                ],
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
                'protected' =>  [
                    static::PROTECT_CREATE,
                    static::PROTECT_VIEW,
                    static::PROTECT_UPDATE,
                    static::PROTECT_DELETE
                ],
                'type' => static::TYPE_STRING
            ),
            'reset_password_code' => array(
                'title' => _('Kod resetu hasła'),
                'protected' =>  [
                    static::PROTECT_CREATE,
                    static::PROTECT_VIEW,
                    static::PROTECT_UPDATE,
                    static::PROTECT_DELETE
                ],
                'type' => static::TYPE_STRING
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
    public function getScopeObject(){
        return new Scope($this->getStorageName());
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