# Boting
> Simple yet Powerful.

[🇹🇷 Türkçe](https://github.com/Quiec/Boting/blob/master/README-tr.md) | [🇬🇧 English](https://github.com/Quiec/Boting/blob/master/README.md)

![](https://img.shields.io/packagist/dt/quiec/boting) ![](https://img.shields.io/packagist/l/quiec/boting) ![](https://img.shields.io/packagist/php-v/quiec/boting) ![](https://img.shields.io/packagist/v/quiec/boting)


_Boting_, The best Telegram Bot library for fast and asynchronous bot with PHP.

## Features
* %100 Async (😳)
* Always compatible with the latest BotAPI
* Single file, small size, simple to upload.
* File download/upload
* Events
* WebHook & GetUpdates support 
## Requirements
If you can install [Guzzle](http://docs.guzzlephp.org/en/stable/overview.html#requirements), you can use it easily.

## Install
If you have [Composer](https://getcomposer.org/download/), you can install it very easily:

``` sh
composer require quiec/boting
```

If you want to use the beta version:

``` sh
composer require quiec/boting:dev-master
```

If Composer is not installed, you can easily install it [Here](https://getcomposer.org/download/).

## Get Update
You can get Update with two ways;

### Webhook
If you are going to receive Updates with Webhook method, just add "true" to the handler.

```php
...
$Bot->Handler("Token", true);
```
### Get Updates
This method is used by default. You don't need to add anything extra.
```php
...
$Bot->Handler("Token");
```

## Events
With the new feature added to Boting 2.0, you can now add convenience commands and capture message types with `on`.
### $bot->command
The command, must be **regex**.

**Example** (__Let's catch /start command__):

```php
$Bot->command("/\/start/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Started bot."]);
});
```
**Let's add another command handler:**
```php
$Bot->command("/[!.\/]start/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Started bot."]);
});
```
The bot will now also respond to `/start,!Start,.start` commands.

### $bot->on
The bot will execute the function if a message of the specified type arrives.

**No match is used, On.**

**Example** (_If the photo comes_):
```php
$Bot->on("photo", function ($Update) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Photo came"]);
});
```
You can look at the On Types [here](https://core.telegram.org/bots/api#message).

### $bot->answer
You can use the answer function to answer `inline_query` or` callback_query`.

**Example** (_Let's answer inline_):
```php
$Bot->answer("inline_query", function ($Update) use ($Bot) {
    $Bir = ["type" => "article", "id" => 0, "title" => "test", "input_message_content" => ["message_text" => "This bot created by Boting..."]];
    $Bot->answerInlineQuery(["inline_query_id" => $Update["inline_query"]["id"], "results" => json_encode([$Bir])]);    
});
```

### Special Events
If you do not want to use ready-made functions, you can define your own function.

❗️Type `true` if you are going to use Webhook or `false` if you will get it with GetUpdates.
```php
$Main = function ($Update) {...};
$Bot->Handler("Token", false, $Main);
```

**Example** (_A function that responds to the /start message_):
```php
<?php
require __DIR__ . '/vendor/autoload.php'; //We include the base of the bot.
use Boting\Boting; // We say we want to use the base.

$Bot = new Boting(); // We start the base.
$Main = function ($Update) use ($Bot) { // We create a function called Main.
    if (!empty($Update["message"])) { // We check if a message has arrived.
        $Mesaj = $Update["message"]["text"]; // We throw the message into the variable.
        $ChatId = $Update["message"]["chat"]["id"]; // We get the chat id to send messages.

        if ($Mesaj === "/start") { // We check if the message is start.
            $Bot->sendMessage(["chat_id" => $ChatId, "text" => "You started the bot."]); // We use the sendMessage function.
        }
    }
};
$Bot->Handler("Here ur bot token", false, $Main); // We define our bot token and function.
```
## Commands
Commands are the same as [BotAPI](https://core.telegram.org/bots/api) commands. You can use BotAPI commands in the same way.

Let's give an example you wanted to send a message,We look at the required parameters from [BotAPI](https://core.telegram.org/bots/api#sendmessage).

<img src="https://i.hizliresim.com/CVaBQE.png" width=600 height=300>

We need `chat_id` and` text`. So let's write our code.

```php
$Bot->sendMessage(["chat_id" => "@fusuf", "text" => "Hello!"]);
```

The process is complete.
Commands return Array, after operation.

## Examples
We can show [this file](https://github.com/Quiec/Boting/blob/master/example.php) as a very good example of using the library.
Also a code that responds to a simple `/start` message:

```php
<?php
require __DIR__ . '/vendor/autoload.php'; //We include the base of the bot.
use Boting\Boting; // We say we want to use the base.

$Bot = new Boting(); // We start the base.
$Bot->command("/[!.\/]start/m", function ($Update, $Match) use ($Bot) {
    $ChatId = $Update["message"]["chat"]["id"]; 
    $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Started bot."]);
});
$Bot->Handler("Here ur bot token"); // We define our bot token.
```

## Licence
This project is completely open source and protected under MIT license. Please refer to the LICENSE.md file


## Contact
You can contact me on [Telegram](https://t.me/fusuf) or open Issue.
