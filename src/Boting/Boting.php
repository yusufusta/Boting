<?php
namespace Boting;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class Boting {
    public $Token = "";
    public $Client;

    public function __construct() {
        $this->LatUpdate = 0;
        $this->Offset = 0;
        $this->Commands = [];
        $this->Request = [];
    }

    public function getUpdates() {
        $Request = $this->Client->getAsync('getUpdates?timeout=10&offset=' . $this->Offset, ["verify" => false])->wait();

        if ($Request->getStatusCode() == "409") {
            echo "\nInvalid token\n";
            die();
        }
        $sonuc = json_decode($Request->getBody()->getContents(), true)["result"];
        if (!is_array($sonuc)) echo $sonuc;
        if (count($sonuc) >= 1) {
            $sonuc = array_reverse($sonuc);
            if ($sonuc[0]["update_id"] > $this->LatUpdate) {
                $this->LatUpdate = $sonuc[0]["update_id"];
                $this->Offset = $sonuc[0]["update_id"] + 1;
                return $sonuc;
            } else {
                return false;
            }
        }  
    }

    public function downloadFile($FileId, $fileName = NULL) {
        $Request = $this->Client->getAsync("getFile?file_id=$FileId")->wait();
        $Body = $Request->getBody()->getContents();
        $Result = json_decode($Body, true)["result"];
        $Token = $this->Token;
        $Download = new Client();

        if ($fileName == NULL) {
            preg_match_all('/\/(.*)\.(.*)/m', $Result["file_path"], $File, PREG_SET_ORDER, 0);
            $fileName = $File[0][1] . "." . $File[0][2];
        }
    
        $Request = $Download->getAsync("https://api.telegram.org" . "/file/bot" . $Token . "/" . $Result["file_path"])->wait()->getBody()->getContents();    
    
        file_put_contents($fileName, $Request);
        return $fileName;
    }


    public function __call($method, $args) {
        try {
            $this->Request = $this->Client->postAsync($method, ["form_params" => $args[0]])->wait();
        } catch (Exception $e) {
            echo $e;
        }

        return $this;
    }

    public function body() {
        $Req = $this->Request->getBody()->getContents();
        return $Req;
    }

    public function Handler ($Token, $Function) {
        $this->Token = $Token;
        $this->Client = new Client(["base_uri" => "https://api.telegram.org" . "/bot" . $Token . "/"]);
        while (True) {
            $Update = $this->getUpdates();
            if ($Update != false) {
                foreach ($Update as $Up) {
                    $Function($this, $Up);
                }
            }
        }
    }
}