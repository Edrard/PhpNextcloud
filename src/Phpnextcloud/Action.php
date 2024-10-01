<?php
namespace edrard\Phpnextcloud;

use edrard\Log\MyLog;
use edrard\ExcPhpnextcloud\BadXMLException;


class Action
{

    static public function phpCurl($url,$type,$httpheader = FALSE,$login = FALSE,$password = FALSE,$data = FALSE){
        $options = array(
            CURLOPT_SAFE_UPLOAD => TRUE,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER=> FALSE,
            CURLOPT_RETURNTRANSFER=> 1,
        );
        if($data){
            $options[CURLOPT_POSTFIELDS] = $data;
        }
        if($login && $password){
            $options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            $options[CURLOPT_USERPWD] = $login.':'.$password;
        }
        if($httpheader){
            $options[CURLOPT_HTTPHEADER] = $httpheader;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    static public function shellCurl($url,$filepath,$login,$pass,$type,$dst = FALSE){
        $add = "";
        switch ($type) {
            case "PUT":
                $add .= " -T ";
                break;
            case "DELETE":
                $add .= "-X DELETE";
                break;
            case "MKCOL":
                $add .= "-X MKCOL";
                break;
            case "GET":
                $add .= "-X GET";
                break;
            case "MOVE":
                $add .= "-X MOVE --header 'Destination: ${dst}'";
                break;
            case "COPY":
                $add .= "-X COPY --header 'Destination: ${dst}'";
                break;
        }
        $run = 'curl -k -s -u "'.$login.':'.$pass.'"'.$add.'"'.$filepath.'" "'.$url.'"';
        exec($run);
    }
}
