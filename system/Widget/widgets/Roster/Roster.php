<?php

/**
 * @package Widgets
 *
 * @file Roster.php
 * This file is part of MOVIM.
 *
 * @brief The Roster widget
 *
 * @author Jaussoin Timothée <edhelas@gmail.com>
 *
 * @version 1.0
 * @date 30 August 2010
 *
 * Copyright (C)2010 MOVIM project
 *
 * See COPYING for licensing information.
 */

class Roster extends WidgetBase
{

    function WidgetLoad()
    {
    	$this->addcss('roster.css');
    	$this->addjs('roster.js');
		$this->registerEvent('roster', 'onRoster');
		$this->registerEvent('presence', 'onPresence');
		$this->registerEvent('vcard', 'onVcard');
    }
    
	function onPresence($presence)
	{
	    $arr = $presence->getPresence();
	    $tab = PresenceHandler::getPresence($arr['jid'], true);
	    RPC::call('incomingPresence',
                      RPC::cdata($tab['jid']), RPC::cdata($tab['presence_txt']));
	}
	
    function onVcard($contact)
    {
        $html = $this->prepareRosterElement($contact, true);
        RPC::call('movim_fill', 'roster'.$contact->getData('jid'), RPC::cdata($html));
    }
	
    function onRoster($roster)
    {
		$html = $this->prepareRoster();
        RPC::call('movim_fill', 'rosterlist', RPC::cdata($html));
        RPC::call('sortRoster');
    }
    
	function ajaxRefreshRoster()
	{
		$this->xmpp->getRosterList();
	}
	
	function prepareRosterElement($contact, $inner = false)
	{
        $presence = PresenceHandler::getPresence($contact->getData('jid'), true);
        $start = 
            '<li 
                class="';
                    if(isset($presence['presence']))
                        $start .= ''.$presence['presence_txt'].' ';
                    else
                        $start .= 'offline ';
                        
                    if($contact->getData('jid') == $_GET['f'])
                        $start .= 'active ';
        $start .= '" 
                id="roster'.$contact->getData('jid').'" 
             >';
             
        $middle = '<div class="chat on" onclick="'.$this->genCallWidget("Chat","ajaxOpenTalk", "'".$contact->getData('jid')."'").'"></div>
                 <a 
					title="'.$contact->getTrueName().' ('.$contact->getData('jid').')" 
                    href="?q=friend&f='.$contact->getData('jid').'"
                 >
                    <img class="avatar"  src="'.$contact->getPhoto('xs').'" />'.
                    '<span>'.$contact->getTrueName();
						if($contact->getData('rosterask') == 'subscribe')
							$middle .= " #";
            $middle .= '</span>
                 </a>';
        $end = '</li>';

        if($inner == true)
            return $middle;
        else
            return $start.$middle.$end;
	}
	
	function prepareRoster()
	{
        $query = Contact::query()
                            ->where(array('key' => $this->user->getLogin()))
                            ->orderby('group', true);
        $contacts = Contact::run_query($query);
        
        $group = '';        
        $html = '';

        if($contacts != false) {
            foreach($contacts as $c) {
                
                if($group != $c->getData('group')) {
                    if($group != '')
                        $html .= '</div>';
                    
                    $group = $c->getData('group');
                    
                    if($group == '') {
                        $group = t('Ungrouped');
                        $html .= '<div><h1>'.$group.'</h1>';
                        $group = '';
                    }
                    else
                        $html .= '<div><h1>'.$group.'</h1>';                    
                }
                
                if($c->getData('rostersubscription') != 'none' || $c->getData('rosterask') == 'subscribe')
                    $html .= $this->prepareRosterElement($c);
            }
            $html .= '</div>';
            
            $html .= '<li class="more" onclick="showRoster(this);"><a href="#"><span>'.t('Show All').'</span></a></li>';
        } else {
            $html .= '<script type="text/javascript">setTimeout(\''.$this->genCallAjax('ajaxRefreshRoster').'\', 1500);</script>';
        }

        return $html;
	}
    
    function build()
    { 
    ?>
        <div id="roster">
            <ul id="rosterlist">
            <?php echo $this->prepareRoster(); ?>
            </ul>
            <div class="config_button" onclick="<?php $this->callAjax('ajaxRefreshRoster');?>"></div>
            <script type="text/javascript">sortRoster();</script>
        </div>
    <?php
    }
}

?>
