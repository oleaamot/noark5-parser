<?php
$xml = new XMLReader();
$xml->open("arkivuttrekk.xml");
$dom = new DOMDOcument;
while ($xml->read()) {
    $node = simplexml_import_dom($dom->importNode($xml->expand(), true));
    // now you can use $node without going insane about parsing
    print ($node->dataset->description . "\n");
    var_dump($node->dataset);
    // go to next <dataset>
    $xml->next('dataset');
}

?>