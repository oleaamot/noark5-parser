#!/usr/bin/php
<?php

/* $id$
 *
 * noark5-parser
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

$xml = new XMLReader();

echo "hello";
xdebug_break();
echo "hello";


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
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseurl . "auth");
curl_setopt($ch, CURLOPT_REFERER, $baseurl);
curl_setopt($ch, CURLOPT_USERAGENT, 'noark5-parser/0.1');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);
curl_exec($ch);
$page = curl_exec($ch);
$data = json_decode($page);
$token = $data->{"token"};
function create($baseurl, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseurl . "hateoas-api/arkivstruktur/arkiv/");
    curl_setopt($ch, CURLOPT_REFERER, $baseurl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'noark5-parser/0.1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.noark5-v4+json ',
        'Authorization: ' . $token,
        'Content-Type: application/vnd.noark5-v4+json')
    );
    $page = curl_exec($ch);
    $data = json_decode($page);
    return $data;
}
function upload($baseurl, $token, $data, $href) {
    print ("Uploading $data on $baseurl$href with $token\n");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseurl . $href);
    curl_setopt($ch, CURLOPT_REFERER, $baseurl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'noark5-parser/0.1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.noark5-v4+json ',
        'Authorization: ' . $token,
        'Content-Type: application/vnd.noark5-v4+json')
    );
    $page = curl_exec($ch);
    var_dump($page);
    return $page;
}
function result($baseurl, $token, $data, $href) {
    print ("Uploading $data on $baseurl$href with $token\n");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseurl . $href);
    curl_setopt($ch, CURLOPT_REFERER, $baseurl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'noark5-parser/0.1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.noark5-v4+json ',
        'Authorization: ' . $token,
        'Content-Type: application/vnd.noark5-v4+json')
    );
    $page = curl_exec($ch);
    var_dump($page);
    return $page;
}
function browse($token, $baseurl, $node, $href) {
    print "Parsing " . $href . "\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $href);
    curl_setopt($ch, CURLOPT_REFERER, $baseurl);
    curl_setopt($ch, CURLOPT_USERAGENT, 'noark5-parser/0.1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.noark5-v4+json ',
        'Authorization: ' . $token,
        'Content-Type: application/vnd.noark5-v4+json',
        'Content-Length: ' . strlen($node))
    );
    curl_exec($ch);
    $page = curl_exec($ch);
    var_dump($page);
    $site = json_decode($page, true);
    $array = $site{'_links'};
    $size = sizeof($array);
    $item = 0;
    for ($item=0;$item<$size;$item++) {
        echo($array[$item]['href'] . "\n");
        // upload($baseurl, $node, $href);
        // if ($array[$item]['href'] == "hateoas-api/arkivstruktur/ny-arkiv") {
        //    print "ny-arkiv";
        // }
        // browse($token, $baseurl, $node, $array[$item]['href']);
    }
}


function getArkivskaperLinkCreate($arkivData) {

    $arkivdata = json_decode($arkivData);
    return "hello";
}

while ($xml->read() && $xml->name !== 'arkiv');
while ($xml->name === 'arkiv') {
    $node = simplexml_import_dom($dom->importNode($xml->expand(), true));
    // now you can use $node without going insane about parsing
    var_dump($node);
    $data = json_encode($node);
    if (isset($node->avsluttetDato)) {
        $arkiv = "{ \"tittel\": \"" . $node->tittel . "\", \"beskrivelse\":\"" .$node->beskrivelse . "\", \"arkivstatus\":\"" . $node->arkivstatus . "\", \"dokumentmedium\":\"" . $node->dokumentmedium . "\", \"opprettetAv\":\"" . $node->opprettetAv . "\", \"opprettetDato\":\"" . $node->opprettetDato . "\", \"avsluttetDato\":\"" . $node->avsluttetDato . "\"}"; } else {
        $arkiv = "{ \"tittel\": \"" . $node->tittel . "\", \"beskrivelse\":\"" .$node->beskrivelse . "\", \"arkivstatus\":\"" . $node->arkivstatus . "\", \"dokumentmedium\":\"" . $node->dokumentmedium . "\", \"opprettetAv\":\"" . $node->opprettetAv . "\", \"opprettetDato\":\"" . $node->opprettetDato . "\", \"avsluttetDato\":null }";
    }
    $arkivresult = result($baseurl, $token, $arkiv, "hateoas-api/arkivstruktur/ny-arkiv");
    $arkivdata = json_decode($arkivresult);
    $arkivskaper = "{ \"arkivskaperID\": \"" . $node->arkivskaper->arkivskaperID . "\", \"arkivskaperNavn\": \"" . $node->arkivskaper->arkivskaperNavn . "\", \"beskrivelse\": \"" . $node->arkivskaper->beskrivelse . "\"}";
    $arkivskaperresult = result($baseurl, $token, $arkivskaper, "hateoas-api/arkivstruktur/arkiv/" . $arkivdata->systemID . "/ny-arkivskaper");
    $arkivskaperdata = json_decode($arkivskaperresult);
    $arkivdel = "{ \"tittel\": \"" . $node->arkivdel->tittel . "\", \"beskrivelse\": \"" . $node->arkivdel->beskrivelse . "\", \"arkivdelstatus\": \"" . $node->arkivdel->arkivdelstatus . "\", \"dokumentmedium\": \"" . $node->arkivdel->dokumentmedium. "\", \"opprettetDato\": \"" . $node->arkivdel->opprettetDato . "\", \"avsluttetAv\": \"" . $node->arkivdel->avsluttetAv . "\"}";
    $arkivdelresult = result($baseurl, $token, $arkivdel, "hateoas-api/arkivstruktur/arkiv/" . $arkivdata->systemID . "/ny-arkivdel");
    $arkivdeldata = json_decode($arkivdelresult);
    // FIXME: mappe xsi:type="saksmappe"
    $mappe_items = $node->arkivdel->mappe->count();
    printf("DEBUG mappe count %d\n", $mappe_items);
    for ($mappeitem=0;$mappeitem<$mappe_items;$mappeitem++) {
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
        for($registreringitem=0;$registreringitem<$registrering_items;$registreringitem++) {
            $registrering = "{ \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetAv . "\", \"arkivertDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->arkivertDato . "\", \"arkivertAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->arkivertAv . "\"}";
            print ("\"registreringsID\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->registreringsID . "\", \"tittel\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->tittel . "\", \"offentligTittel\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->offentligTittel . "\", \"forfatter\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->forfatter . "\", \"journalaar\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalaar . "\", \"journalsekvensnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalsekvensnummer . "\", \"journalpostnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalpostnummer . "\", \"journalposttype\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalposttype . "\", \"journalstatus\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalstatus . "\", \"journaldato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journaldato . "\", \"dokumentetsDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentetsDato . "\", \"mottattDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->mottattDato . "\", \"antallVedlegg\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->antallVedlegg . "\", \"journalEnhet\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalEnhet . "\"}");
            $registreringresult = result($baseurl, $token, $registrering, "hateoas-api/arkivstruktur/mappe/" . $mappedata->systemID . "/ny-registrering");
            $registreringdata = json_decode($registreringresult);
            /* FIXME: Need to insert \"forfatter\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->forfatter . "\", in dokumentbeskrivelse */
            $dokumentbeskrivelse = "{ \"dokumenttype\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumenttype . "\", \"dokumentstatus\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tittel . "\", \"beskrivelse\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->beskrivelse . "\", \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->opprettetAv . "\", \"dokumentmedium\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentmedium . "\", \"tilknyttetRegistreringSom\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetRegistreringSom . "\", \"dokumentnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentnummer . "\", \"tilknyttetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetDato . "\", \"tilknyttetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetAv . "\"}";
            $dokumentbeskrivelseresult = result($baseurl, $token, $dokumentbeskrivelse, "hateoas-api/arkivstruktur/registrering/" . $registreringdata->systemID . "/ny-dokumentbeskrivelse");
            $dokumentbeskrivelsedata = json_decode($dokumentbeskrivelseresult);
            $dokumentobjekt = "{ \"versjonsnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->versjonsnummer . "\", \"variantformat\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->variantformat . "\", \"format\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->format . "\", \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->opprettetAv . "\", \"referanseDokumentfil\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\", \"referanseDokumentfil\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\", \"sjekksum\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->sjekksum . "\", \"sjekksumAlgoritme\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->sjekksumAlgoritme . "\", \"filstoerrelse\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->filstoerrelse . "\"}";
            $dokumentobjektresult = result($baseurl, $token, $dokumentobjekt, "hateoas-api/arkivstruktur/registrering/" . $registreringdata->systemID . "/ny-dokumentobjekt");
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
            for($kpitem=0;$kpitem<$kp_items;$kpitem++) {
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
    for ($item=0;$item<$size;$item++) {
        echo($array[$item]['href'] . "\n");
        browse($token, $baseurl, $node, $array[$item]['href']);
    }
    // go to next <arkiv>
    $xml->next('arkiv');
}
$data = create($baseurl, $token);
print_r($data);
?>
