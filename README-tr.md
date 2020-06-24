# Boting
> Basit ama güçlü.

[🇹🇷 Türkçe](https://github.com/Quiec/Boting/blob/master/README-tr.md) | [🇬🇧 English](https://github.com/Quiec/Boting/blob/master/README.md)

![](https://img.shields.io/packagist/dt/quiec/boting) ![](https://img.shields.io/packagist/l/quiec/boting) ![](https://img.shields.io/packagist/php-v/quiec/boting) ![](https://img.shields.io/packagist/v/quiec/boting)


_Boting_, PHP ile hızlı ve asenkron bot yazmanız için en iyi Telegram Bot kütüphanesi.

## Özellikleri
* %100 Async (😳)
* Her zaman en son BotAPI'ye uygun
* Tek dosya, boyutu küçük, yüklenmesi basit.
* Aynı anda birden fazla komut çalıştırabilirsiniz
* Şu anlık sadece getUpdates methodu ile çalışmaktadır. İleri ki sürümler de webhook desteği gelecektir. 
## Gereksinimler
Eğer [Guzzle](http://docs.guzzlephp.org/en/stable/overview.html#requirements) yükleyebiliyorsanız rahatlıkla kullanabilirsiniz.

## Yükleme
Eğer [Composer](https://getcomposer.org/download/)'e sahipseniz, çok kolay kurabilirsiniz:

``` sh
composer require quiec/boting
```

Beta sürümünü kullanmak isterseniz:

``` sh
composer require quiec/boting:dev-master
```

Eğer Composer yüklü değilse, [bu adresten](https://getcomposer.org/download/) kolaylıkla yükleyebilirsiniz.
## Komut Çağırma
Komutlar [BotAPI](https://core.telegram.org/bots/api) komutları ile aynı. BotAPI komutları aynı şekilde kullanabilirsiniz.
Örnek verelim mesaj göndermek istediniz, [BotAPI](https://core.telegram.org/bots/api#sendmessage)'den gerekli parametrelere bakıyoruz.

<img src="https://i.hizliresim.com/CVaBQE.png" width=600 height=300>

Bize `chat_id` ve `text` lazım. O zaman kodumuzu yazalım.

```php
$Bot->sendMessage(["chat_id" => "@fusuf", "text" => "Merhaba"]);
```

Bu kadar.

## Örnekler
Kütüphanenin kullanımı hakkında çok iyi bir örnek olarak [bu dosyayı](https://github.com/Quiec/Boting/blob/master/example.php) gösterebiliriz.
Ayrıca basit bir `/start` mesajına yanıt veren bir kod:

```php
<?php
require __DIR__ . '/vendor/autoload.php'; // Bot'un tabanını dahil ediyoruz.
use Boting\Boting; // Tabanı kullanmak istediğimizi söylüyoruz.

$Ana = function ($Bot, $Update) { // Ana diye bir fonksiyon oluşturuyoruz.
    if (!empty($Update["message"])) { // Mesaj mı geldi diye kontrol ediyoruz.
        $Mesaj = $Update["message"]["text"]; // Mesajı değişkene atıyoruz.
        $ChatId = $Update["message"]["chat"]["id"]; // Mesaj göndermek için sohbet id'isini alıyoruz.

        if ($Mesaj === "/start") { // Mesajın start olup olmadığını kontrol ediyoruz.
            $Bot->sendMessage(["chat_id" => $ChatId, "text" => "Bot'u başlattınız."]); // sendMessage fonksiyonu kullanıyoruz.
        }
    }
};


$Bot = new Boting(); // Tabanı başlatıyoruz.
$Bot->Handler("Buraya Bot Tokeniniz", $Ana); // Bot tokenimizi ve fonksiyonumuzu tanımlıyoruz.
```

## Lisans
Bu proje tamamen açık kaynaklı olup, MIT lisansı altında korunmaktadır. Lütfen LICENSE.md dosyasına bakın.

## İletişim
Bana [Telegram](https://t.me/fusuf) üzerinden ulaşabilirsiniz ya da Issue açabilirsiniz.
