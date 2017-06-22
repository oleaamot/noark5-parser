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
}