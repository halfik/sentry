<?php namespace Netinteractive\Sentry\User\Elegant;


class Record extends \Netinteractive\Elegant\Model\Record
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }
} 