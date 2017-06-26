<?php

class NoarkObjectCreator
{

    public function __construct()
    {
    }

    public function createArkiv($node)
    {
        // Temporarily, only pull out tittel and besrkivelse
        return "{ \"tittel\": \"" . $node->tittel .
            "\", \"beskrivelse\":\"" . $node->beskrivelse . "\"}";
    }

    public function createKlassifikasjonssystem($node)
    {
        return "{ \"tittel\": \"" . $node->tittel .
            "\", \"beskrivelse\":\"" . $node->beskrivelse . "\"}";
    }

    public function createClass($node)
    {
        return "{ \"tittel\": \"" . $node->tittel .
            "\", \"beskrivelse\":\"" . $node->beskrivelse . "\"}";
    }

    public function createArkivskaper($node)
    {
        return "{ \"arkivskaperID\": \"" . $node->arkivskaperID .
            "\", \"arkivskaperNavn\": \"" . $node->arkivskaperNavn .
            "\", \"beskrivelse\": \"" . $node->beskrivelse .
            "\"}";

    }

    public function createArkivdel($node)
    {
        // Temporarily, only pull out tittel and besrkivelse
        return "{ \"tittel\": \"" . $node->tittel .
            "\", \"beskrivelse\": \"" . $node->beskrivelse .
            "\", \"arkivdelstatus\": \"" . $node->arkivdelstatus .
            "\"}";
    }

    public function createMappe($node)
    {
        return "{ \"mappeID\": \"" . $node->mappeID .
            "\", \"tittel\": \"" . $node->tittel .
            "\", \"beskrivelse\": \"" . $node->beskrivelse .
            "\"}";
    }
    public function createSaksmappe($node)
    {
        return "{ \"mappeID\": \"" . $node->mappeID .
            "\", \"tittel\": \"" . $node->tittel .
            "\", \"beskrivelse\": \"" . $node->beskrivelse .
            "\", \"saksaar\": \"" . $node->saksaar .
            "\", \"sakssekvensnummer\": \"" . $node->sakssekvensnummer .
            "\", \"saksdato\": \"" . $node->saksdato .
            "\", \"administrativEnhet\": \"" . $node->administrativEnhet .
            "\", \"saksansvarlig\": \"" . $node-> saksansvarlig .
            "\", \"saksstatus\": \"" . $node->saksstatus .
            "\"}";
    }

    public function createRegistrering($node)
    {
        return "{}";

        /*
         *  None of these fields are settable on create
         * "{" .
             "\"arkivertDato\": \"" . $node->registreringarkivertDato .
            "\", \"arkivertAv\": \"" . $node->registreringarkivertAv .
            "\"}";
        */
    }

    public function createDokumentBeskrivelse($node)
    {
        return "{" .
            " \"dokumenttype\": \"" . $node->dokumenttype .
            "\", \"dokumentstatus\": \"" . $node->tittel .
            "\", \"beskrivelse\": \"" . $node->beskrivelse .
            "\", \"tittel\": \"" . $node->tittel .
            "\", \"tilknyttetDato\": \"" . $node->tilknyttetDato .
            "\", \"dokumentmedium\": \"" . $node->dokumentmedium .
            "\", \"tilknyttetRegistreringSom\": \"" . $node->tilknyttetRegistreringSom .
            "\", \"dokumentnummer\": \"" . $node->dokumentnummer .
            "\"}";
    }


    public function createDokumentObjekt($node)
    {
        return "{" .
            " \"versjonsnummer\": " . $node->versjonsnummer .
            ", \"variantformat\": \"" . trim($node->variantformat).
            "\", \"format\": \"" . $node->format .
            "\", \"referanseDokumentfil\": \"" . trim($node->referanseDokumentfil) .
            "\", \"sjekksum\": \"" . $node->sjekksum .
            "\", \"sjekksumAlgoritme\": \"" . $node->sjekksumAlgoritme .
            "\", \"filstoerrelse\": " . $node->filstoerrelse .
            "}";
    }

    public function createKorrespondansePart($node)
    {
        return "{" .
            "\"korrespondansepartnavn\": \"" . $node->korrespondansepartNavn .
            "\", \"postadresse\": \"" . $node->postadresse .
            "\", \"postnummer\": \"" . $node->postnummer .
            "\", \"epostadresse\": \"" . $node->epostadresse .
            "\", \"telefonnummer\": \"" . $node->telefonnummer .
            "\", \"kontaktperson\": \"" . $node->kontaktperson .
            "\", \"administrativEnhet\": \"" . $node->administrativEnhet .
            "\", \"saksbehandler\": \"" . $node->saksbehandler .
            "\"}";
    }
    /*     print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->korrespondanseparttype . "\n"); */
    /*             print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->korrespondansepartNavn . "\n"); */
    /*             print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->postadresse . "\n"); */
    /*             print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->postnummer . "\n"); */
    /*             print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->epostadresse . "\n"); */
    /*             print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->telefonnummer . "\n"); */
    /*             // print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->kontaktperson . "\n"); */
    /*             print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->administrativEnhet . "\n"); */
    /*             print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->saksbehandler . "\n"); */
    /* } */
}