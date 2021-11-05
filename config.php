<?php
// https://api.telegram.org/bot1876663810:AAGahX6GYh17Nb5CIAntKGQ-7hv6U7b3JrM/setwebhook?url=https://botbasige.cptele.ir/mohamad_sharifi/git/AntiSpam/m-ghafour/index.php
/* 
 * create directories called gif, flood, data directories before using the bot
 */
define('API_KEY', '1876663810:AAGahX6GYh17Nb5CIAntKGQ-7hv6U7b3JrM');
# index.php main file variables :
$admin = 198532866; //ایدی سودو
$idbot = 1876663810; //ایدی ربات
$botname = "ادمین ربات جزوه";
$botusername = "@jozve_admin_bot";
$botusername2 = "jozve_admin_bot";
$channel = "akhlaghiQoutes";

# recieve_gif.php varables :
$bot_url =      "https://api.telegram.org/bot" . API_KEY;
$bot_dl_url =   "https://api.telegram.org/file/bot" . API_KEY;
$gif_saving_path = "https://botbasige.cptele.ir/mohamad_sharifi/git/AntiSpam/gif";
# DB variables :
$DB_driver = "mysql";
$DB_name = "botbasig_sharifi";
$DB_username = "botbasig_mhmed";
$DB_password = "sc0Z&FVLB}8e";
$DNS = "$DB_driver:host=localhost;dbname=$DB_name;charset=utf8mb4";

?>