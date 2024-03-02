# TelegramDump
The Laravel package for sending dumps directly to Telegram is an extremely useful tool for developers who want to monitor and analyze the behavior of Laravel applications in real time by receiving dumps directly in a Telegram chat. This package facilitates a direct link between the Laravel application and Telegram, allowing developers to quickly and efficiently send dumps of data or the application's state at critical moments or for debugging.

### Key Features:
- **Simple Integration:** It integrates easily into existing Laravel projects, requiring only a few lines of code for setup and initialization.
- **Flexible Configuration:** Offers extensive configuration options, including setting the Telegram bot token and chat ID to direct messages to the desired location.
- **Security:** Implements security measures to ensure that dumps are sent securely, protecting sensitive data.
- **Customization:** Allows customization of the messages sent to Telegram, including the ability to add additional relevant information for debugging.
- **Real-time Reporting:** Ideal for real-time application monitoring, instantly sending dumps to Telegram upon certain events or errors.
- **Support for Formatting:** Supports message formatting to improve the readability of dumps in Telegram, facilitating quick data analysis.

### Typical Usage:

Developers can use this package to send notifications about uncaught exceptions, validation errors, or any other relevant information that helps monitor the application's state and quickly identify issues. It is particularly useful in production environments, where direct access to logs may be more restricted.


### Implementation:

Implementing the package requires configuring a Telegram bot and obtaining a token, which are then used to set up the package in the Laravel configuration file. After configuration, developers can call the function to send dumps anywhere in their code, transmitting any information they wish directly to the specified Telegram chat.


# Installation
You can install the package via composer:

```bash
composer require nicolae-soitu/telegram-dump
```

You can publish
```bash
php artisan vendor:publish --provider="NicolaeSoitu\TelegramDump\Providers\TelegramDumpServiceProvide"
```


# Create bot
For using this package you need have or to create Telegram bot

- Open telegram go to @BotFather
- Type /newbot
- Follow the instructions.
- Go to your bot push /start

## Add to the `.env` file
```
TELEGRAM_DUMP_TOKEN=...telegram token...
TELEGRAM_DUMP_CHAT_ID=...telegram chat id...
```

## Usage example
```php

use NicolaeSoitu/TelegramDump/TelegramDump;

//...
  $var = ['foo'=>'bar'];
  TelegramDump::send($var);

  // or with parameters

  TelegramDump::
  // optional user id or chat id
  to(120200000030203020)
  // optional type of notification
  // info ℹ️
  // warning ⚠️
  // ok ✅
  // delete ❌
  // or custom (any string/emoji)
  -> setType('info')
  // Notification title
  -> setTitle('Title')
  // Notification descriotion
  -> description('Description')
  // send is required - any variable/object/string...
  -> send($var);

  try {
  // Code that may throw an Exception or Error.
  } catch (Throwable $t) {
    TelegramDump::send($t);
  } catch (Exception $e) {
    TelegramDump::send($e);
  }
// ...
```

#### To receive all errors
in App\Exceptions\Handler
```php
#app/Exceptions/Handler.php
use NicolaeSoitu/TelegramDump/TelegramDump;
// add in reportable
//...
$this->reportable(function (Throwable $e) {
  TelegramDump::send($e); // add it here in reportable
});
//...
```

In conclusion, this package represents an efficient and flexible solution for monitoring and debugging Laravel applications, providing a direct and fast method to send critical information to Telegram, facilitating a rapid response to emerging issues.
