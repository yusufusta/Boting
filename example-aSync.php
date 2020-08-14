<?php
require __DIR__ . '/vendor/autoload.php'; 
use Boting\Boting; 
use Boting\Exception; 
use Psr\Http\Message\ResponseInterface;

$Bot = new Boting();
$Bot->Async = true;
$Bot->catch(function ($e) {
    echo $e;

    // $e->getErrorDescription();
    // $e->getErrorCode();
});

$Bot->command("/[!.\/]start/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"];
    $Baslangic = microtime(true);
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Ping!"])->then(
        function (ResponseInterface $res) use ($Bot, $ChatId, $Baslangic){
            $mid = json_decode($res->getBody(), true)["result"]["message_id"];
            $Bot->editMessageText(["chat_id" => $ChatId, "message_id" => $mid, "text" => "Pong!\n" . (microtime(true) - $Baslangic) . "ms"]);
        }
    );
});

$Bot->handler("some token"); 
