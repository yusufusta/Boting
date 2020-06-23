<?php
namespace Boting;

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

    public function __call($method, $args) {
        $this->Request[] = $this->Client->postAsync($method, ["form_params" => $args[0]]);
        Promise\unwrap($this->Request);
        return $this;
    }

    public function run() {
        Promise\settle($this->Request)->wait();
    }

    public function Handler ($Token, $Function) {
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