<?php
namespace Boting;
use GuzzleHttp\Client;
use Spatie\Regex\Regex;

class Exception extends \Exception {
    private $Message = '';
    private $Description = '';

    public function __construct($Message, $Code = 0, \Exception $Previous = null, $Error = '', $Description = '') {
        $this->Code = $Code;
        $this->Message = $Error;
        $this->Description = $Description;
        parent::__construct($Message, $Code, $Previous);
    }

    public function getErrorCode(): string {
        return $this->Code;
    }

    public function getErrorDescription(): string {
        return $this->Description;
    }

    public function __toString() {
        return 'Telegram returned an error: ' . $this->Description.PHP_EOL.'Backtrace:'.PHP_EOL.$this->getTraceAsString();
    }
}

class Boting {
    public $Token = "";
    public $Client;
    public $Async = false;

    public function __construct() {
        $this->LatUpdate = 0;
        $this->Offset = -1;
        $this->Commands = [];
        
        $this->answerType = ["inline_query", "callback_query"];
        $this->answerTypes = [];

        $this->Type = ["animation", "audio", "document", "photo", "sticker", "video", "video_note", "voice", "contact", "dice", "game", "poll", "venue", "location", "new_chat_members", "left_chat_member", "new_chat_title", "new_chat_photo", "delete_chat_photo", "group_chat_created", "supergroup_chat_created", "pinned_message", "text"];
        $this->Types = [];
        $this->Request = [];
        $this->errorHandler = false;
        $this->uploadTypes = ["sendPhoto", "sendAudio", "sendDocument", "sendVideo", "sendAnimation", "sendVoice", "sendVideoNote"];
    }

    public function getUpdates() {
        if ($this->WebHook == false) {
            $Request = $this->Client->getAsync('getUpdates?timeout=10&offset=' . $this->Offset, ["verify" => false])->wait();
            $Body = $Request->getBody()->getContents();

            $sonuc = json_decode($Body, true);
            if ($sonuc["ok"] === false) {
                if ($sonuc["error_code"] === 401) {
                    throw new Exception("ERROR_" . $sonuc["error_code"], $sonuc["error_code"], NULL, "Invalid Bot Token", "Invalid Bot Token");
                }
            } else {
                $sonuc = $sonuc["result"];
                if (!is_array($sonuc)) return;
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
        } else {
            $Body = file_get_contents("php://input");
            return json_decode($Body, true);
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

    public function catch($function) {
        return $this->errorHandler = [$function];
    }


    public function __call($method, $args) {
        if (in_array($method, $this->uploadTypes) && !empty($args[1]) && $args[1] === true) {
            $MultiPart = [];
            foreach ($args[0] as $arg => $deger) {
                $MultiPart[] = ["name" => $arg, "contents" => $deger];
            }
            
            if ($this->Async) {
                return $Request = $this->Client->postAsync($method, ["multipart" => $MultiPart]);
            } else {
                $Request = $this->Client->postAsync($method, ["multipart" => $MultiPart])->wait();
            }
        } else {
            if ($this->Async) {
                return $Request = $this->Client->postAsync($method, ["form_params" => $args[0]]);
            } else {
                $Request = $this->Client->postAsync($method, ["form_params" => $args[0]])->wait();
            }
        }
        $Json = json_decode($Request->getBody()->getContents(), true);

        if ($Json["ok"] === false) {
            if ($this->errorHandler === false) {
                throw new Exception("ERROR_" . $Json["error_code"], $Json["error_code"], NULL, $Json["description"], $Json["description"]);
            } else {
                $this->errorHandler[0](new Exception("ERROR_" . $Json["error_code"], $Json["error_code"], NULL, $Json["description"], $Json["description"]));
            }
        } else {
            return $Json;
        }
    }

    public function command($commands = "/\/start/m", $callback) {
        if (is_array($commands)) {
            foreach ($commands as $command) {
                if (empty($this->Commands[$command])) {
                    $this->Commands[$command] = [$command, $callback];
                } else {
                    echo "$command already defined!";
                    continue;
                }
            }
        } else {
            if (empty($this->Commands[$commands])) {
                $this->Commands[$commands] = [$commands, $callback];;
            } else {
                echo "$commands already defined!";
                return;
            }
        }
        return true;
    }

    public function on($types, $callback) {
        if (is_array($types)) {
            foreach ($types as $type) {
                if (empty($this->Types[$type])) {
                    if (in_array($type, $this->Type)) {
                        $this->Types[$type] = [$type, $callback];
                    } else {
                        echo "$type invalid type!";
                        continue;      
                    }
                } else {
                    echo "$type already defined!";
                    continue;
                }
            }
        } else {
            if (empty($this->Types[$types])) {
                if (in_array($types, $this->Type)) {
                    $this->Types[$types] = [$types, $callback];
                } else {
                    echo "$types invalid type!";
                    return;      
                }
            } else {
                echo "$types already defined!";
                return;
            }
        }
        return true;
    }

    private function isCommand($Update) {
        if(!empty($Update["message"]["text"])) {
            $Text = $Update["message"]["text"];
            foreach ($this->Commands as $Command) {
                $Match = Regex::match($Command[0], $Text);
                if ($Match->hasMatch()) {
                    return [$Command[1], $Match];
                }
            }

            if (!empty($this->Types["text"])) {
                return [$this->Types["text"][1]];
            } else {
                return false;
            }
        } else if (!empty($Update["callback_query"])) {
            if (!empty($this->answerType["callback_query"])) {
                return [$this->answerType["callback_query"][1]];
            }
            return false;
        } else if (!empty($Update["inline_query"])) {
            if (!empty($this->answerType["inline_query"])) {
                return [$this->answerType["inline_query"][1]];
            }
            return false;
        } else {
            if (empty($Update["callback_query"]) || empty($Update["inline_query"])) {
                foreach ($this->Type as $Tip) {
                    if (!empty($Update["message"][$Tip])) {
                        if (!empty($this->Types[$Tip])) {
                            return [$this->Types[$Tip][1]];
                        }
                    }
                }
                return false;    
            }
        return false;
        }
    }

    public function answer($query, $callback) {
        if (!in_array($query, $this->answerType)) {
            echo "$query invalid type.";
            return;
        }
        if (empty($this->answerType[$query])) {
            $this->answerType[$query] = [$query, $callback];;
        } else {
            echo "$query already defined!";
            return;
        }
    }

    public function handler ($Token, $WebHook = false, $CallBack = NULL) {
        $this->Token = $Token;
        if ($WebHook == true) {
            $this->WebHook = true;
            $this->Client = new Client(["http_errors" => false, "base_uri" => "https://api.telegram.org" . "/bot" . $Token . "/"]);
            $this->hookClient = new Client();
            $Update = $this->getUpdates();
            if ($CallBack === NULL) {
                $Command = $this->isCommand($Update);
                if ($Command !== false) {
                    if (count($Command) === 2) {
                        $Command[0]($Update, $Command[1]);
                    } else {
                        $Command[0]($Update);
                    }
                }    
            } else {
                $CallBack($Update);
            }
        } else {
            $this->WebHook = false;
            $this->Client = new Client(["http_errors" => false, "base_uri" => "https://api.telegram.org" . "/bot" . $Token . "/"]);
            while (True) {
                $Update = $this->getUpdates();
                if ($Update != false) {
                    foreach ($Update as $Up) {
                        if ($CallBack === NULL) {
                            $Command = $this->isCommand($Up);
                            if ($Command !== false) {
                                if (count($Command) === 2) {
                                    $Command[0]($Up, $Command[1]);
                                } else {
                                    $Command[0]($Up);
                                }
                            }    
                        } else {
                            $CallBack($Up);
                        }
                    }
                }    
            }    
        }
    } 
}
