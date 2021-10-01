<?php


class Db
{

    var $check_user;

    /**
     * Db constructor.
     */
    public function __construct($chat_id)
    {
        $connection = $this->connect_to_db();
        $result= $connection -> query("SELECT * FROM User WHERE chat_id = '$chat_id'");
        $connection -> close();
        if($result->num_rows == 0) {
            $this->check_user=false;
        }
        else {
            $this->check_user=true;
        }

    }




    function url_photo($chat_id,$name){
        $connection = $this->connect_to_db();
        $connection->query("INSERT INTO Image (chat_id,name,url,is_set) VALUES ('$chat_id','$name','','n')");
        $id=$connection->insert_id;
        $url="img/".$name."_".$id.".jpg";
        $connection->query("UPDATE Image SET url='$url' WHERE id='$id'");
        $connection -> close();
        return $url;
    }
    function get_url_photo($chat_id){
        $re=array();
        $connection = $this->connect_to_db();
        $result = $connection -> query("SELECT url FROM Image WHERE chat_id ='$chat_id' AND is_set='n'");
        while ($row = $result -> fetch_assoc()){
            $url    =    $row['url'];
            array_push($re,$url);
            }
        $connection->query("UPDATE Image SET is_set='y' WHERE chat_id='$chat_id'");
        $connection -> close();
        return $re;

    }







    function show_step_user($chat_id){
        $connection = $this->connect_to_db();
        $result = $connection -> query("SELECT step FROM User  WHERE chat_id ='$chat_id'");
        $connection -> close();
        $row=$result->fetch_assoc();
        $step=$row['step'];
        return $step;
    }


    function step_user($step,$chat_id){

        $connection = $this->connect_to_db();
        if($this->check_user)
            $connection -> query("UPDATE User SET step='$step' WHERE chat_id='$chat_id'");
        else {
            $connection->query("INSERT INTO User (chat_id,step) VALUES ('$chat_id','$step')");
            $this->check_user=true;
        }
        $connection -> close();

    }

    function connect_to_db() {
        $connection = new mysqli("localhost", "botbasig_mgsh", "m!g@s#h$2%0^0&0*", "botbasig_botmgsh");

        if ($connection -> connect_error)
            echo "Failed to connect to db: " . $connection -> connect_error;
        //  $connection -> query("SET NAMES utf8");
        return $connection;
    }

}
