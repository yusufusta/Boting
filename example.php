<?php
require __DIR__ . '/vendor/autoload.php';
use Boting\Boting;

echo "Started";
$Main = function ($Bot, $Update) {
    if (!empty($Update["inline_query"])) {
        $Bir = ["type" => "article", "id" => 0, "title" => "test", "input_message_content" => ["message_text" => "sad"]];
        $Bot->answerInlineQuery(["inline_query_id" => $Update["inline_query"]["id"], "results" => json_encode([$Bir])]);    
    } elseif (!empty($Update["message"])) {
        $Message = $Update["message"]["text"];
        $ChatId = $Update["message"]["chat"]["id"];

        if ($Message === "/start") {
            $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Bot Started..."])
                ->sendMessage(["chat_id" => $ChatId, "text" => "Fully Async"]);    
        } else if ($Message === "/photo") {
            $Bot->sendPhoto(["chat_id" => $ChatId, "photo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Telegram_2019_Logo.svg/1200px-Telegram_2019_Logo.svg.png"]);
        } else if ($Message === "/callback") {
            $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Example message for callback query", "reply_markup" => json_encode(["inline_keyboard" => [[["text" => "Click me", "callback_data" => "test"], ["text" => "Don't click me", "callback_data" => "test2"]]]])]);
        } else if ($Message === "/keyboard") {
            $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Example message for keyboard", "reply_markup" => json_encode(["keyboard" => [[["text" => "Click me"], ["text" => "Don't click me"]]]])]);
        } else if ($Message === "Click me") {
            $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Thanks for click"]);
        } else if ($Message === "Don't click me") {
            $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Why clicked?!", "reply_markup" => json_encode(["remove_keyboard" => TRUE])]);
        }
    
    } elseif (!empty($Update["callback_query"])) {
        $Data = $Update["callback_query"]["data"];
        if ($Data == "test") {
            $Bot->editMessageText(["chat_id" => $Update["callback_query"]["message"]["chat"]["id"], "message_id" => $Update["callback_query"]["message"]["message_id"], "text" => "You clicked button!"]);
        } else {
            $Bot->answerCallbackQuery(["callback_query_id" => $Update["callback_query"]["id"], "text" => "Unknown callback: " . $Data, "show_alert" => true]);
        }
    }
};


$Bot = new Boting();
$Bot->Handler("1049255545:AAE2iwoyTrqvadFPREBneMCsL2QoqxdIeRA", $Main);
