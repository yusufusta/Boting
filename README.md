# Boting
> Simple yet Powerful.

[🇹🇷 Türkçe](https://github.com/Quiec/Boting/blob/master/README-tr.md) | [🇬🇧 English](https://github.com/Quiec/Boting/blob/master/README.md)

![](https://img.shields.io/packagist/dt/quiec/boting) ![](https://img.shields.io/packagist/l/quiec/boting) ![](https://img.shields.io/packagist/php-v/quiec/boting) ![](https://img.shields.io/packagist/v/quiec/boting)


_Boting_, The best Telegram Bot library for fast and asynchronous bot typing with PHP.

## Features
* %100 Async (😳)
* Always compatible with the latest BotAPI
* Single file, small size, simple to upload.
* You can run multiple commands at the same time.
* Currently works only with the getUpdates method. Webhook support will be added in future releases. 
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
## Commands
Commands are the same as [BotAPI](https://core.telegram.org/bots/api) commands. You can use BotAPI commands in the same way.
Let's give an example you wanted to send a message,We look at the required parameters from [BotAPI](https://core.telegram.org/bots/api#sendmessage).

<img src="https://i.hizliresim.com/CVaBQE.png" width=600 height=300>

We need `chat_id` and` text`. So let's write our code.

```php
$Bot->sendMessage(["chat_id" => "@fusuf", "text" => "Hello!"]);
```

The process is complete.

## Examples
We can show [this file](https://github.com/Quiec/Boting/blob/master/example.php) as a very good example of using the library.
Also a code that responds to a simple `/start` message:

```php
<?php
require __DIR__ . '/vendor/autoload.php'; //We include the base of the bot.
use Boting\Boting; // We say we want to use the base.

$Main = function ($Bot, $Update) { // We create a function called Main.
    if (!empty($Update["message"])) { // We check if a message has arrived.
        $Mesaj = $Update["message"]["text"]; // We throw the message into the variable.
        $ChatId = $Update["message"]["chat"]["id"]; // We get the chat id to send messages.

        if ($Mesaj === "/start") { // We check if the message is start.
            $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Bot'u başlattınız."]); // We use the sendMessage function.
        }
    }
};


$Bot = new Boting(); // We start the base.
$Bot->Handler("Here ur bot token", $Main); // We define our bot token and function.
```

## Licence
This project is completely open source and protected under MIT license. Please refer to the LICENSE.md file


## Contact
You can contact me on [Telegram] (https://t.me/fusuf) or open Issue.
