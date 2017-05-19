<?php
$xml = new XMLReader();
$xml->open("arkivuttrekk.xml");
$dom = new DOMDOcument;
while ($xml->read()) {
    $node = simplexml_import_dom($dom->importNode($xml->expand(), true));
    // now you can use $node without going insane about parsing
    var_dump($node->dataset);    
    // go to next <product />
    $xml->next('dataset');
}

?>