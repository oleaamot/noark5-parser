#!/usr/bin/php
<?php

require_once "controller/LoginController.php";
require_once "controller/NikitaEntityController.php";
require_once "controller/NoarkObjectCreator.php";
// MIT License
//
// Copyright (c) 2017  Ole Aamot Software
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to
// deal in the Software without restriction, including without limitation the
// rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
// sell copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
// DEALINGS IN THE SOFTWARE.


$xml = new XMLReader();
if ($argc > 4) {
    $xml->open($argv[1]);
    $baseurl = $argv[2];
    $user = $argv[3];
    $pass = $argv[4];
} else {
    echo "noark5-parser.php FILE BASEURL USER PASS\n";
    exit(0);
}
$dom = new DOMDocument;
$data = array("username" => $user, "password" => $pass);
$data_string = json_encode($data);

$loginController = new  LoginController($baseurl);
$token = $loginController->login($user, $pass);

if (!isset($token)) {
    echo "Could not login into nikita ... exiting";
    exit;
}

echo "Successfully logged onto nikita. Token is " . $token;

$applicationController = new NikitaEntityController($token);
$applicationData = $applicationController->getData($baseurl);

$urlArkivstruktur = $applicationController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR);

$arkivstrukturController = new NikitaEntityController($token);
$arkivstrukturData = $arkivstrukturController->getData($urlArkivstruktur);

$urlCreateFonds = $arkivstrukturController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_ARKIV);

$arkivController = new NikitaEntityController($token);

$noarkObjectCreator = new NoarkObjectCreator();

while ($xml->read() && $xml->name !== 'arkiv') ;
while ($xml->name === 'arkiv') {
    $node = simplexml_import_dom($dom->importNode($xml->expand(), true));
    // now you can use $node without going insane about parsing

    if ($arkivController->postData($urlCreateFonds, $noarkObjectCreator->createArkiv($node)) === true) {

        // Get the address to post to
        $urlCreateFondsCreator = $arkivController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_ARKIVSKAPER);
        // Create a controller
        $arkivskaperController = new NikitaEntityController($token);
        // upload the arkivskaper data
        if ($arkivskaperController->postData($urlCreateFondsCreator, $noarkObjectCreator->createArkivskaper($node->arkivskaper)) === true) {
        }

        //Get the link to post the data to
        $urlCreateSeries = $arkivController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_ARKIVDEL);

        //Create the controller
        $arkivdelController = new NikitaEntityController($token);

        // post the data
        if ($arkivdelController->postData($urlCreateSeries, $noarkObjectCreator->createArkivdel($node->arkivdel)) === true) {

            $urlCreateMappe = $arkivdelController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_MAPPE);
            // FIXME: mappe xsi:type="saksmappe"
            $mappe_items = $node->arkivdel->mappe->count();
            printf("DEBUG mappe count %d\n", $mappe_items);
            for ($mappeitem = 0; $mappeitem < $mappe_items; $mappeitem++) {
                print ("iteration num [" . $mappeitem . "] " . $node->arkivdel->mappe[$mappeitem]->systemID . "\n");
                // Create a controller for mappe
                $mappeController = new NikitaEntityController($token);

                if ($mappeController->postData($urlCreateMappe, $noarkObjectCreator->createMappe($node->arkivdel->mappe[$mappeitem])) === true) {
                    $registrering_items = $node->arkivdel->mappe[$mappeitem]->registrering->count();

                    $urlCreateRegistrering = $mappeController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_REGISTRERING);
                    for ($registreringitem = 0; $registreringitem < $registrering_items; $registreringitem++) {

                        // Create a controller for mappe
                        $registreringController = new NikitaEntityController($token);

                        if (true === $registreringController->postData($urlCreateRegistrering,
                                $noarkObjectCreator->createRegistrering($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]))
                        ) {

                            $dokumentBeskrivelse_items = $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->count();

                            $urlCreateDokumentBeskrivelse = $registreringController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_DOKUMENT_BESKRIVELSE);
                            for ($dokumentBeskrivelseitem = 0; $dokumentBeskrivelseitem < $dokumentBeskrivelse_items; $dokumentBeskrivelseitem++) {

                                // Create a controller for mappe
                                $dokumentBeskrivelseController = new NikitaEntityController($token);

                                if (true === $dokumentBeskrivelseController->postData($urlCreateDokumentBeskrivelse,
                                        $noarkObjectCreator->createDokumentBeskrivelse($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse[$dokumentBeskrivelseitem]))
                                ) {

                                    // Get the address to post to
                                    $urlCreateDokumentObjekt = $dokumentBeskrivelseController->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_DOKUMENT_OBJEKT);
                                    // Create a controller
                                    $dokumentObjektController = new NikitaEntityController($token);
                                    // upload the arkivskaper data
                                    if ($dokumentObjektController->postData($urlCreateDokumentObjekt, $noarkObjectCreator->createDokumentObjekt(
                                            $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse[$dokumentBeskrivelseitem]->dokumentobjekt
                                        )) === true) {
                                        echo "Hi again! \n";
                                    }
                                    else {
                                        printError("dokumentobjekt", Constants::COULD_NOT_POST,
                                            $dokumentObjektController->getStatusLastCall(),
                                            $dokumentObjektController->getDescriptionLastCall());
                                    }


                                } else {
                                    printError("dokumentBeskrivelse", Constants::COULD_NOT_POST,
                                        $dokumentBeskrivelseController->getStatusLastCall(),
                                        $dokumentBeskrivelseController->getDescriptionLastCall());
                                }
                            }

                        } else {
                            printError("registrering", Constants::COULD_NOT_POST,
                                $registreringController->getStatusLastCall(),
                                $registreringController->getDescriptionLastCall());
                        }
                    }
                } else {
                    printError("mappe", Constants::COULD_NOT_POST, $mappeController->getStatusLastCall(),
                        $mappeController->getDescriptionLastCall());
                }
            }
        } else {
            printError("arkivdel", Constants::COULD_NOT_POST, $arkivdelController->getStatusLastCall(),
                $arkivdelController->getDescriptionLastCall());
        }
    } else {
        printError("arkiv", Constants::COULD_NOT_POST, $arkivController->getStatusLastCall(),
            $arkivController->getDescriptionLastCall());
    }


// Only works to here  ...
    exit;


// FIXME: mappe xsi:type="saksmappe"
    $mappe_items = $node->arkivdel->mappe->count();
    printf("DEBUG mappe count %d\n", $mappe_items);
    for ($mappeitem = 0; $mappeitem < $mappe_items; $mappeitem++) {
        print ("iteration num [" . $mappeitem . "] " . $node->arkivdel->mappe[$mappeitem]->systemID . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->mappeID . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->tittel . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->beskrivelse . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->opprettetDato . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->avsluttetDato . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->opprettetAv . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->avsluttetAv . "\n");
        $mappe = "{ \"mappeID\": \"" . $node->arkivdel->mappe[$mappeitem]->mappeID . "\", \"tittel\": \"" . $node->arkivdel->mappe[$mappeitem]->tittel . "\", \"beskrivelse\": \"" . $node->arkivdel->mappe[$mappeitem]->beskrivelse . "\", \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetAv . "\", \"avsluttetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->avsluttetAv . "\", \"avsluttetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->avsluttetDato . "\"}";
        $mapperesult = result($baseurl, $token, $mappe, "hateoas-api/arkivstruktur/arkivdel/" . $arkivdeldata->systemID . "/ny-mappe");
        $mappedata = json_decode($mapperesult);
        // FIXME: registrering xsi:type="journalpost"
        $registrering_items = $node->arkivdel->mappe[$mappeitem]->registrering->count();
        for ($registreringitem = 0; $registreringitem < $registrering_items; $registreringitem++) {
            $registrering = "{ \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetAv . "\", \"arkivertDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->arkivertDato . "\", \"arkivertAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->arkivertAv . "\"}";
            print ("\"registreringsID\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->registreringsID . "\", \"tittel\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->tittel . "\", \"offentligTittel\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->offentligTittel . "\", \"forfatter\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->forfatter . "\", \"journalaar\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalaar . "\", \"journalsekvensnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalsekvensnummer . "\", \"journalpostnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalpostnummer . "\", \"journalposttype\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalposttype . "\", \"journalstatus\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalstatus . "\", \"journaldato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journaldato . "\", \"dokumentetsDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentetsDato . "\", \"mottattDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->mottattDato . "\", \"antallVedlegg\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->antallVedlegg . "\", \"journalEnhet\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalEnhet . "\"}");
            $registreringresult = result($baseurl, $token, $registrering, "hateoas-api/arkivstruktur/mappe/" . $mappedata->systemID . "/ny-registrering");
            $registreringdata = json_decode($registreringresult);

            $dokumentobjektdata = json_decode($dokumentobjektresult);
            print ("DEBUG0\n");
            print_r($dokumentobjektdata);
            print ("\nDEBUG0\n");
            $journalpost = result($baseurl, $token, $dokumentobjekt, "hateoas-api/sakarkiv/journalpost/ny-journalpost");
            $journalpostdata = json_decode($journalpost);
            print ("DEBUG1\n");
            print_r($journalpostdata);
            print ("\nDEBUG1\n");
            $saksmappe = result($baseurl, $token, $dokumentobjekt, "hateoas-api/sakarkiv/saksmappe/ny-saksmappe");
            $saksmappedata = json_decode($saksmappe);
            print ("DEBUG2\n");
            print_r($saksmappedata);
            print ("\nDEBUG2\n");
            $kp_items = $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->count();
            print "DEBUG kp_items : " . $kp_items . "\n";
            for ($kpitem = 0; $kpitem < $kp_items; $kpitem++) {
                $korrespondansepart = "{ \"korrespondanseparttype\" : { \"kode\" : \"EA\" }, \"navn\" : \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->korrespondansepartNavn . "\", \"postadresse\": { \"adresselinje1\" : \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->postadresse . "\", \"postnr\" : \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->postnummer . "\"}, \"kontaktinformasjon\" : { \"epostadresse\" : \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->epostadresse . "\"}}";
                print("\nDEBUG3\n");
                print("korrespondansepart=");
                print($korrespondansepart);
                print("\nDEBUG3\n");
                $korrespondansepartresult = result($baseurl, $token, $korrespondansepart, "hateoas-api/sakarkiv/journalpost/" . $journalpostdata->systemID . "/ny-korrespondansepartperson");
                $korrespondansepartdata = json_decode($korrespondansepartresult);
                print("DEBUG4\n");
                print_r($korrespondansepartdata);
                print("\nDEBUG4\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->korrespondanseparttype . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->korrespondansepartNavn . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->postadresse . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->postnummer . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->epostadresse . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->telefonnummer . "\n");
                // print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->kontaktperson . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->administrativEnhet . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->saksbehandler . "\n");
            }
            /* // FIXME: mappe xsi:type="saksmappe" */
            // FIXME: registrering xsi:type="journalpost"
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->systemID . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->opprettetDato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->opprettetAv . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->arkivertDato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->arkivertAv . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->systemID . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumenttype . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentstatus . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tittel . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->beskrivelse . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->forfatter . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->opprettetDato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->opprettetAv . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentmedium . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetRegistreringSom . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentnummer . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetDato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetAv . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->versjonsnummer . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->variantformat . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->format . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->opprettetDato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->opprettetAv . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->sjekksum . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->sjekksumAlgoritme . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->filstoerrelse . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->registreringsID . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->tittel . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->offentligTittel . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->forfatter . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalaar . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalsekvensnummer . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalpostnummer . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalposttype . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalstatus . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journaldato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentetsDato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->mottattDato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->antallVedlegg . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalenhet . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->korrespondanseparttype . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->korrespondansepartNavn . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->postadresse . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->postnummer . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->epostadresse . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->telefonnummer . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->kontaktperson . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->administrativEnhet . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->saksbehandler . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->avskrivning->avskrivningsdato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->avskrivning->avskrevetAv . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->avskrivning->avskrivningsmaate . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->saksaar . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->sakssekvensnummer . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->saksdato . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->administrativEnhet . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->saksansvarlig . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->saksstatus . "\n");
        }
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseurl);
    curl_setopt($ch, CURLOPT_REFERER, $baseurl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'noark5-parser/0.1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/vnd.noark5-v4+json ',
            'Authorization: ' . $token,
            'Content-Type: application/vnd.noark5-v4+json')
    );
    curl_exec($ch);
    $page = curl_exec($ch);
    var_dump($page);
    $site = json_decode($page, true);
    $array = $site{'_links'};
    $size = sizeof($array);
    $item = 0;
    for ($item = 0; $item < $size; $item++) {
        echo($array[$item]['href'] . "\n");
        browse($token, $baseurl, $node, $array[$item]['href']);
    }
// go to next <arkiv>
    $xml->next('arkiv');
}
$data = create($baseurl, $token);
print_r($data);


function printError($type, $information, $status, $description)
{
    echo "Could not create an object of type (" . $type . "). Reason (" . $information . ")";
    if (isset($status) && isset($description)) {
        echo "nikita returned status (" . $status . "), description (" . $description . ")";
    }
    echo PHP_EOL;
}

?>
