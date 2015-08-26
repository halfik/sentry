Cartalyst\Sentry
=====================
# Sentry

@author: halfik

Sentry is a PHP 5.3+ fully-featured authentication & authorization system. It also provides additional features such as user groups and additional security features.

Sentry is a framework agnostic set of interfaces with default implementations, though you can substitute any implementations you see fit.

[![Build Status](https://travis-ci.org/cartalyst/sentry.png?branch=master)](https://travis-ci.org/cartalyst/sentry)

![Bitdeli](https://d2weczhvl823v0.cloudfront.net/cartalyst/sentry/trend.png)

# Changelog



## 3.11.1
    Usnnalem z modelu User z tablicy guarded pola: activation_code, reset_password_code, persist_code. 
    Wersja eleganta 1.7+, nie zapisuje na baze danych pol guarded.
    
## 3.11.0
    + event sentry.register przekazuje teraz 2 argumenty do funkcji bindujacych sie: obiekt uzytkownika oraz dane, ktore przyszly z inputa.

## 3.10.0
    + Social Facebook provider

## 3.9.1
    + poprawka bledu w metodzie User::hasRole

## 3.9.0
    + zmiana nazwy tabeli users na tabele user, wymaga to również zmiany w corze w modelu User.php

## 3.8.5
    + filtry acl dodaja komentarz do sql, ktory modyfikuja

## 3.8.4
    + dodalem metode findUsersByRole($roleCode) która służy do wyszukiwania użytkowników po roli

### 3.8.3
    + fix: poprawienie metody hasRole($roleCode) => dodanie sprawdzania czy uzytkownik posiada grupy

### 3.8.2
    + usunale z modelu User::toArray mapowanie activated na bool. do tego uzywamy mechanizmu filtrow display.

### 3.8.1
    + fix: blad z wersjonowaniem

## 3.8.0
    + User::toArray() zmieniony na User::toArray($displayFilters=false)

### 3.7.1
    + User dostal metode do sprawdzania, czy posiada dana role:  public function hasRole($roleCode): boolean

## 3.7.0
    + możliwość wielokrotnego logowania na to samo konto

### 3.6.1
    + fix wymagan co do paczki Netinteractive\Gettext

## 3.6
    + paczka powstala w zwiazku z nowa wersja Netinteractive\Elegant. Zaszly zmiany w polach modelu. Kompatybilne z Elegant 1.3>=

## 3.5
    + dodano pole "login" - not nullable


####### Features

It also provides additional features such as user groups and additional security features:

- Configurable authentication (can use any type of authentication required, such as username or email)
- ACL
- Authorization
- Activation of user *(optional)*
- Groups and group permissions
- "Remember me"
- User suspension
- Login throttling *(optional)*
- User banning
- Password resetting
- User data
- Interface driven - switch out your own implementations at will

### Installation

Installation of Sentry is very easy. We've got a number of guides to get Sentry working with your favorite framework or on it's own:

- [Install Sentry](https://cartalyst.com/manual/sentry#installation)

### Getting Started

- Use in [Laravel 4](https://cartalyst.com/manual/sentry#laravel-4)
- Use in [FuelPHP 1](https://cartalyst.com/manual/sentry#fuelphp-1.x)
- Use in [CodeIgniter 3](https://cartalyst.com/manual/sentry#codeigniter-3.0-dev)
- Use [natively (through composer)](https://cartalyst.com/manual/sentry#native)

### Upgrading

Currently, we do not have an upgrade method from Sentry 1, however we may be able to publish one before the stable release of Sentry 2.0. When upgrading between betas or release-candidates, please see [our changelog](https://github.com/cartalyst/sentry/blob/master/changelog.md).

### Support

We offer support through [our help forums](http://help.cartalyst.com), on [IRC at #cartalyst](http://webchat.freenode.net/?channels=cartalyst) and through GitHub issues (bugs only).

If you like Sentry, consider [subscribing](http://www.cartalyst.com/pricing) to our [Arsenal](http://www.cartalyst.com/arsenal). It allows us to keep creating awesome software and afford to eat at night. Subscribers also get **priority support** with all of our packages, both free and subscriber-only.

