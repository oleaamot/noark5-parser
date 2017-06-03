<?php
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
$data = json_decode($page);
$token = $data->{"token"};
function parser($token, $node, $href) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $href);
    curl_setopt($ch, CURLOPT_REFERER, 'https://nikita.hioa.no:8092/');
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
        parser($token, $node, $array[$item]['href']);
    }
}
$xml = new XMLReader();
if ($argc > 1) {
    $xml->open($argv[1]);
} else {
    echo "noark-parser.php FILE\n";
    exit(0);
}

$dom = new DOMDOcument;
while ($xml->read()) {
    $node = simplexml_import_dom($dom->importNode($xml->expand(), true));
    // now you can use $node without going insane about parsing
    parser($token, $node, "http://nikita.hioa.no:8092/noark5v4/hateoas-api/arkivstruktur/ny-arkiv");
    print_r($node);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://nikita.hioa.no:8092/noark5v4/");
    curl_setopt($ch, CURLOPT_REFERER, 'https://nikita.hioa.no:8092/');
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
        parser($token, $node, $array[$item]['href']);
    }
    // go to next <dataset>
    $xml->next('dataset');
}
?>