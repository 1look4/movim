<?php

namespace Moxl\Stanza;

class Avatar {
    static function get($to)
    {
        $xml = '
            <pubsub xmlns="http://jabber.org/protocol/pubsub">
                <items node="urn:xmpp:avatar:data"/>
            </pubsub>';
        $xml = \Moxl\API::iqWrapper($xml, $to, 'get');
        \Moxl\API::request($xml);
    }

    static function set($data)
    {
        $xml = '
            <pubsub xmlns="http://jabber.org/protocol/pubsub">
                <publish node="urn:xmpp:avatar:data">
                    <item id="'.sha1($data).'">
                        <data xmlns="urn:xmpp:avatar:data">'.$data.'</data>
                    </item>
                </publish>
            </pubsub>
        ';
            
        $xml = \Moxl\API::iqWrapper($xml, false, 'set');
        \Moxl\API::request($xml);
    }

    static function setMetadata($data)
    {
        $xml = '
            <pubsub xmlns="http://jabber.org/protocol/pubsub">
                <publish node="urn:xmpp:avatar:metadata">
                    <item id="'.sha1($data).'">
                        <metadata xmlns="urn:xmpp:avatar:metadata">
                          <info bytes="'.strlen(base64_decode($data)).'"
                                height="410"
                                id="'.sha1($data).'"
                                type="image/jpeg"
                                width="410"/>
                        </metadata>
                    </item>
                </publish>
            </pubsub>
        ';
            
        $xml = \Moxl\API::iqWrapper($xml, false, 'set');
        \Moxl\API::request($xml);
    }
}
