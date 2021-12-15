<?php

/* 
 * create directories called gif, flood, data directories before using the bot
 */
define('API_KEY', ''); // token
# index.php main file variables :
$admin = ; //ایدی ادمین
$idbot = ; //ایدی ربات
$botname = "دانشگاه فردوسی"; // bot's name
$botusername = "@" . ""; // bot's username
$botusername2 = substr($botusername , 1);
$channel = ""; // related channel

# recieve_gif.php varables :
$bot_url =      "https://api.telegram.org/bot" . API_KEY;
$bot_dl_url =   "https://api.telegram.org/file/bot" . API_KEY;
$gif_saving_path = "" ."/gif"; // main directory of the bot
# DB variables :
$DB_driver = "mysql";
$DB_name = ""; // DB name
$DB_username = ""; // DB's username
$DB_password = ""; // DB's password
$DNS = "$DB_driver:host=localhost;dbname=$DB_name;charset=utf8mb4";

?> 