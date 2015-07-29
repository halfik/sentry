<?php namespace Cartalyst\Sentry\SocialProfile\Elegant;


use Cartalyst\Sentry\BaseModel;
use Cartalyst\Sentry\Hashing\HasherInterface;

class SocialProfile extends BaseModel {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'social_profile';


	/**
	 * The hasher the model uses.
	 *
	 * @var \Cartalyst\Sentry\Hashing\HasherInterface
	 */
	protected static $hasher;


    /**
     * init fields and their rules
     */
    protected function init(){

        $fields=array(
            'id'=>array(
                'title'=>'Id',
                'type'=>'int',
                'sortable' => true,
                'rules'=>array(
                    'any'=>'integer',
                    'update'=>'required'
                )

            ),
            'user__id'=>array(
                'title'=>_('Użytkownik'),
                'type'=>'int',
                'sortable' => true,
                'rules'=>array(
                    'any'=>'required'
                )
            ),
            'profile__id'=>array(
                'title'=>_('Typ profilu'),
                'type'=>'text',
                'sortable' => true,
                'rules'=>array(
                    'any'=>'required'
                )
            ),
            'type'=>array(
                'title'=>_('Typ'),
                'type'=>'string',
                'sortable' => true,
                'rules'=>array(
                    'any'=>'required'
                )
            ),
        );

        $this->initFields($fields);
    }

}
