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
        $data = 'note="Shared on '.date("Y-m-d").'"&shareType=3&path=/'.( !$dest ? '' : $dest.'/' ).$filename;
        return Action::phpCurl($url,"POST",$this->config['httpheader'],$this->config['login'],$this->config['password'],$data);
    }
}
