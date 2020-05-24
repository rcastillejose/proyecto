<?php
/* /src/Service/DniGenerator.php */
namespace App\Service;

class PasswordGenerator
{
    public static function password()
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $password = "";
        
        for($i=0;$i<8;$i++) {
            $password .= substr($str,rand(0,62),1);
        }
        
        return $password;
    }
   
}