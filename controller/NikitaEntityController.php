<?php
require_once "NikitaController.php";
require_once "Constants.php";


/**
 * Created by PhpStorm.
 * User: tsodring
 * Date: 6/15/17
 * Time: 4:46 PM
 */
class NikitaEntityController extends NikitaController
{
    private $curlHandler;
    private $statusCode;
    private $description = '';

    public function __construct($token)
    {
        $this->token = $token;
        $this->curlHandler = curl_init();
    }

    public function getStatusLastCall() {
        return $this->statusCode;
    }

    public function getDescriptionLastCall() {
       return $this->description;
    }

    function postData($url, $data)
    {
        // Get the page showing application details
        curl_setopt($this->curlHandler, CURLOPT_URL, $url);
        curl_setopt($this->curlHandler, CURLOPT_CUSTOMREQUEST, Constants::POST);
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, array(Constants::ACCEPT_NOARK_JSON));
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, array(
                'Accept: application/vnd.noark5-v4+json',
                'Authorization: ' . $this->token,
                'Content-Type: application/vnd.noark5-v4+json')
        );
        $result = curl_exec($this->curlHandler);
        $info = curl_getinfo($this->curlHandler);
        $this->statusCode = $info['http_code'];

        $this->links = json_decode($result, true);
        if ($this->statusCode !== 201){
            $this->description = $result;
            return false;
        }
        elseif ($result === false) {
            $this->description = $result;
            return $result;
        }
        else {
            return true;
        }
    }

    function getData($url)
    {
        // Get the page showing application details
        curl_setopt($this->curlHandler, CURLOPT_URL, $url);
        curl_setopt($this->curlHandler, CURLOPT_CUSTOMREQUEST, Constants::GET);
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, array(Constants::ACCEPT_NOARK_JSON));
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, array(
                'Accept: application/vnd.noark5-v4+json',
                'Authorization: ' . $this->token)
        );
        $result = json_decode(curl_exec($this->curlHandler), true);
        $this->links = $result;
        return $result;
    }

/*


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
    }*/
}