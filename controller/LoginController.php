<?php
require_once "NikitaController.php";
require_once "Constants.php";


/**
 * Created by PhpStorm.
 * User: tsodring
 * Date: 6/15/17
 * Time: 4:46 PM
 */
class LoginController extends NikitaController
{
    private $curlHandler;
    private $applicationUrl;

    public function __construct($applicationUrl)
    {
        $this->applicationUrl = $applicationUrl;
        $this->curlHandler = curl_init();
    }

    function login($username, $password)
    {
        // Get the page showing application details
        curl_setopt($this->curlHandler, CURLOPT_URL, $this->applicationUrl);
        curl_setopt($this->curlHandler, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($this->curlHandler, CURLOPT_CUSTOMREQUEST, Constants::GET);
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, array(Constants::ACCEPT_NOARK_JSON));
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($this->curlHandler);
        // Check this works, what happens if nikita is down
        $applicationDetails = json_decode($data, true);
        $loginAddress = $this->getHrefAssociatedWithRel(Constants::REL_JWT_LOGIN, $applicationDetails, true);
        if ($loginAddress === false) {
            // Could not find login mechanism
            echo "Could not find login mechanism";
            return;
        }

        $jwtLoginFields = json_encode(array("username" => $username, "password" => $password));
        // Once you have the link to the authentication mechanism, attempt to login
        curl_setopt($this->curlHandler, CURLOPT_URL, $loginAddress);
        curl_setopt($this->curlHandler, CURLOPT_REFERER, $loginAddress);
        curl_setopt($this->curlHandler, CURLOPT_CUSTOMREQUEST, Constants::POST);
        curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $jwtLoginFields);
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jwtLoginFields))
        );
        $loginResult = json_decode(curl_exec($this->curlHandler));
        $this->token = $loginResult->{"token"};
        return $this->token;
    }

    function getToken()
    {
        return $this->token;
    }


}