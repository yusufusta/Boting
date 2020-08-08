<?php
require __DIR__ . '/vendor/autoload.php'; 
use Boting\Boting; 
use Boting\Exception; 

$Bot = new Boting();

$Bot->catch(function ($e) {
    echo $e;

    // $e->getErrorDescription();
    // $e->getErrorCode();
});

$Bot->command("/[!.\/]start/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"];
    try {
        $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Started bot."]);
    } catch (Exception $e) {
        if ($e->getErrorCode() == 400) {
            echo "User stopped bot!";
        }
    }
});

$Bot->command("/\/name ?(.*)/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Name = $Match->group(1);
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Hello, $Name"]);
});

$Bot->command("/\/photo/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendPhoto(["chat_id" => $ChatId, "photo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Telegram_2019_Logo.svg/1200px-Telegram_2019_Logo.svg.png", "caption" => "from Url"]);
    
    # If you are uploading file from local, you must pass true! #
    $Bot->sendPhoto(["chat_id" => $ChatId, "photo" => fopen("test.png", "r"), "caption" => "from Local"], true);
    $Bot->sendDocument(["chat_id" => $ChatId, "document" => fopen("test.png", "r"), "caption" => "from Local as Document"], true);
});

$Bot->command("/\/callback/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Example message for callback query", "reply_markup" => json_encode(["inline_keyboard" => [[["text" => "Click me", "callback_data" => "test"], ["text" => "Don't click me", "callback_data" => "test2"]]]])]);
});

$Bot->command("/\/keyboard/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Example message for keyboard", "reply_markup" => json_encode(["keyboard" => [[["text" => "Click me"], ["text" => "Don't click me"]]]])]);
});

$Bot->command("/Click me/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Thanks for click"]);
});

$Bot->command("/Don\'t click me/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Why clicked?!", "reply_markup" => json_encode(["remove_keyboard" => TRUE])]);
});

$Bot->on("sticker", function ($Update) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $MId = $Update["message"]["message_id"];
    $bir = microtime(true);
    $First = $Bot->sendMessage(["chat_id" => $ChatId, "text" => "I don't like stickers."]);
    $Bot->deleteMessage(["chat_id" => $ChatId, "message_id" => $MId]);
    $Bot->editMessageText(["chat_id" => $ChatId, "message_id" => $First["result"]["message_id"], "text" => "Deleted in " . (microtime(true) - $bir) . "seconds"]);
});

$Bot->on("photo", function ($Update) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $FileId = $Update["message"]["photo"][2]["file_id"]; 
    $Ilk = $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Downloading "]);
    $FileName = $Bot->downloadFile($FileId);
    $Bot->editMessageText(["chat_id" => $ChatId, "message_id" => $Ilk["result"]["message_id"], "text" => "Downloaded file as $FileName"]);

});

$Bot->answer("inline_query", function ($Update) use ($Bot) {
    $Bir = ["type" => "article", "id" => 0, "title" => "test", "input_message_content" => ["message_text" => "This bot created"]];
    $Bot->answerInlineQuery(["inline_query_id" => $Update["inline_query"]["id"], "results" => json_encode([$Bir])]);    
});

$Bot->answer("callback_query", function ($Update) use ($Bot) {
    $Data = $Update["callback_query"]["data"];
    if ($Data == "test") {
        $Bot->editMessageText(["chat_id" => $Update["callback_query"]["message"]["chat"]["id"], "message_id" => $Update["callback_query"]["message"]["message_id"], "text" => "You clicked button!"]);
    } else {
        $Bot->answerCallbackQuery(["callback_query_id" => $Update["callback_query"]["id"], "text" => "Unknown callback: " . $Data, "show_alert" => true]);
    }
});

$Bot->handler("some token"); 
