<?php

/**
 * @package Widgets
 *
 * @file Profile.php
 * This file is part of MOVIM.
 *
 * @brief The Profile widget
 *
 * @author Timothée	Jaussoin <edhelas_at_gmail_dot_com>
 *
 * @version 1.0
 * @date 20 October 2010
 *
 * Copyright (C)2010 MOVIM project
 *
 * See COPYING for licensing information.
 */

class Profile extends WidgetBase
{

    private static $status;

    function WidgetLoad()
    {
        $this->addcss('profile.css');
        $this->addjs('profile.js');
        $this->registerEvent('myvcard', 'onMyVcardReceived');
    }
    
    function onMyVcardReceived($vcard = false)
    {
		$html = $this->prepareVcard($vcard);
        RPC::call('movim_fill', 'profile', RPC::cdata($html));
    }
    
	function ajaxSetStatus($statustext, $status)
	{
		$xmpp = Jabber::getInstance();
		$xmpp->setStatus($statustext, $status);
	}
    
    function prepareVcard($vcard = false)
    {
    
        global $sdb;
        $user = new User();
        $me = $sdb->select('Contact', array('key' => $user->getLogin(), 'jid' => $user->getLogin()));
        
		$xmpp = Jabber::getInstance();
        $presence = PresenceHandler::getPresence($user->getLogin(), true, $xmpp->getResource());
        
        if(isset($me[0])) {
            $me = $me[0];
            $html = '
				<table>
					<tr>
						<td>
							<img src="'.$me->getPhoto().'">
						</td>
						<td>
							<h1>'.$me->getTrueName().'</h1>
						</td>
					</tr>
				</table>
				';
            $html .= '<input type="text" id="status" value="'.$presence['status'].'"><br />';
        }
        
        return $html;
    }
    
    function build()
    {
    ?>
    
        <div id="profile">
			<?php 
				echo $this->prepareVcard();
			?>
        </div>
    <?php
    }
}
