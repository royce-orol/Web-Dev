<?php
session_start();

class Update
{
    private $error = "";

    public function get_data($id)
    {

        $query = "select * from user where userid = '$id' limit 1";
        $DB = new Database();
        $result = $DB->read($query);


        if($result)
        {
            $row = $result[0];
            return $row;
        }else
        {

            return false;
        }
    }






}


?>
