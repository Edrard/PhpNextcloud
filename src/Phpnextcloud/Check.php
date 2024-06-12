<?php
namespace edrard\Phpnextcloud;

use edrard\Log\MyLog;
use edrard\Log\Timer;
use edrard\ExcPhpnextcloud\NoFileException;


class Check
{
    static public function checkIfUploaded($url,$login,$password,$httpheader){
        MyLog::info("Checking url - ".$url);
        $data = '<?xml version="1.0" encoding="UTF-8"?>
        <d:propfind xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns" xmlns:nc="http://nextcloud.org/ns">
        <d:prop>
        <d:getlastmodified/>
        <d:getcontentlength/>
        <d:getcontenttype/>
        <oc:permissions/>
        <d:resourcetype/>
        <d:getetag/>
        </d:prop>
        </d:propfind>';
        $options = array(
            CURLOPT_SAFE_UPLOAD => true,
            CURLOPT_CUSTOMREQUEST => "PROPFIND ",
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_RETURNTRANSFER=> 1,
            CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
            CURLOPT_USERPWD=> $login.':'.$password,
            CURLOPT_HTTPHEADER=>$httpheader
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);
        $status = static::checkRespons($response);
        MyLog::info("Checking upload Url - $url:".$status ? 'Good' : 'Bad');
        return $status;
    }
    static private function checkRespons($response){
        if(preg_match('/200 OK/', $response)){
            return TRUE;
        }
        return FALSE;
    }
}
