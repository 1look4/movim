<?php
/*
 * ConfigurePersistentStorage.php - http://xmpp.org/extensions/xep-0223.html
 *
 * Copyright 2012 edhelas <edhelas@edhelas-laptop>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 *
 */

namespace Moxl\Xec\Action\Pubsub;

use Moxl\Xec\Action;
use Moxl\Stanza\Pubsub;

class ConfigurePersistentStorage extends Action
{
    private $_to;
    private $_node;
    private $_access_model;
    private $_max_items;

    public function request()
    {
        $this->store();
        Pubsub::configurePersistentStorage($this->_to, $this->_node, $this->_access_model, $this->_max_items);
    }

    public function setTo($to)
    {
        $this->_to = $to;
        return $this;
    }

    public function setNode($node)
    {
        $this->_node = $node;
        return $this;
    }

    public function setAccessPresence()
    {
        $this->_access_model = 'presence';
        return $this;
    }

    public function setMaxItems($max)
    {
        $this->_max_items = $max;
        return $this;
    }

    public function handle($stanza, $parent = false) {
        $this->pack($this->_node);
        $this->deliver();
    }

    public function errorFeatureNotImplemented($error) {
        $this->pack($this->_node);
        $this->deliver();
    }

    public function errorItemNotFound($error) {
        $this->pack($this->_node);
        $this->deliver();
    }

}
