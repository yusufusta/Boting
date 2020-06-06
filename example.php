<?php
require __DIR__ . '/vendor/autoload.php';
use Boting\Boting;

$Bot = new Boting("YOUR TOKEN HERE");

echo "Bot is working";
while (True) {
    $Update = $Bot->getUpdates();
    if ($Update == false) {
        continue;
    }
    
    if (!empty($Update["inline_query"])) {
        $Bir = ["type" => "article", "id" => 0, "title" => "test", "input_message_content" => ["message_text" => "sad"]];
        $Bot->answerInlineQuery(["inline_query_id" => $Update["inline_query"]["id"], "results" => json_encode([$Bir])]);    
    } elseif (!empty($Update["message"])) {
        if ($Update["message"]["text"] === "/start") $Bot->sendMessage(["chat_id" => $Update["message"]["chat"]["id"], "text" => "Started bot."]);    
        if ($Update["message"]["text"] === "/photo") $Bot->sendPhoto(["chat_id" => $Update["message"]["chat"]["id"], "photo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Telegram_2019_Logo.svg/1200px-Telegram_2019_Logo.svg.png"]);    
        if ($Update["message"]["text"] === "/callback") $Bot->sendMessage(["chat_id" => $Update["message"]["chat"]["id"], "text" => "Example message for callback query", "reply_markup" => json_encode(["inline_keyboard" => [[["text" => "Click me", "callback_data" => "test"], ["text" => "Don't click me", "callback_data" => "test2"]]]]) ]);    
        if ($Update["message"]["text"] === "/keyboard") $Bot->sendMessage(["chat_id" => $Update["message"]["chat"]["id"], "text" => "Example message for keyboard", "reply_markup" => json_encode(["keyboard" => [[["text" => "Click me"], ["text" => "Don't click me"]]]])]);    
        if ($Update["message"]["text"] === "Click me") $Bot->sendMessage(["chat_id" => $Update["message"]["chat"]["id"], "text" => "Thanks for click"]);    
        if ($Update["message"]["text"] === "Don't click me") $Bot->sendMessage(["chat_id" => $Update["message"]["chat"]["id"], "text" => "Why clicked?!", "reply_markup" => json_encode(["remove_keyboard" => TRUE])]);    
    
    } elseif (!empty($Update["callback_query"])) {
        $Data = $Update["callback_query"]["data"];
        if ($Data == "test") {
            $Bot->editMessageText(["chat_id" => $Update["callback_query"]["message"]["chat"]["id"], "message_id" => $Update["callback_query"]["message"]["message_id"], "text" => "You clicked button!"]);
        } else {
            $Bot->answerCallbackQuery(["callback_query_id" => $Update["callback_query"]["id"], "text" => "Unknown callback: " . $Data, "show_alert" => true]);
        }
    }
}