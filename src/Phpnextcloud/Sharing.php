<?php
namespace edrard\Phpnextcloud;

use edrard\Log\MyLog;
use edrard\ExcPhpnextcloud\BadXMLException;


class Sharing
{
    protected $config;

    function __construct(array $config){
        MyLog::init();
        MyLog::info("Init Phpnextcloud Sharing with config",$config);
        $this->config = $config;
    }
    public function share($filename,$dest=''){
        try{
            $respons = $this->uploadFileToNext($filename,$dest);
        }Catch(\Exception $e){
            MyLog::critical('['.string_split_last(get_class($e)).'] '.$e->getMessage());
            return False;
        }
        return $this->responsRead($respons);
    }
    private function responsRead($respons){
        $ret =  simplexml_load_string($respons);
        MyLog::info("File was shared ",obj_to_array($ret));
        return $ret;
    }
    private function uploadFileToNext($filename,$dest=''){
        MyLog::info("Sharing file - ".$filename);
        $url = $this->config['url'].'ocs/v2.php/apps/files_sharing/api/v1/shares';

        $options = array(
            CURLOPT_POST => 1,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => 'note="Shared on '.date("Y-m-d").'"&shareType=3&path=/'.( !$dest ? '' : $dest.'/' ).$filename,
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_RETURNTRANSFER=> 1,
            CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
            CURLOPT_USERPWD=> $this->config['login'].':'.$this->config['password'],
            CURLOPT_HTTPHEADER=>$this->config['httpheader']
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
