#!/usr/bin/php
<?php

require_once "controller/LoginController.php";
require_once "controller/NikitaEntityController.php";
require_once "controller/NoarkObjectCreator.php";

// Put  in the GPL license!

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


$noarkObjectCreator = new NoarkObjectCreator();

while ($xml->read() && $xml->name !== 'arkiv') ;
while ($xml->name === 'arkiv') {
    $root = simplexml_import_dom($dom->importNode($xml->expand(), true));
    processFonds($arkivstrukturController, $root, $token, $noarkObjectCreator);
    $xml->next('arkiv');
}

function processFonds($controller, $arkiv, $token, $noarkObjectCreator)
{

    $urlCreateFonds = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_ARKIV);
    $arkivController = new NikitaEntityController($token);
    if ($arkivController->postData($urlCreateFonds, $noarkObjectCreator->createArkiv($arkiv)) === true) {
        printSuccess("arkiv");
        if (isset($arkiv->arkivskaper)) {
            processFondsCreator($arkivController, $arkiv, $token, $noarkObjectCreator);
        }
        processAllSeries($arkivController, $arkiv, $token, $noarkObjectCreator);
    } else {
        printError("arkiv", Constants::COULD_NOT_POST, $arkivController->getStatusLastCall(),
            $arkivController->getDescriptionLastCall());
    }
}

function processFondsCreator($controller, $arkiv, $token, $noarkObjectCreator)
{
    $urlCreateFondsCreator = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_ARKIVSKAPER);
    $arkivskaperController = new NikitaEntityController($token);
    if ($arkivskaperController->postData($urlCreateFondsCreator, $noarkObjectCreator->createArkivskaper($arkiv->arkivskaper)) === true) {
        printSuccess("arkivskaper");
    }

}

function processAllSeries($controller, $arkiv, $token, $noarkObjectCreator)
{
    $arkivdel_items = $arkiv->arkivdel->count();
    for ($arkivdelitem = 0; $arkivdelitem < $arkivdel_items; $arkivdelitem++) {
        print ("arkivdel iteration num [" . $arkivdelitem . "] " . $arkiv->arkivdel[$arkivdelitem]->systemID . "\n");
        // Create a controller for arkivdel
        processSeries($controller, $arkiv->arkivdel[$arkivdelitem], $token, $noarkObjectCreator);
    }
}

function processSeries($controller, $arkivdel, $token, $noarkObjectCreator)
{
    $urlCreateSeries = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_ARKIVDEL);
    $arkivdelController = new NikitaEntityController($token);

    if ($arkivdelController->postData($urlCreateSeries, $noarkObjectCreator->createArkivdel($arkivdel)) === true) {

        printSuccess("arkivdel");
        if (isset($arkivdel->mappe)) {
            processAllFile($arkivdelController, $arkivdel, $token, $noarkObjectCreator);
        } else if (isset($arkivdel->klassifikasjonssystem)) {
            processAllClassificationSystem($arkivdelController, $arkivdel, $token, $noarkObjectCreator);
        } else if (isset($arkivdel->registrering)) {
            processAllRegistration($arkivdelController, $arkivdel, $token, $noarkObjectCreator);
        }
    } else {
        printError("arkivdel", Constants::COULD_NOT_POST, $arkivdelController->getStatusLastCall(),
            $arkivdelController->getDescriptionLastCall());
    }
}

function processAllClassificationSystem($controller, $arkivdel, $token, $noarkObjectCreator)
{
    $klassifikasjonssystem_items = $arkivdel->klassifikasjonssystem->count();
    for ($klassifikasjonssystemitem = 0; $klassifikasjonssystemitem < $klassifikasjonssystem_items;
         $klassifikasjonssystemitem++) {
        print ("klassifikasjonssystem iteration num [" . $klassifikasjonssystemitem . "] " .
            $arkivdel->klassifikasjonssystem[$klassifikasjonssystemitem]->systemID . "\n");
        // Create a controller for klassifikasjonssystem
        processClassificationSystem($controller, $arkivdel->klassifikasjonssystem[$klassifikasjonssystemitem],
            $token, $noarkObjectCreator);
    }
}

function processClassificationSystem($controller, $klassifikasjonssystem, $token, $noarkObjectCreator)
{
    $urlCreateClassificationSystem = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_KLASSIFIKASJONSSYSTEM);
    $klassifikasjonssystemController = new NikitaEntityController($token);

    if ($klassifikasjonssystemController->postData($urlCreateClassificationSystem,
            $noarkObjectCreator->createKlassifikasjonssystem($klassifikasjonssystem)) === true
    ) {
        printSuccess("klassifikasjonsystem");
        processAllClass($klassifikasjonssystemController, $klassifikasjonssystem, $token, $noarkObjectCreator);
    } else {
        printError("klassifikasjonssystem", Constants::COULD_NOT_POST, $klassifikasjonssystemController->getStatusLastCall(),
            $klassifikasjonssystemController->getDescriptionLastCall());
    }
}

function processAllClass($controller, $arkivdel, $token, $noarkObjectCreator)
{
    $klasse_items = $arkivdel->klasse->count();
    for ($klasseitem = 0; $klasseitem < $klasse_items;
         $klasseitem++) {
        print ("klasse iteration num [" . $klasseitem . "] " .
            $arkivdel->klasse[$klasseitem]->systemID . "\n");
        // Create a controller for klasse
        processClass($controller, $arkivdel->klasse[$klasseitem],
            $token, $noarkObjectCreator);
    }
}

function processClass($controller, $klasse, $token, $noarkObjectCreator)
{
    $urlCreateClass = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_KLASSE);
    $klasseController = new NikitaEntityController($token);

    if ($klasseController->postData($urlCreateClass,
            $noarkObjectCreator->createKlasse($klasse)) === true
    ) {
        printSuccess("");
        if (isset($klasse->mappe)) {
            processAllFiles($klasseController, $klasse, $token, $noarkObjectCreator);
        } else if (isset($klasse->klasse)) {
            processAllClass($klasseController, $klasse, $token, $noarkObjectCreator);
        }
        processAllFiles($klasseController, $klasse, $token, $noarkObjectCreator);
    } else {
        printError("klasse", Constants::COULD_NOT_POST, $klasseController->getStatusLastCall(),
            $klasseController->getDescriptionLastCall());
    }
}

function processAllFile($controller, $arkivdel, $token, $noarkObjectCreator)
{
    $mappe_items = $arkivdel->mappe->count();
    for ($mappeitem = 0; $mappeitem < $mappe_items; $mappeitem++) {
        print ("iteration num [" . $mappeitem . "] " . $arkivdel->mappe[$mappeitem]->systemID . "\n");
        // Create a controller for mappe
        processFile($controller, $arkivdel->mappe[$mappeitem], $token, $noarkObjectCreator);
    }
}

function processFile($controller, $mappe, $token, $noarkObjectCreator)
{
    $urlCreateMappe = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_MAPPE);
    $mappeController = new NikitaEntityController($token);
    if ($mappeController->postData($urlCreateMappe, $noarkObjectCreator->createMappe($mappe)) === true) {
        printSuccess("mappe");
        processAllRegistration($mappeController, $mappe, $token, $noarkObjectCreator);
    } else {
        printError("mappe", Constants::COULD_NOT_POST, $mappeController->getStatusLastCall(),
            $mappeController->getDescriptionLastCall());
    }
}

function processAllRegistration($controller, $mappe, $token, $noarkObjectCreator)
{
    $registrering_items = $registrering_items = $mappe->registrering->count();

    for ($registreringitem = 0; $registreringitem < $registrering_items; $registreringitem++) {
        processRegistration($controller, $mappe->registrering[$registreringitem], $token, $noarkObjectCreator);
    }
}

function processRegistration($controller, $registrering, $token, $noarkObjectCreator)
{
    $urlCreateRegistrering = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_REGISTRERING);
    $registreringController = new NikitaEntityController($token);
    if (true === $registreringController->postData($urlCreateRegistrering, $noarkObjectCreator->createRegistrering($registrering))) {
        printSuccess("registrering");
        processAllDokumentbeskrivelse($registreringController, $registrering, $token, $noarkObjectCreator);
    } else {
        printError("registrering", Constants::COULD_NOT_POST,
            $registreringController->getStatusLastCall(),
            $registreringController->getDescriptionLastCall());
    }
}


function processAllDokumentbeskrivelse($controller, $registrering, $token, $noarkObjectCreator)
{
    $dokumentBeskrivelse_items = $registrering->dokumentbeskrivelse->count();
    for ($dokumentBeskrivelseitem = 0; $dokumentBeskrivelseitem < $dokumentBeskrivelse_items; $dokumentBeskrivelseitem++) {
        processDokumentbeskrivelse($controller, $registrering->dokumentbeskrivelse[$dokumentBeskrivelseitem],
            $token, $noarkObjectCreator);
    }
}

function processDokumentbeskrivelse($controller, $dokumentbeskrivelse, $token, $noarkObjectCreator)
{
    $dokumentBeskrivelseController = new NikitaEntityController($token);
    $urlCreateDokumentBeskrivelse = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_DOKUMENT_BESKRIVELSE);
    if (true === $dokumentBeskrivelseController->postData($urlCreateDokumentBeskrivelse,
            $noarkObjectCreator->createDokumentBeskrivelse($dokumentbeskrivelse))
    ) {
        printSuccess("dokumentbeskrivelse");
        processDokumentobjekt($dokumentBeskrivelseController, $dokumentbeskrivelse->dokumentobjekt, $token, $noarkObjectCreator);

    } else {
        printError("dokumentBeskrivelse", Constants::COULD_NOT_POST,
            $dokumentBeskrivelseController->getStatusLastCall(),
            $dokumentBeskrivelseController->getDescriptionLastCall());
    }
}

function processDokumentobjekt($controller, $dokumentobjekt, $token, $noarkObjectCreator)
{
    $urlCreateDokumentObjekt = $controller->getURLFromLinks(Constants::REL_ARKIVSTRUKTUR_NY_DOKUMENT_OBJEKT);
    // Create a controller
    $dokumentObjektController = new NikitaEntityController($token);
    // upload the arkivskaper data
    if ($dokumentObjektController->postData($urlCreateDokumentObjekt,
            $noarkObjectCreator->createDokumentObjekt($dokumentobjekt)) === true
    ) {
        printSuccess("dokumentobjekt");
    } else {
        printError("dokumentobjekt", Constants::COULD_NOT_POST,
            $dokumentObjektController->getStatusLastCall(),
            $dokumentObjektController->getDescriptionLastCall());
    }
}


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


/*
 *
 *
 * Add in
 *     // Get the address to post to

 *
 *    print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->korrespondanseparttype . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->korrespondansepartNavn . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->postadresse . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->postnummer . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->epostadresse . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->telefonnummer . "\n");
                // print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->kontaktperson . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->administrativEnhet . "\n");
                print ($node->arkivdel->mappe[$mappeitem]->registrering[$registreringitem]->korrespondansepart[$kpitem]->saksbehandler . "\n");
 */
?>
