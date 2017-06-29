#!/usr/bin/php
<?php

/* $id$
 *
 * noark5-parser
 *
 * Copyright (C) 2017  Ole Aamot
 * Copyright (C) 2017  Thomas Sødring
 *
 * Authors: Ole Aamot <oka@oka.no>, Thomas Sødring <Thomas.Sodring@hioa.no>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once "controller/LoginController.php";
require_once "controller/NikitaEntityController.php";
require_once "controller/NoarkObjectCreator.php";

$baseurl = "http://localhost:8092/noark5v4/";
$user = "admin";
$pass = "password";
$data = array("username" => $user, "password" => $pass);
$data_string = json_encode($data);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseurl . "auth");
curl_setopt($ch, CURLOPT_REFERER, $baseurl);
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
$data = create("http://localhost:8092/noark5v4/", $token);
var_dump($data);
?>
