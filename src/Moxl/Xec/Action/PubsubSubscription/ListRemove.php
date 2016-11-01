<?php

namespace Moxl\Xec\Action\PubsubSubscription;

use Moxl\Xec\Action;
use Moxl\Xec\Action\Pubsub\Errors;
use Moxl\Stanza\PubsubSubscription;

class ListRemove extends Errors
{
    private $_to;
    private $_from;
    private $_node;

    public function request()
    {
        $this->store();
        PubsubSubscription::listRemove($this->_to, $this->_from, $this->_node);
    }

    public function setTo($to)
    {
        $this->_to = $to;
        return $this;
    }

    public function setFrom($from)
    {
        $this->_from = $from;
        return $this;
    }

    public function setNode($node)
    {
        $this->_node = $node;
        return $this;
    }

    public function handle($stanza, $parent = false)
    {
        if($stanza["type"] == "result"){
            $this->event('groupremoved', $this->_node);
        }
    }
}
