#!/usr/bin/php
<?php
// MIT License
//
// Copyright (c) 2017 Ole K Aamot
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
if ($argc > 5) {
    $xml->open($argv[1]);
    $host = $argv[2];
    $port = $argv[3];
    $user = $argv[4];
    $pass = $argv[5];
} else {
    echo "noark-parser.php FILE HOST PORT USER PASS\n";
    exit(0);
}
$dom = new DOMDocument;
$data = array("username" => $user, "password" => $pass);
$data_string = json_encode($data);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . $host . ":" . $port . "/noark5v4/auth");
curl_setopt($ch, CURLOPT_REFERER, "http://" . $host . ":" . $port);
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
function browse($token, $host, $port, $node, $href) {
    print "Parsing " . $href . "\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $href);
    curl_setopt($ch, CURLOPT_REFERER, "http://" . $host . ":" . $port . "/");
    curl_setopt($ch, CURLOPT_USERAGENT, 'noark5-parser/0.1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $node->dataset->description);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.noark5-v4+json ',
        'Authorization: ' . $token,
        'Content-Type: application/vnd.noark5-v4+json',
        'Content-Length: ' . strlen($node->dataset->description))
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
        browse($token, $host, $port, $node, $array[$item]['href']);
    }
}
while ($xml->read()) {
    $node = simplexml_import_dom($dom->importNode($xml->expand(), true));
    // now you can use $node without going insane about parsing
    print ($node->dataset->description . "\n");
    // var_dump($node->dataset);
    // parser($token, $node, $host . ":" . $port . "/noark5v4/hateoas-api/arkivstruktur/ny-arkiv");
    // print_r($node);
    print ($node->systemID . "\n");
    print ($node->tittel . "\n");
    print ($node->beskrivelse . "\n");
    print ($node->arkivstatus . "\n");
    print ($node->dokumentmedium . "\n");
    print ($node->opprettetDato . "\n");
    print ($node->opprettetAv . "\n");
    print ($node->opprettetDato . "\n");
    print ($node->avsluttetDato . "\n");
    print ($node->arkivskaper->arkivskaperID . "\n");
    print ($node->arkivskaper->arkivskaperNavn . "\n");
    print ($node->arkivskaper->beskrivelse . "\n");
    print ($node->arkivdel->systemID . "\n");
    print ($node->arkivdel->tittel . "\n");
    print ($node->arkivdel->beskrivelse . "\n");
    print ($node->arkivdel->arkivdelstatus . "\n");
    print ($node->arkivdel->dokumentmedium . "\n");
    print ($node->arkivdel->opprettetDato . "\n");
    print ($node->arkivdel->avsluttetAv . "\n");
    // FIXME: mappe xsi:type="saksmappe"
    print ($node->arkivdel->mappe[0]->systemID . "\n");
    print ($node->arkivdel->mappe[0]->mappeID . "\n");
    print ($node->arkivdel->mappe[0]->tittel . "\n");
    print ($node->arkivdel->mappe[0]->beskrivelse . "\n");
    print ($node->arkivdel->mappe[0]->opprettetDato . "\n");
    print ($node->arkivdel->mappe[0]->avsluttetDato . "\n");
    // FIXME: registrering xsi:type="journalpost"
    print ($node->arkivdel->mappe[0]->registrering[0]->systemID . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->opprettetDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->opprettetAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->arkivertDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->arkivertAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->systemID . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumenttype . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentstatus . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->tittel . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->beskrivelse . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->forfatter . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->opprettetDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->opprettetAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentmedium . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->tilknyttetRegistreringSom . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->tilknyttetDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->tilknyttetAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->versjonsnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->variantformat . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->format . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->opprettetDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->opprettetAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->sjekksum . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->sjekksumAlgoritme . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->filstoerrelse . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->registreringsID . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->tittel . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->offentligTittel . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->forfatter . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->journalaar . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->journalsekvensnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->journalpostnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->journalposttype . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->journalstatus . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->journaldato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->dokumentetsDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->mottattDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->antallVedlegg . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->journalenhet . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->korrespondanseparttype . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->korrespondansepartNavn . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->postadresse . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->postnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->epostadresse . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->telefonnummer . "\n");
    // print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->kontaktperson . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->administrativEnhet . "\n");
    print ($node->arkivdel->mappe[0]->registrering[0]->korrespondansepart->saksbehandler . "\n");
    // print ($node->arkivdel->mappe[0]->registrering[0]->avskrivning->avskrivningsdato . "\n");
    // print ($node->arkivdel->mappe[0]->registrering[0]->avskrivning->avskrevetAv . "\n");
    // print ($node->arkivdel->mappe[0]->registrering[0]->avskrivning->avskrivningsmaate . "\n");
    // FIXME: registrering xsi:type="journalpost"
    print ($node->arkivdel->mappe[0]->registrering[1]->systemID . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->opprettetDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->opprettetAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->arkivertDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->arkivertAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->systemID . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumenttype . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentstatus . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->tittel . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->beskrivelse . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->forfatter . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->opprettetDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->opprettetAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentmedium . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->tilknyttetRegistreringSom . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->tilknyttetDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->tilknyttetAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->versjonsnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->variantformat . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->format . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->opprettetDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->opprettetAv . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->sjekksum . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->sjekksumAlgoritme . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->filstoerrelse . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->registreringsID . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->tittel . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->offentligTittel . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->forfatter . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->journalaar . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->journalsekvensnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->journalpostnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->journalposttype . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->journalstatus . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->journaldato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->dokumentetsDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->sendtDato . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->antallVedlegg . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->journalenhet . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->korrespondanseparttype . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->korrespondansepartNavn . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->postadresse . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->postnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->epostadresse . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->telefonnummer . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->kontaktperson . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->administrativEnhet . "\n");
    print ($node->arkivdel->mappe[0]->registrering[1]->korrespondansepart->saksbehandler . "\n");
    print ($node->arkivdel->mappe[0]->saksaar . "\n");
    print ($node->arkivdel->mappe[0]->sakssekvensnummer . "\n");
    print ($node->arkivdel->mappe[0]->saksdato . "\n");
    print ($node->arkivdel->mappe[0]->administrativEnhet . "\n");
    print ($node->arkivdel->mappe[0]->saksansvarlig . "\n");
    print ($node->arkivdel->mappe[0]->saksstatus . "\n");
    // FIXME: mappe xsi:type="saksmappe"
    print ($node->arkivdel->mappe[1]->systemID . "\n");
    print ($node->arkivdel->mappe[1]->mappeID . "\n");
    print ($node->arkivdel->mappe[1]->tittel . "\n");
    print ($node->arkivdel->mappe[1]->beskrivelse . "\n");
    print ($node->arkivdel->mappe[1]->opprettetDato . "\n");
    print ($node->arkivdel->mappe[1]->opprettetAv . "\n");
    print ($node->arkivdel->mappe[1]->avsluttetDato . "\n");
    print ($node->arkivdel->mappe[1]->avsluttetAv . "\n");
    // FIXME: registrering xsi:type="journalpost"
    print ($node->arkivdel->mappe[1]->registrering[0]->systemID . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->opprettetDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->opprettetAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->arkivertDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->arkivertAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->systemID . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumenttype . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentstatus . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->tittel . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->beskrivelse . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->forfatter . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->opprettetDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->opprettetAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentmedium . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->tilknyttetRegistreringSom . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->tilknyttetDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->tilknyttetAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->versjonsnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->variantformat . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->format . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->opprettetDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->opprettetAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->sjekksum . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->sjekksumAlgoritme . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentbeskrivelse->dokumentobjekt->filstoerrelse . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->registreringsID . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->tittel . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->offentligTittel . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->forfatter . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->journalaar . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->journalsekvensnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->journalpostnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->journalposttype . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->journalstatus . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->journaldato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->dokumentetsDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->mottattDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->antallVedlegg . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->journalenhet . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->korrespondanseparttype . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->korrespondansepartNavn . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->postadresse . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->postnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->epostadresse . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->telefonnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->kontaktperson . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->administrativEnhet . "\n");
    print ($node->arkivdel->mappe[1]->registrering[0]->korrespondansepart->saksbehandler . "\n");
    // print ($node->arkivdel->mappe[1]->registrering[0]->avskrivning->avskrivningsdato . "\n");
    // print ($node->arkivdel->mappe[1]->registrering[0]->avskrivning->avskrevetAv . "\n");
    // print ($node->arkivdel->mappe[1]->registrering[0]->avskrivning->avskrivningsmaate . "\n");
    // FIXME: registrering xsi:type="journalpost"
    print ($node->arkivdel->mappe[1]->registrering[1]->systemID . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->opprettetDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->opprettetAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->arkivertDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->arkivertAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->systemID . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumenttype . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentstatus . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->tittel . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->beskrivelse . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->forfatter . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->opprettetDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->opprettetAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentmedium . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->tilknyttetRegistreringSom . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->tilknyttetDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->tilknyttetAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->versjonsnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->variantformat . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->format . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->opprettetDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->opprettetAv . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->referanseDokumentfil . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->sjekksum . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->sjekksumAlgoritme . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentbeskrivelse->dokumentobjekt->filstoerrelse . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->registreringsID . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->tittel . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->offentligTittel . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->forfatter . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->journalaar . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->journalsekvensnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->journalpostnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->journalposttype . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->journalstatus . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->journaldato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->dokumentetsDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->sendtDato . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->antallVedlegg . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->journalenhet . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->korrespondanseparttype . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->korrespondansepartNavn . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->postadresse . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->postnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->epostadresse . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->telefonnummer . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->kontaktperson . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->administrativEnhet . "\n");
    print ($node->arkivdel->mappe[1]->registrering[1]->korrespondansepart->saksbehandler . "\n");
    print ($node->arkivdel->mappe[1]->saksaar . "\n");
    print ($node->arkivdel->mappe[1]->sakssekvensnummer . "\n");
    print ($node->arkivdel->mappe[1]->saksdato . "\n");
    print ($node->arkivdel->mappe[1]->administrativEnhet . "\n");
    print ($node->arkivdel->mappe[1]->saksansvarlig . "\n");
    print ($node->arkivdel->mappe[1]->saksstatus . "\n");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://" . $host . ":" . $port . "/noark5v4/");
    curl_setopt($ch, CURLOPT_REFERER, "http://" . $host . ":" . $port);
    curl_setopt($ch, CURLOPT_USERAGENT, 'noark5-parser/0.1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $node->dataset->description);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.noark5-v4+json ',
        'Authorization: ' . $token,
        'Content-Type: application/vnd.noark5-v4+json',
        'Content-Length: ' . strlen($node->dataset->description))
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
        browse($token, $host, $port, $node, $array[$item]['href']);
    }
    // go to next <dataset>
    $xml->next('dataset');
}
?>