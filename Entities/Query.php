<?php
include 'Db.php';

class Query{

    public static function get_config(){
        return json_decode(file_get_contents(__DIR__ . "/../config.json"), true)['cat'];   
    }

    public static function get_conn(){

        $cat = "'" . implode("', '", array_keys(Query::get_config())) . "'";

        $conn =  AskBot/Db::get_conn();

        $conn->exec("CREATE TABLE IF NOT EXISTS Query (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sender_id INT UNSIGNED NOT NULL,
            sender_name VARCHAR(30) NOT NULL,
            chat_id INT NOT NULL,
            content VARCHAR(500) NOT NULL,
            stat ENUM('unanswered', 'answered'),
            cat ENUM($cat) 
            )");

        $conn->exec("CREATE TABLE IF NOT EXISTS Chat (
            chat_id INT PRIMARY KEY,
            cat ENUM($cat) 
            )");
 
        return $conn;
        
    }

    public static function get_cat($chat_id){
        $conn = Query::get_conn();

        $stmt = $conn->prepare("SELECT cat FROM Chat WHERE chat_id=:chat_id");
        $stmt->execute([
            ':chat_id' => $chat_id
        ]);
        $stmt->setFetchMode(PDO::FETCH_NUM);

        foreach ($stmt as $row) {
            return $row[0];
        }

        return false;
        
    }

    public static function set_cat($chat_id, $cat){
        $conn = Query::get_conn();

        $stmt = $conn->prepare("REPLACE INTO Chat (chat_id, cat)
        VALUE (:chat_id, :cat);");
        $stmt->execute([
            ':chat_id' => $chat_id, ':cat' => $cat
        ]);
        
    }
    
    static function insert($sender_id, $sender_name, $chat_id, $content, $cat){
        $conn = Query::get_conn();
        
        $stmt = $conn->prepare("INSERT INTO Query (sender_id, sender_name, chat_id, content, stat, cat)
        VALUES (:sender_id, :sender_name, :chat_id, :content, :stat, :cat);");


        $stmt->execute([
            ':sender_id' => $sender_id, 
            ':sender_name' => $sender_name, 
            ':chat_id' => $chat_id, 
            ':content' => $content, 
            ':stat' => 'unanswered', 
            ':cat' => $cat
        ]);

        $stmt = $conn->prepare("SELECT id FROM Query ORDER BY id DESC LIMIT 0, 1;");
        $stmt->execute();

        // set the resulting array to associative
        // $stmt->setFetchMode(PDO::FETCH_NUM);

        return new Query((int)$stmt->fetch(PDO::FETCH_ASSOC)['id']);
        // foreach ( as $row) {
            // }
            // $this->id = var_dump($stmt);
            // $conn->exec("INSERT INTO Query (sender_id, sender_name, chat_id, content)
            // VALUES ($sender_id, $sender_name, $chat_id, $content)");
            
    } 
    public $id;

    function __construct($id){
        $this->id = $id;
    }

    public function get_from_key($key){
        $conn = Query::get_conn();
        
        $stmt = $conn->prepare("SELECT * FROM Query WHERE id=:id;");
        $stmt->execute([':id' => $this->id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC)[$key];

    }

    public function set_to_key($key, $value){

        $conn = Query::get_conn();

        $stmt = $conn->prepare("UPDATE Query SET :key=:value WHERE id=:id");

        $stmt->execute([
            ':key' => $key,
            ':value' => $value,
            ':id' => $this->id
        ]);
        
        // return Query::get_con()->exec("SELECT $key FROM Query WHERE id=$this->id")->fetch_assoc()[$key];
    }

    public function get_content(){
        return $this->get_from_key['content'];
    }

    public function get_message(){

        $ans = $this->get_from_key('content') . PHP_EOL .
        "id: " . $this->id;
        return $ans;

    }
    
}