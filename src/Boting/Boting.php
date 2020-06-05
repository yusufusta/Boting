<?php
namespace Boting;

use AsyncRequest\Request;
use AsyncRequest\Response;
use AsyncRequest\AsyncRequest;
use AsyncRequest\IRequest;

use Exception;

class Boting {
    private $Base = "https://api.telegram.org/bot";
    public $Token = "";
    private $asyncRequest;
    private $asyncMethod;
    private $LatUpdate;
    private $sonuc;
    private $sonucm;
    public $httpSonuc;

    public function __construct($token) {
        $this->LatUpdate = 0;
        $this->Token = $token;
        $this->sonucm = "";
        $this->asyncRequest = new AsyncRequest();
        $this->asyncMethod = new AsyncRequest();          
    }

    public function getUpdates() {
        $sonuc = function (Response $response) {
            $this->httpSonuc = $response->getHttpCode();
            $this->sonuc = $response->getBody();
        };
        $this->asyncRequest->enqueue(new Request($this->Base . $this->Token . '/getUpdates?timeout=10&offset=-1'), $sonuc);
        $this->asyncRequest->run(); 
        if ($this->httpSonuc == "409") {
            echo "\nInvalid token\n";
            die();
        }
        $sonuc = json_decode($this->sonuc, true)["result"];
        if (!is_array($sonuc)) echo $sonuc;
        if (count($sonuc) >= 1) {
            $sonuc = array_reverse($sonuc)[0];
            if ($sonuc["update_id"] != $this->LatUpdate) {
                $this->LatUpdate = $sonuc["update_id"];
                return $sonuc;
            } else {
                return false;
            }    
        }
    }

    public function __call($method, $args) {
        $msonuc = function (Response $response) {
            $this->sonucm = $response->getBody();
        };

        $Istek = new Request($this->Base . $this->Token . "/" . $method);

        $args = $args[0];
        $Istek->setOption(CURLOPT_POST, true);
        $Istek->setOption(CURLOPT_POSTFIELDS, http_build_query($args));

        $this->asyncMethod->enqueue($Istek, $msonuc);
        $this->asyncMethod->run();
        
        $jsonsonuc = json_decode($this->sonucm, true);
        if ($jsonsonuc["ok"] == false) {
            echo "\n\nError! Error code: {$jsonsonuc['error_code']}, {$jsonsonuc['description']}\n";
            return;
        }
        return $this->sonucm;
    }
}
