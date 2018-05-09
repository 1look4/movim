<?php

namespace Moxl\Stanza;

class Vcard
{
    static function get($to)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $vcard = $dom->createElementNS('vcard-temp', 'vCard');
        $xml = \Moxl\API::iqWrapper($vcard, $to, 'get');
        \Moxl\API::request($xml);
    }

    static function set($to = false, $data)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $vcard = $dom->createElementNS('vcard-temp', 'vCard');

        if ($data->fn) $vcard->appendChild($dom->createElement('FN', $data->fn->value));
        if ($data->name) $vcard->appendChild($dom->createElement('NICKNAME', $data->name->value));
        if ($data->url) $vcard->appendChild($dom->createElement('URL', $data->url->value));
        if ($data->date) $vcard->appendChild($dom->createElement('BDAY', $data->date->value));

        if ($data->email) {
            $email = $dom->createElement('EMAIL');
            $email->appendChild($dom->createElement('USERID', $data->email->value));
            $vcard->appendChild($email);
        }

        if ($data->country || $data->locality || $data->postalcode) {
            $adr = $dom->createElement('ADR');
            $adr->appendChild($dom->createElement('LOCALITY', $data->locality->value));
            $adr->appendChild($dom->createElement('PCODE', $data->postalcode->value));
            $adr->appendChild($dom->createElement('CTRY', $data->country->value));
            $vcard->appendChild($adr);
        }

        if ($data->desc) $vcard->appendChild($dom->createElement('DESC', $data->desc->value));

        if ($data->photobin && $data->phototype) {
            $photo = $dom->createElement('PHOTO');
            $photo->appendChild($dom->createElement('TYPE', $data->phototype->value));
            $photo->appendChild($dom->createElement('BINVAL', $data->photobin->value));
            $vcard->appendChild($photo);
        }

        $xml = \Moxl\API::iqWrapper($vcard, $to, 'set');
        \Moxl\API::request($xml);
    }
}
