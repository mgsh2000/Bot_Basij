<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$config =  json_decode(file_get_contents("config.json"));

$bot_api_key  = $config->token;
$bot_username = $config->name;
$hook_url     = $config->hook;

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Set webhook
    $result = $telegram->setWebhook($hook_url);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    echo $e->getMessage();
}
