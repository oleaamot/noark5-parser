#!/usr/bin/php
<?php
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

function target($baseurl, $token) {
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
    curl_exec($ch);
    $page = curl_exec($ch);
    var_dump($page);
}

function upload($baseurl, $token, $data, $href) {
    print ("Uploading $data on $baseurl/$href with $token\n");
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
    curl_exec($ch);
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
while ($xml->read()) {

    $node = simplexml_import_dom($dom->importNode($xml->expand(), true));
    // now you can use $node without going insane about parsing
    var_dump($node);

    $data = json_encode($node);

    $arkiv = "{ \"tittel\": \"" . $node->tittel . "\", \"beskrivelse\":\"" .$node->beskrivelse . "\", \"arkivstatus\":\"" . $node->arkivstatus . "\", \"dokumentmedium\":\"" . $node->dokumentmedium . "\", \"opprettetAv\":\"" . $node->opprettetAv . "\", \"opprettetDato\":\"" . $node->opprettetDato . "\", \"avsluttetDato\":\"" . $node->avsluttetDato . "\"}";
    $arkivresult = upload($baseurl, $token, $arkiv, "hateoas-api/arkivstruktur/ny-arkiv");
    $arkivdata = json_decode($arkivresult);
    $arkivskaper = "{ \"arkivskaperID\": \"" . $node->arkivskaper->arkivskaperID . "\", \"arkivskaperNavn\": \"" . $node->arkivskaper->arkivskaperNavn . "\", \"beskrivelse\": \"" . $node->arkivskaper->beskrivelse . "\"}";
    $arkivskaperresult = upload($baseurl, $token, $arkivskaper, "hateoas-api/arkivstruktur/arkiv/" . $arkivdata->systemID . "/ny-arkivskaper");
    $arkivskaperdata = json_decode($arkivskaperresult);

    $arkivdel = "{ \"tittel\": \"" . $node->arkivdel->tittel . "\", \"beskrivelse\": \"" . $node->arkivdel->beskrivelse . "\", \"arkivdelstatus\": \"" . $node->arkivdel->arkivdelstatus . "\", \"dokumentmedium\": \"" . $node->arkivdel->dokumentmedium. "\", \"opprettetDato\": \"" . $node->arkivdel->opprettetDato . "\", \"avsluttetAv\": \"" . $node->arkivdel->avsluttetAv . "\"}";
    $arkivdelresult = upload($baseurl, $token, $arkivdel, "hateoas-api/arkivstruktur/arkiv/" . $arkivdata->systemID . "/ny-arkivdel");
    $arkivdeldata = json_decode($arkivdelresult);

    // FIXME: mappe xsi:type="saksmappe"
    $mappe_items = count($node->arkivdel->mappe);
    for ($mappeitem=0;$mappeitem<$mappe_items;$mappeitem++) {
        // print ("iteration num [" . $mappeitem . "] " . $node->arkivdel->mappe[$mappeitem]->systemID . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->mappeID . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->tittel . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->beskrivelse . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->opprettetDato . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->avsluttetDato . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->opprettetAv . "\n");
        print ($node->arkivdel->mappe[$mappeitem]->avsluttetAv . "\n");
        $mappe = "{ \"mappeID\": \"" . $node->arkivdel->mappe[$mappeitem]->mappeID . "\", \"tittel\": \"" . $node->arkivdel->mappe[$mappeitem]->tittel . "\", \"beskrivelse\": \"" . $node->arkivdel->mappe[$mappeitem]->beskrivelse . "\", \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetAv . "\", \"avsluttetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->avsluttetAv . "\", \"avsluttetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->avsluttetDato . "\"}";
        $mapperesult = upload($baseurl, $token, $mappe, "hateoas-api/arkivstruktur/arkivdel/" . $arkivdeldata->systemID . "/ny-mappe");
        $mappedata = json_decode($mapperesult);
        // FIXME: registrering xsi:type="journalpost"
        $registrering_items = count($node->arkivdel->mappe[$mappeitem]->registrering);
        for($registreringitem=0;$registreringitem<$registrering_items;$registreringitem++) {
            $registrering = "{ \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->opprettetAv . "\", \"arkivertDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->arkivertDato . "\", \"arkivertAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->arkivertAv . "\"}";
            print ("\"registreringsID\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->registreringsID . "\", \"tittel\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->tittel . "\", \"offentligTittel\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->offentligTittel . "\", \"forfatter\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->forfatter . "\", \"journalaar\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalaar . "\", \"journalsekvensnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalsekvensnummer . "\", \"journalpostnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalpostnummer . "\", \"journalposttype\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalposttype . "\", \"journalstatus\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalstatus . "\", \"journaldato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journaldato . "\", \"dokumentetsDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentetsDato . "\", \"mottattDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->mottattDato . "\", \"antallVedlegg\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->antallVedlegg . "\", \"journalEnhet\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->journalEnhet . "\"}");
            $registreringresult = upload($baseurl, $token, $registrering, "hateoas-api/arkivstruktur/mappe/" . $mappedata->systemID . "/ny-registrering");
            $registreringdata = json_decode($registreringresult);
            /* FIXME: Need to insert \"forfatter\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->forfatter . "\", in dokumentbeskrivelse */
            $dokumentbeskrivelse = "{ \"dokumenttype\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumenttype . "\", \"dokumentstatus\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tittel . "\", \"beskrivelse\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->beskrivelse . "\", \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->opprettetAv . "\", \"dokumentmedium\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentmedium . "\", \"tilknyttetRegistreringSom\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetRegistreringSom . "\", \"dokumentnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentnummer . "\", \"tilknyttetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetDato . "\", \"tilknyttetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->tilknyttetAv . "\"}";
            $dokumentbeskrivelseresult = upload($baseurl, $token, $dokumentbeskrivelse, "hateoas-api/arkivstruktur/registrering/" . $registreringdata->systemID . "/ny-dokumentbeskrivelse");
            $dokumentbeskrivelsedata = json_decode($dokumentbeskrivelseresult);
            $dokumentobjekt = "{ \"versjonsnummer\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->versjonsnummer . "\", \"variantformat\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->variantformat . "\", \"format\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->format . "\", \"opprettetDato\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->opprettetDato . "\", \"opprettetAv\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->opprettetAv . "\", \"referanseDokumentfil\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\", \"referanseDokumentfil\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\", \"sjekksum\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->sjekksum . "\", \"sjekksumAlgoritme\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->sjekksumAlgoritme . "\", \"filstoerrelse\": \"" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->dokumentbeskrivelse->dokumentobjekt->filstoerrelse . "\"}";
            $dokumentobjektresult = upload($baseurl, $token, $dokumentobjekt, "hateoas-api/arkivstruktur/registrering/" . $registreringdata->systemID . "/ny-dokumentobjekt");
            $dokumentobjektdata = json_decode($dokumentobjektresult);
            $korrespondansepart = "{ 'korrespondanseparttype' : { 'kode' : 'EA' }, 'navn' : '" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepartNavn . "', 'postadresse': { 'adresselinje1' : '" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->postadresse . "', 'postnr' : '" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->postnummer . "', } 'kontaktinformasjon' : { 'epostadresse' : '" . $node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->epostadresse . "'}, }";
            $korrespondansepartresult = upload($baseurl, $token, $korrespondansepart, "hateoas-api/arkivstruktur/registrering/" . $registreringdata->systemID . "/ny-korrespondansepartperson");
            $korrespondansepartdata = json_decode($korrespondansepartresult);
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->korrespondanseparttype . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->korrespondansepartNavn . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->postadresse . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->postnummer . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->epostadresse . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->telefonnummer . "\n");
            // print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->kontaktperson . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->administrativEnhet . "\n");
            print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart->saksbehandler . "\n");
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
    // go to next <arkivdel>
    $xml->next('arkivdel');
}
target($baseurl, $token);
?>
