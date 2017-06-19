<?php

/**
 * Created by PhpStorm.
 * User: tsodring
 * Date: 6/15/17
 * Time: 4:47 PM
 */
class NikitaController
{
    protected $url = "http://localhost:8092/";
    protected $referer = "http://localhost:8092/";
    protected $userAgent = "noark5-parser/0.1";
    protected $request = "POST";
    protected $fields = "";
    protected $returnTransfer = true;
    protected $httpHeader = "";
    protected $token;

    protected $links;

    function getHrefAssociatedWithRel($rel, $links)
    {
        if ($links != null && is_array($links))
            foreach ($links[Constants::LINKS] as $item) {
                if (is_array($item)) {
                    if ($item[Constants::REL] === $rel) {
                        return $item[Constants::HREF];
                    }
                }
            }
        return false;
    }

    function getURLFromLinks($rel) {
        return $this->getHrefAssociatedWithRel($rel, $this->links);
    }

    function getToken()
    {
        return $this->token;
    }
}