Netinteractive\Sentry
=====================

Package to work with User and related data.


## Services
*  Netinteractive\Sentry\SentryServiceProvider - registers in App most important classes:
     * sentry.auth.manager - class that allows to work with authentication drivers.
     * sentry.user - user provider that allows to do basic stuff user related
     * sentry.role - role provider
     * sentry.throttle - throttle provider
     * sentry.social - soccial profile provider
     * sentry.session - session object used by sentry package
     * sentry.cookie - cookie object used by sentry package
     * sentry - main sentry class, that provides interface to work with user, roles, throttle etc.
   
## Commands:
    * ni:makeAdmin - creates admin account based on config credentials

## Changelog

* 5.0.27
    * updated: RoleProvider::findAll() now orders result by role name

* 5.0.25 - 5.0.26:
    * updated: redefined protection levels for fields

* 5.0.24:
    * removed from compose.json gettext package require

* 5.0.23:
    * fixed: artisan make command
    
* 5.0.22:
    * changed: models fields protection level definitions

* 5.0.17 - 5.0.21
    * fixed: role and user field definitions.

* 5.0.14 - 5.0.16
    * fixed: migration config data access wrong namespace bug.
    * update: changed migration names.

* 5.0.13
    * fixed: Netinteractive\Sentry\Role\Elegant\Blueprint has timestamps=true

* 5.0.12
    * fixed: composer.json double entries.

* 5.0.6 - 5.0.11
    * fixed: Config usage bug and config merge bug.

* 5.0.5
    * fixed: Config usage bug. Package were using config instead of published one.
    
* 5.0.4:
    * implemented \Netinteractive\Elegant\Model\Provider in all providers.

* 5.0.3:
    * added: Netinteractive\Sentry\Commands\MakeAdmin - it registers ni:makeAdmin command that created admin account based on config data.

* 5.0.2 : 
    * added: \Netinteractive\Sentry\Role\Elegant\Blueprint::getAllowedPermissionsValues() .
    * added: \Netinteractive\Sentry\Role\Elegant:Provider::setPermissionsAttribute(array $permissions, $overwrite=false).
    
* 5.0.1 : 
    * deleted: schema folder.
    
* 5.0.0 : init


### Support

We offer support through [our help forums](http://help.cartalyst.com), on [IRC at #cartalyst](http://webchat.freenode.net/?channels=cartalyst) and through GitHub issues (bugs only).

If you like Sentry, consider [subscribing](http://www.cartalyst.com/pricing) to our [Arsenal](http://www.cartalyst.com/arsenal). It allows us to keep creating awesome software and afford to eat at night. Subscribers also get **priority support** with all of our packages, both free and subscriber-only.

