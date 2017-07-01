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
function printSuccess($type)
{
    echo "Successfully created an object of type (" . $type . ")" . PHP_EOL;
}
function printError($type, $information, $status, $description)
{
    echo "Could not create an object of type (" . $type . "). Reason (" . $information . ")";
    if (isset($status) && isset($description)) {
        echo "nikita returned status (" . $status . "), description (" . $description . ")";
    }
    echo PHP_EOL;
}
function processFondsCreator($controller, $arkiv, $token)
{
    global $xml;
    $xml->startElement('arkivskaper');
    $urlarkivSkaperData = $arkivstrukturController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_ARKIVSKAPER);
    $arkivSkaperDataController = new NikitaEntityController($token);
    $arkivSkaperData = $arkivSkaperDataController->getData($urlarkivSkaperData);
    printSuccess("arkivskaper");
    $xml->startElement('arkivskaperID');
    $xml->text($arkiv['arkivskaperID']);
    $xml->endElement();
    $xml->startElement('arkivskaperNavn');
    $xml->text($arkiv['arkivskaperNavn']);
    $xml->endElement();
    $xml->startElement('beskrivelse');
    $xml->text($arkiv['beskrivelse']);
    $xml->endElement();
    $xml->endElement();
}
function processFolder($controller, $arkiv, $token) {
    global $xml;
    $urlmappeData = $arkivDelDataController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_MAPPE);
    $mappeController = new NikitaEntityController($token);
    $mappeData = $mappeController->getData($urlmappeData);
    // FIXME: NULL output
    $xml->startElement('mappe');
    $xml->WriteAttribute('xsi:type', 'saksmappe');
    $xml->startElement('systemID');
    $xml->text($arkiv['systemID']);
    $xml->endElement();
    $xml->startElement('mappeID');
    $xml->text($arkiv['mappeID']);
    $xml->endElement();
    $xml->startElement('tittel');
    $xml->text($arkiv['tittel']);
    $xml->endElement();
    $xml->startElement('beskrivelse');
    $xml->text($arkiv['beskrivelse']);
    $xml->endElement();
    $xml->startElement('opprettetDato');
    $xml->text($arkiv['opprettetDato']);
    $xml->endElement();
    $xml->startElement('opprettetAv');
    $xml->text($arkiv['opprettetAv']);
    $xml->endElement();
    $xml->startElement('avsluttetDato');
    $xml->text($arkiv['avsluttetDato']);
    $xml->endElement();
    $xml->startElement('avsluttetAv');
    $xml->text($arkiv['avsluttetAv']);
    $xml->endElement();
    $xml->endElement();
}
function processAllSeries($controller, $arkiv, $token)
{
    processSeries($controller, $arkiv, $token);
}
function processSeries($controller, $arkiv, $token)
{
    global $xml;
    $xml->startElement('arkivdel');
    $xml->startElement('systemID');
    $xml->text($arkiv['systemID']);
    $xml->endElement();
    $xml->startElement('tittel');
    $xml->text($arkiv['tittel']);
    $xml->text('tittel');
    $xml->endElement();
    $xml->startElement('beskrivelse');
    $xml->text($arkiv['beskrivelse']);
    $xml->endElement();
    $xml->startElement('arkivdelstatus');
    $xml->text($arkiv['arkivdelstatus']);
    $xml->endElement();
    $xml->startElement('dokumentmedium');
    $xml->text($arkiv['dokumentmedium']);
    $xml->endElement();
    $xml->startElement('opprettetDato');
    $xml->text($arkiv['opprettetDato']);
    $xml->endElement();
    $xml->startElement('opprettetAv');
    $xml->text($arkiv['opprettetAv']);
    $xml->endElement();
    $xml->startElement('avsluttetDato');
    $xml->text($arkiv['avsluttetDato']);
    $xml->endElement();
    $xml->startElement('avsluttetAv');
    $xml->text($arkiv['avsluttetAv']);
    $xml->endElement();
}
function processRegistration($controller, $arkiv, $token)
{
    global $xml;
    $urlregistreringData = $mappeController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_REGISTRERING);
    $registreringController = new NikitaEntityController($token);
    $registreringData = $registreringController->getData($urlregistreringData);
    $xml->startElement('registrering');
    foreach($registreringData["results"] as $registreringResults) {
        $xml->startElement('registrering');
        $xml->WriteAttribute('xsi:type', 'journalpost');
        $xml->startElement('systemID');
        $xml->text($registreringResults['systemID']);
        $xml->endElement();
        $xml->startElement('opprettetDato');
        $xml->text($registreringResults['opprettetDato']);
        $xml->endElement();
        $xml->startElement('opprettetAv');
        $xml->text($registreringResults['opprettetAv']);
        $xml->endElement();
        $xml->startElement('arkivertDato');
        $xml->text($registreringResults['arkivertDato']);
        $xml->endElement();
        $xml->startElement('arkivertAv');
        $xml->text($registreringResults['arkivertAv']);
        $xml->endElement();
        $urldokumentbeskrivelseData = $registreringController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_REGISTRERING);
        $dokumentbeskrivelseController = new NikitaEntityController($token);
        $dokumentbeskrivelseData = $dokumentbeskrivelseController->getData($urldokumentbeskrivelseData);
        $xml->startElement('dokumentbeskrivelse');
        foreach($dokumentbeskrivelseData["results"] as $dokumentbeskrivelseResult) {
            $xml->startElement('systemID');
            $xml->text($dokumentbeskrivelseResults['systemID']);
            $xml->endElement();
            $xml->startElement('dokumenttype');
            $xml->text($dokumentbeskrivelseResults['dokumenttype']);
            $xml->endElement();
            $xml->startElement('dokumentstatus');
            $xml->text($dokumentbeskrivelseResults['dokumentstatus']);
            $xml->endElement();
            $xml->startElement('tittel');
            $xml->text($dokumentbeskrivelseResults['tittel']);
            $xml->endElement();
            $xml->startElement('beskrivelse');
            $xml->text($dokumentbeskrivelseResults['beskrivelse']);
            $xml->endElement();
            $xml->startElement('forfatter');
            $xml->text($dokumentbeskrivelseResults['forfatter']);
            $xml->endElement();
            $xml->startElement('opprettetDato');
            $xml->text($dokumentbeskrivelseResults['opprettetDato']);
            $xml->endElement();
            $xml->startElement('opprettetAv');
            $xml->text($dokumentbeskrivelseResults['opprettetAv']);
            $xml->endElement();
            $xml->startElement('dokumentmedium');
            $xml->text($dokumentbeskrivelseResults['dokumentmedium']);
            $xml->endElement();
            $xml->startElement('tilknyttetRegistreringSom');
            $xml->text($dokumentbeskrivelseResults['tilknyttetRegistreringSom']);
            $xml->endElement();
            $xml->startElement('dokumentnummer');
            $xml->text($dokumentbeskrivelseResults['dokumentnummer']);
            $xml->endElement();
            $urldokumentobjektData = $dokumentbeskrivelseController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_REGISTRERING);
            $dokumentobjektController = new NikitaEntityController($token);
            $dokumentobjektData = $dokumentobjektController->getData($urldokumentobjektData);
            $xml->startElement('dokumentobjekt');
            foreach($dokumentobjektData["results"] as $dokumentobjektResult) {
                $xml->startElement('versjonsnummer');
                $xml->text($dokumentobjektResults['versjonsnummer']);
                $xml->endElement();
                $xml->startElement('variantformat');
                $xml->text($dokumentobjektResults['variantformat']);
                $xml->endElement();
                $xml->startElement('format');
                $xml->text($dokumentobjektResults['format']);
                $xml->endElement();
                $xml->startElement('opprettetDato');
                $xml->text($dokumentobjektResults['opprettetDato']);
                $xml->endElement();
                $xml->startElement('opprettetAv');
                $xml->text($dokumentobjektResults['opprettetAv']);
                $xml->endElement();
                $xml->startElement('referanseDokumentfil');
                $xml->text($dokumentobjektResults['referanseDokumentfil']);
                $xml->endElement();
                $xml->startElement('sjekksum');
                $xml->text($dokumentobjektResults['sjekksum']);
                $xml->endElement();
                $xml->startElement('sjekksumAlgoritme');
                $xml->text($dokumentobjektResults['sjekksumAlgoritme']);
                $xml->endElement();
                $xml->startElement('filstoerrelse');
                $xml->text($dokumentobjektResults['filstoerrelse']);
                $xml->endElement();
            }
            $xml->endElement();
        }
        $xml->endElement();
    }
    $xml->endElement();
}
function processFonds($controller,$arkiv,$token)
{
    $urlArkivstruktur = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR);
    $arkivstrukturController = new NikitaEntityController($token);
    $arkivstrukturData = $arkivstrukturController->getData($urlArkivstruktur);
    $urlarkivData = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_ARKIV);
    $arkivController = new NikitaEntityController($token);
    $arkivData = $arkivController->getData($urlarkivData);
    var_dump($arkivstrukturData);
    var_dump($arkivData);
    if ($arkivstrukturController->getData($urlArkivstruktur) == true) {
        printSuccess("arkiv");
        $urlarkivData = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR);
        $arkivDataController = new NikitaEntityController($token);
        $arkivData = $arkivDataController->getData($urlarkivData);
        if (isset($arkivstrukturData->arkivskaper)) {
            processFondsCreator($arkivDataController, $arkivData, $token);
        }
        if (isset($arkivstrukturData->arkivdel)) {
            processAllSeries($arkivDataController, $arkivData, $token);
        }
        if (isset($arkivstrukturData->registrering)) {
            processRegistration($arkivDataController, $arkivData, $token);
        }
        if (isset($arkivstrukturData->arkiv)) {
            processFonds($arkivDataController, $arkivData, $token);
        }
    }
    var_dump($arkivData);
}
if ($argc > 4) {
    $xml = new XMLWriter();
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
        exit(1);
    }
    echo "Successfully logged onto nikita. Token is " . $token . "\n";
    $applicationController = new NikitaEntityController($token);
    $applicationData = $applicationController->getData($baseurl);
    $xml->startDocument('1.0','UTF-8');
    $xml->startElement('arkiv');
    $xml->writeAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
    $xml->writeAttribute('xmlns','http://www.arkivverket.no/standarder/noark5/arkivstruktur');
    $xml->writeAttribute('xmlns:n5mdk','http://www.arkivverket.no/standarder/noark5/metadatakatalog');
    $xml->writeAttribute('xsi:schemaLocation','http://www.arkivverket.no/standarder/noark5/arkivstruktur arkivstruktur.xsd');

    $urlArkivstruktur = $applicationController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR);
    $arkivstrukturController = new NikitaEntityController($token);
    $arkivstrukturData = $arkivstrukturController->getData($urlArkivstruktur);

    $urlArkiv = $arkivstrukturController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_ARKIV);
    $arkivController = new NikitaEntityController($token);
    $arkivData = $arkivController->getData($urlArkiv);
    
    $memxml = new XMLWriter();
    $memxml->openMemory();
    $memxml->setIndent(true);
    
    foreach($arkivData['results'] as $arkivResults) {
        $memxml->startElement('systemID');
        $memxml->text($arkivResults["systemID"]);
        $memxml->endElement();
        $memxml->startElement('tittel');
        $memxml->text($arkivResults["tittel"]);
        $memxml->endElement();
        $memxml->startElement('beskrivelse');
        $memxml->text($arkivResults["beskrivelse"]);
        $memxml->endElement();
        $memxml->startElement('arkivstatus');
        $memxml->text($arkivResults["arkivstatus"]);
        $memxml->endElement();
        $memxml->startElement('dokumentmedium');
        $memxml->text($arkivResults["dokumentmedium"]);
        $memxml->endElement();
        $memxml->startElement('opprettetDato');
        $memxml->text($arkivResults["opprettetDato"]);
        $memxml->endElement();
        $memxml->startElement('opprettetAv');
        $memxml->text($arkivResults["opprettetAv"]);
        $memxml->endElement();
        $memxml->startElement('avsluttetDato');
        $memxml->text($arkivResults["avsluttetDato"]);
        $memxml->endElement();
        $memxml->startElement('avsluttetAv');
        $memxml->text($arkivResults["avsluttetAv"]);
        $memxml->endElement();
    }
    $xmlstr = $memxml->outputMemory(true);
    $memxml->flush();
    unset($memxml);
    $xml->writeRaw($xmlstr);
    $xml->endElement();
    $xml->endDocument();
    exit(0);
} else {
    echo "noark5-export.php FILE BASEURL USER PASS\n";
    exit(0);
}
?>
