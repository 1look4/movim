<?php
/*
 * @file SASL.php
 *
 * @brief Handle incoming SASL proposal
 *
 * Copyright 2014 edhelas <edhelas@edhelas-laptop>
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

namespace Moxl\Xec\Payload;

use Moxl\Xec\Action\Register\Get;

class SASL extends Payload
{
    public function handle($stanza, $parent = false)
    {
        $mec = (array)$stanza->mechanism;

        /*
         * Weird behaviour on old eJabberd servers, fixed on the new versions
         * see https://github.com/processone/ejabberd/commit/2d748115
         */
        if(isset($parent->starttls) && isset($parent->starttls->required))
            return;

        $session = \Session::start();
        $user = $session->get('username');

        if($user) {
            if(!is_array($mec)) {
                $mec = array($mec);
            }

            $mecchoice = str_replace('-', '', \Moxl\Auth::mechanismChoice($mec));

            $session->set('mecchoice', $mecchoice);

            \Moxl\Utils::log("/// MECANISM CHOICE ".$mecchoice);

            if(method_exists('\Moxl\Auth','mechanism'.$mecchoice)) {
                call_user_func('Moxl\Auth::mechanism'.$mecchoice);
            } else {
                \Moxl\Utils::log("/// MECANISM CHOICE NOT FOUND");
            }
        } else {
            $g = new Get;
            $g->setTo($session->get('host'))->request();
        }
    }
}
