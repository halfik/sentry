<?php namespace Netinteractive\Sentry\Commands;

use \Illuminate\Console\Command;
use Netinteractive\Sentry\SentryServiceProvider;
use Netinteractive\Sentry\User\UserNotFoundException;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Input\InputArgument;

class MakeAdmin extends Command
{


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ni:makeAdmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(

        );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $roleProvider = \App::make('sentry')->getRoleProvider();
        $userProvider = \App::make('sentry')->getUserProvider();
        $data = config('packages.netinteractive.sentry.config.admin');

        if (!$data){
            return;
        }

        #we create admin account only if it dosn't exists
        try{
            $userProvider->findByEmail($data['email']);
        }
        catch(UserNotFoundException $e){
            $adminRole = $roleProvider->findByCode('admin');
            $userRecord = $userProvider->createRecord();

            #we have to prevent from triggering all events binded to user record on create
            \Event::listen('eloquent.created: '.get_class($userRecord), function($event){
                return false;
            },100);

            $user  = $userProvider->getEmptyUser();
            $user->disableValidation();
            $user->fill($data);

            $mapper = $userProvider->getMapper();
            $mapper->save($user);

            $user->addRole($adminRole);

            $userProvider->getMapper()->save($user, true);
        }
    }



    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(

        );
    }

}