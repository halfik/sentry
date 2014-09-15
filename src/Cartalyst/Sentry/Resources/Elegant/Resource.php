<?php namespace Cartalyst\Sentry\Resources\Elegant;


use Cartalyst\Sentry\Resources\ResourceInterface;
use Cartalyst\Sentry\Elegant AS Elegant;

class Resource extends Elegant implements ResourceInterface {

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'resources';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = array();

    public $childrens = array();


    /**
     * Returns the group's ID.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getKey();
    }

    /**
     * Returns the resouce name.
     *
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * Return resource value
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * return parent id
     * @return int
     */
    public function getParentId(){
        return $this->parent_id;
    }

    /**
     * Retuurns informations if resourse has subresources
     * @return bool
     */
    public function hasChilds(){
        if (empty($this->childrens)){
            return false;
        }
        return true;
    }

    /**
     * returns array of subresources values
     * @return array
     */
    public function getChildsValues(){
        $subresourcesValues = array();

        foreach ($this->childrens AS $subresource){
            $subresourcesValues[] = $subresource->getValue();
        }

        return $subresourcesValues;
    }

}
