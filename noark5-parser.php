<?php
$xml = new XMLReader();
$xml->open("arkivuttrekk.xml");
$dom = new DOMDOcument;
while ($xml->read()) {
    $node = simplexml_import_dom($dom->importNode($xml->expand(), true));
    // now you can use $node without going insane about parsing
    print ($node->dataset->description . "\n");
    $data = array("username" => "admin", "password" => "password");
    $data_string = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://nikita.hioa.no:8092/noark5v4/auth");
    curl_setopt($ch, CURLOPT_REFERER, 'https://nikita.hioa.no:8092/');
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
    var_dump($page);
    // var_dump($node->dataset);
    // go to next <dataset>
    $xml->next('dataset');
}

?>