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
        $response = Action::phpCurl($url,"PROPFIND",$httpheader,$login,$password,$data);
        $status = static::checkRespons($response);
        $respons = $status !== FALSE ? 'Good' : 'Bad';
        MyLog::info("Checking upload Url - ".$url.": ".$respons);
        return $status;
    }
    static private function checkRespons($response){
        if(preg_match('/200 OK/', $response)){
            return TRUE;
        }
        return FALSE;
    }
}
