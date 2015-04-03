<?php

namespace Moxl\Stanza;

class Message {
    static function message($to, $content)
    {
        $session = \Sessionx::start();
        $xml = '
            <message xmlns="jabber:client" to="'.str_replace(' ', '\40', $to).'" type="chat" id="'.$session->id.'">
                <body>'.$content.'</body>
                <active xmlns="http://jabber.org/protocol/chatstates"/>
                <request xmlns="urn:xmpp:receipts"/>
            </message>';

        \Moxl\API::request($xml);
    }

    static function encrypted($to, $content)
    {
        $session = \Sessionx::start();
        $xml = '
            <message xmlns="jabber:client" to="'.str_replace(' ', '\40', $to).'" type="chat" id="'.$session->id.'">
                <body>You receive an encrypted message</body>
                <x xmlns="jabber:x:encrypted">
                    '.$content.'
                </x>
                <active xmlns="http://jabber.org/protocol/chatstates"/>
                <request xmlns="urn:xmpp:receipts"/>
            </message>';
        \Moxl\API::request($xml);
    }

    static function composing($to)
    {
        $session = \Sessionx::start();
        $xml = '
            <message xmlns="jabber:client" to="'.str_replace(' ', '\40', $to).'" type="chat" id="'.$session->id.'">
                <composing xmlns="http://jabber.org/protocol/chatstates"/>
            </message>';
        \Moxl\API::request($xml);
    }

    static function paused($to)
    {
        $session = \Sessionx::start();
        $xml = '
            <message xmlns="jabber:client" to="'.str_replace(' ', '\40', $to).'" type="chat" id="'.$session->id.'">
                <paused xmlns="http://jabber.org/protocol/chatstates"/>
            </message>';
        \Moxl\API::request($xml);
    }

    static function receipt($to, $id)
    {
        $session = \Sessionx::start();
        $xml = '
        <message xmlns="jabber:client" id="'.$session->id.'" to="'.str_replace(' ', '\40', $to).'">
            <received xmlns="urn:xmpp:receipts" id="'.$id.'"/>
        </message>';
        \Moxl\API::request($xml);
    }
}
