#!/usr/bin/php
<?php

/* $id$
 *
 * noark5-export
 *
 * Copyright (C) 2017  Ole Aamot
 * Copyright (C) 2017  Thomas Sødring
 *
 * Authors: Ole Aamot <oka@oka.no>, Thomas Sødring <Thomas.Sodring@hioa.no>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once "controller/LoginController.php";
require_once "controller/NikitaEntityController.php";
require_once "controller/NoarkObjectCreator.php";

$xml = new XMLWriter();
if ($argc > 4) {
    $xml->openURI($argv[1]);
    $xml->setIndent(true);
    $baseurl = $argv[2];
    $user = $argv[3];
    $pass = $argv[4];
    $data = array("username" => $user, "password" => $pass);
    $data_string = json_encode($data);
    $loginController = new LoginController($baseurl);
    $token = $loginController->login($user, $pass);
    if (!isset($token)) {
        echo "Could not login into nikita ... exiting\n";
        exit;
    }
    echo "Successfully logged onto nikita. Token is " . $token . "\n";
    $applicationController = new NikitaEntityController($token);
    $applicationData = $applicationController->getData($baseurl);
    $urlArkivstruktur = $applicationController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR);
    $arkivstrukturController = new NikitaEntityController($token);
    $arkivstrukturData = $arkivstrukturController->getData($urlArkivstruktur);
    $xml->startDocument('1.0','UTF-8');
    $xml->startElement('arkiv');
    $xml->writeAttributeNS('xmlns', 'xsi', null, 'http://www.w3.org/2001/XMLSchema-instance');
    $xml->writeAttributeNS('xmlns', null, null, 'http://www.arkivverket.no/standarder/noark5/arkivstruktur');
    $xml->writeAttributeNS('xmlns', 'n5mdk', null, 'http://www.arkivverket.no/standarder/noark5/metadatakatalog');
    $xml->writeAttributeNS('xsi', 'schemaLocation', null, 'http://www.arkivverket.no/standarder/noark5/arkivstruktur arkivstruktur.xsd');
    $memxml = new XMLWriter();
    $memxml->openMemory();
    $memxml->setIndent(true);

    $memxml->startElement('systemID');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('tittel');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('beskrivelse');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('arkivstatus');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('dokumentmedium');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('opprettetDato');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('opprettetAv');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('avsluttetDato');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('avsluttetAv');
    $memxml->text('element');
    $memxml->endElement();

    $memxml->startElement('arkivskaper');
    $memxml->startElement('arkivskaperID');
    $memxml->text('arkivskaperID');
    $memxml->endElement();
    $memxml->startElement('arkivskaperNavn');
    $memxml->text('arkivskaperNavn');
    $memxml->endElement();
    $memxml->startElement('beskrivelse');
    $memxml->text('beskrivelse');
    $memxml->endElement();
    $memxml->endElement();

    $memxml->startElement('arkivdel');
    $memxml->text('beskrivelse');
    $memxml->endElement();

    $xmlstr = $memxml->outputMemory(true);
    $xml->writeRaw($xmlstr);

    $memxml->flush();
    unset($memxml);
    $xml->endElement();
    $xml->endDocument();
    var_dump($arkivstrukturData);
} else {
    echo "noark5-export.php FILE BASEURL USER PASS\n";
    exit(0);
}
?>
