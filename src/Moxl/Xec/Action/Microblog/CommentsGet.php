<?php
/*
 * CommentsGet.php
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

namespace Moxl\Xec\Action\Microblog;

use Moxl\Xec\Action;
use Moxl\Stanza\Microblog;

class CommentsGet extends Action
{
    private $_to;
    private $_id;
    
    public function request() 
    {
        $this->store();
        Microblog::commentsGet($this->_to, $this->_id);
    }
    
    public function setTo($to)
    {
        $this->_to = $to;
        return $this;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    public function handle($stanza, $parent = false) {
        $evt = new \Event();

        $node = (string)$stanza->pubsub->items->attributes()->node;
        list($xmlns, $parent) = explode("/", $node);

        if($stanza->pubsub->items->item) {
            $comments = array();

            foreach($stanza->pubsub->items->item as $item) {
                $p = new \modl\Postn();
                $p->set($item, $this->_to, false, $node);
                
                $pd = new \modl\PostnDAO();
                $pd->set($p);
                
                array_unshift($comments, $p);
            }

            $this->pack($this->_id);
            $this->deliver();
        } else {
            $evt->runEvent('nocomment', $parent);   
        }
    }
    
    public function error() {
        $this->pack($this->_id);
        $this->deliver();
    }

}
