<?php
namespace Cartalyst\Sentry;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder AS QueryBuilder;
use Illuminate\Support\MessageBag AS MessageBag;
use Netinteractive\Utils\Utils AS Utils;

class Elegant extends Model{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var Object
     */
    protected $Error;
    protected $Validator;
    protected $validationEnabled = true;

    protected static $queryAllowAcl = true;



    public function __construct(array $attributes = array()){
        \Searchable::$alias = $this->getTable();
        $this->init();
        parent::__construct($attributes);
    }

    protected function init(){
    }


    /**
     * Find a model by its primary key.
     * Przeciazylismy i dodajemy nazwe tabeli z modelu przed nazwa pola
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Illuminate\Support\Collection|static
     */
    public static function find($id, $columns = array('*'))
    {
        foreach ($columns AS &$column){
            if (strpos('.', $column) == false){
                $column =  \App::make(get_called_class())->getTable().'.'.$column;
            }
        }

        return parent::find($id, $columns);
    }

    /**
     * Get all of the models from the database.
     * Przeciazylismy i dodajemy nazwe tabeli z modelu przed nazwa pola
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function all($columns = array('*')){
        foreach ($columns AS &$column){
            if (strpos('.', $column) == false){
                $column =  \App::make(get_called_class())->getTable().'.'.$column;
            }
        }

        return parent::all($columns);
    }

    /**
     * Zwraca obiekt walidatora
     * @return Illuminate\Support\Facades\Validator
     */
    public function Validator(){
        if(is_null($this->Validator)){
            $this->Validator=\Validator::make($this->attributes,$this->getFieldsRules());
        }
        return $this->Validator;
    }


    /**
     * Zwraca tablice z lista pol modelu wraz z informacjami o walidacji etc. etc.
     * @return array
     */
    public function getFields(){
        if (!$this->fields){
            $this->fields = array();
        }
        return $this->fields;
    }


    /**
     * Metoda pozwala kierowac odpaleniem eventu acl w query builderze
     * @param bool $allow
     */
    public static function allowQueryAcl($allow=true){
        self::$queryAllowAcl = $allow;
    }

    /**
     * Perform a model insert operation.
     * Przeciazylismy, aby dodac eventy after_create, z ktorych korzystaja np. userParams
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return bool|null
     */
    public function performInsert(\Illuminate\Database\Eloquent\Builder $query, array $options=array()){
        $this->validate('insert');

        $attributes = $this->attributes;
        $this->attributes = $this->getDirty();

        $result = parent::performInsert($query, $options);

        $this->attributes = array_merge($attributes, $this->attributes );
        $this->fireModelEvent('after_created', false);

        return $result;
    }

    /**
     * Perform a model update operation.
     * Przeciazylismy, aby dodac eventy after_updated, z ktorych korzystaja np. userParams
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return bool|null
     */
    protected function performUpdate(\Illuminate\Database\Eloquent\Builder $query, array $options=array()){
        $this->validate('update');
        $result = parent::performUpdate($query, $options);
        $this->fireModelEvent('after_updated', false);

        return $result;
    }

    /**
     * Validate model fields
     * @param string $rulesGroups
     * @throws ElegantValidationException
     * @return Elegant
     */
    public function validate($rulesGroups='all'){
        if (!$this->validationEnabled){
            return ;
        }
        $MessageBag=new MessageBag();
        $Validator=$this->Validator();
        $Validator->setData($this->attributes);

        $rules = $this->getFieldsRules($rulesGroups);
        foreach ($rules AS $field=>$val){
            if ($this->exists && !$this->isDirty($field)){
                unset($rules[$field]);
            }
        }

        $Validator->setRules($rules);

        if($Validator->fails()){
            $messages=$Validator->messages()->toArray();
            foreach($messages as $key=>$message){
                $MessageBag->add($key,$message);
            }
            $this->Error = new ElegantValidationException($MessageBag);
            throw $this->Error;
        }

        return $this;
    }

	/**
	 * add error into validation exception
	 * @param $key
	 * @param $message
     * @throws ElegantValidationException
     * @return Elegant
	 */
	public function addValidationError($key, $message){
		if(is_null($this->Error)){
			$MessageBag=new MessageBag();
			$this->Error = new ElegantValidationException($MessageBag);
		}
		else{
			$MessageBag=$this->Error->getMessageBag();
		}
		$MessageBag->add($key,$message);
        return $this;
	}

    /**
     * @param string $rulesGroups
     * @return bool
     */
    public function isValid($rulesGroups='all'){
        try{
            $this->validate($rulesGroups);
            return true;
        }catch (ElegantValidationException $e){
            return false;
        }
    }

	/**
	 * Walidacja przypisanych rekordów
	 * @param $key
	 */
	public function checkAttachedIds($key, $message='Brak powiązanych rekordów!'){
		if(!$this->exists && !$this->getAttribute($key)){
			throw new ElegantAttachException($message);
		}

		if(isset($this->attributes[$key])){
			$arr=explode(',',$this->attributes[$key]);
			if(count($arr)<1 || empty($arr[0])){
				throw new ElegantAttachException($message);
			}
		}
	}


    /**
     * Builds query alliases for fields
     * @param null $fields
     * @return array
     */
    public function makeFieldsAliases($fields=null){
        if(!$fields){
            $fields=array_keys($this->getFields());
        }
        $class=get_class($this);
        $result=[];
        foreach($fields as $field){
            $result[]=$class.'.'.$field.' AS '.$class.'_'.$field;
        }
        return $result;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $q
     * @param string $keyword
     * @param array $inFields
     * @return Elegant
     */
    public function makeLikeWhere(\Illuminate\Database\Eloquent\Builder &$q, $keyword, $inFields){
        $keyword = trim($keyword);
        $q->where(function(\Illuminate\Database\Eloquent\Builder $q)use($keyword, $inFields){
            foreach($inFields as $field){
                if ( isSet($this->fields[$field]['searchable'])){
                    $searchable = $this->fields[$field]['searchable'];
                    if($searchable instanceof \Closure){
                        $this->fields[$field]['searchable']($q,$keyword);
                    }
                }
            }
        });

        return $this;
    }

    /**
     * @param string $field
     * @param string $type
     * @param string $operator
     * @return Elegant
     */
    public function setFieldSearchable($field, $type, $operator='='){
        $this->fields[$field]['searchable'] = Searchable::$type($field, $operator);
        return $this;
    }

    /**
     * @param array $params
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function search(array $params=array()){
        $q=$this->newQuery();
        if(array_get($params,'fields')){
            $q->select($this->makeFieldsAliases($params['fields']));
            $q->from($this->getTable().' AS '.get_class($this));
        }
        return $q;
    }

    /**
     * return validation|save|delete last exception
     * @return /Exception
     */
    public function getError(){
        return $this->Error;
    }

    /**
     * @return bool|null
     * @throws ElegantDeletionException
     * @return mixed
     */
    public function delete(){
        if(!parent::delete()){
            $this->Error=new ElegantDeletionException("Can't delete record!");
            throw $this->Error;
        }
        return true;
    }

    /**
     * Zwraca pola po ktorych mozna sortowac
     * @return array
     */
    public function getSortableFields(){
        $fields = array();

        foreach ($this->fields AS $key=>$field){
            if (array_get($field,'sortable')){
                $fields[$key] = $field;
            }
        }

        return $fields;
    }

    /**
     * Zwraca pola, po ktorych mozna wyszukiwac
     * @return array
     */
    public function getSearchableFields(){
        $fields = array();

        foreach ($this->fields AS $key=>$field){
            if (array_get($field,'searchable')){
                $fields[$key] = $field;
            }
        }

        return $fields;
    }

    /**
     * return titles for fields
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsTitles($fieldsKeys=null){
        if(is_null($fieldsKeys)){
            $fieldsKeys=array_keys($this->getFields());
        }
        if(!is_array($fieldsKeys)){
            $fieldsKeys=array($fieldsKeys);
        }
        $result=array();
        $fields=$this->getFields();
        foreach($fields as $key=>$field){
            if(in_array($key,$fieldsKeys)){
                $result[$key]=$field['title'];
            }

        }
        return $result;
    }

    /**
     * zwraca label dla pola
     * @param string $field
     * @return mixed
     */
    public function getFieldTitle($field){
        if (!isSet($this->fields[$field]['title'])){
            return null;
        }

        return $this->fields[$field]['title'];
    }

    /**
     * return field validation rules
     * @param string $key
     * @return array
     */
    public function getFieldRules($key){
        if (isSet($this->fields[$key]['rules'])){
            return $this->fields[$key]['rules'];
        }
        return array();
    }

    /**
     * return field rules for selected group. default group is "any". always return at least any
     * @param string $rulesGroups
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsRules($rulesGroups='any', $fieldsKeys=null){
        $rulesGroups=Utils::paramToArray($rulesGroups);

        if(is_null($fieldsKeys)){
            $fieldsKeys=array_keys($this->getFields());
        }

        $fieldsKeys=Utils::paramToArray($fieldsKeys);

        if(!in_array('any',$rulesGroups)){
            array_push($rulesGroups,'any');
        }

        $result=array();
        $fields=$this->getFields();
        foreach($fields as $key=>$field){
            if(!in_array($key,$fieldsKeys) || !isSet($field['rules'])){
                continue;
            }

            $rules=$field['rules'];
            $result[$key]='';
            foreach($rulesGroups as $ruleGroup){
                if(in_array($ruleGroup,$rulesGroups)){
                    $result[$key].='|'.array_get($rules,$ruleGroup);
                }
            }
        }
        return $result;
    }

    /**
     * return field types for selected fields
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsTypes(array $fieldsKeys=array()){
        if(is_null($fieldsKeys)){
            $fieldsKeys=array_keys($this->getFields());
        }
        if(!is_array($fieldsKeys)){
            $fieldsKeys=array($fieldsKeys);
        }
        $result=array();
        $fields=$this->getFields();
        foreach($fields as $key=>$field){
            if(in_array($key,$fieldsKeys)){
                $result[$key]=$field['type'];
            }
        }
        return $result;
    }

    /**
     * zwraca informacje o typie pola
     * @param string $field
     * @return mixed
     */
    public function getFieldType($field){
        if (!isSet($this->fields[$field]['type'])){
            return null;
        }

        return $this->fields[$field]['type'];
    }

    /**
     * set up validation rules for selected field
     * @param string $field
     * @param string|array $rules
     * @param null|string $group
     * @return Elegant
     */
    public function setFieldRules($field, $rules, $group=null){
        if ($group === null){
            $this->fields[$field]['rules'] = $rules;
        }
        else{
            $this->fields[$field]['rules'][$group] = $rules;
        }
        return $this;
    }

    /**
     * enalbe/disable validation
     * @param bool $enalble
     * @return Elegant
     */
    public function setValidationEnabled($enalble=true){
        $this->validationEnabled = $enalble;
        return $this;
    }

    /**
     * return information if field is model field
     * @param string $field
     * @return bool
     */
    public function isOriginal($field){
        $fields = array_keys($this->getFields());

        return in_array($field, $fields);
    }


    /**
     * Aliast dla isOriginal
     * @param string $field
     * @return bool
     */
    public function isInFields($field){
        return $this->isOriginal($field);
    }

    /**
     * Get the attributes that have been changed since last sync.
     * Usuwamy z dirty pola, ktore nie pochodza z tego active recordu
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty =  parent::getDirty();

        foreach ($dirty as $field => $value)
        {
            if (!$this->isOriginal($field)){
                unset($dirty[$field]);
            }
            elseif($this->getFieldType($field) == 'password' && empty($dirty[$field])){
                unset($dirty[$field]);
            }
        }

        return $dirty;
    }

    /**
     * zwraca obiekt walidatora
     * @return mixed
     */
    public function getValidator(){
        return $this->Validator;
    }

    /**
     * Przeciazony fill - odpalamy nasz filtr acl sprawdzajacy prawo zapisu pol
     * @param array $attributes
     * @return mixed
     */
    public function fill(array $attributes){
        if(count($attributes)){
            $Obj=new \stdClass();
            $Obj->data=$attributes;
            $Obj->Record=$this;
            \Event::fire('acl.filter.model.fill', $Obj);
            $attributes=$Obj->data;
        }
        return parent::fill($attributes);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        $type = $this->getFieldType($key);
        /**
         * booleanow nie trimujemy, bo zamienia false na pusty string
         */
        if(is_scalar($value) && !is_bool($value)){
            $value = trim($value);
        }
        
        switch ($type){
            case 'integer':
                if (empty($value)){
                    $value = null;
                }
                break;
            case 'date':
            case 'dateTime':
                if (empty($value)){
                    $value = null;
                }
                break;
        }

        parent::setAttribute($key, $value);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();
        $grammar = $conn->getQueryGrammar();

        return \App::make('QueryBuilder', array($conn, $grammar, $conn->getPostProcessor()))->allowAclFilter(self::$queryAllowAcl);
    }


    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return \App::make('ModelBuilder', array($query));
    }
}

class ElegantDeletionException extends \Exception{

}

class ElegantAttachException extends \Exception{
	protected $message = "Brak powiązanych rekordów!";
}

class ElegantValidationException extends \Exception{
    protected $MessageBag;

    public function __construct(MessageBag $MessageBag = null, $message = "", $code = 0, Exception $previous = null){
        $this->MessageBag=$MessageBag;
        return parent::__construct($message, $code,$previous);
    }

    public function getMessageBag(){
        return $this->MessageBag;
    }
}