<?php
class Db{
    
    public static function get_config(){
        return json_decode(file_get_contents(__DIR__ . "/../config.json"))->db;   
    }

    
    public static function  get_conn() {
        $config = Db::get_config();
        // return new mysqli($config->host, $config->name, $config->password, "botbasig_5y8O");

        // set the PDO error mode to exception
        $conn = new PDO("mysql:host=$config->host;dbname=botbasig_5y8O", $config->name, $config->password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
    
}