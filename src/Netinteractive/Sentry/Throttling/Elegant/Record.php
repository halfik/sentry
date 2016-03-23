<?php namespace Netinteractive\Sentry\Throttling\Elegant;


class Record extends \Netinteractive\Elegant\Model\Record
{
    /**
     * @return $this
     */
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }
}