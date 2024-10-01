<?php
namespace edrard\Phpnextcloud;

use edrard\Log\MyLog;
use edrard\ExcPhpnextcloud\BadXMLException;


class Delete
{
    protected $config;
    protected $url;

    function __construct(array $config){
        MyLog::init();
        MyLog::info("Init Phpnextcloud Delete with config",$config);
        $this->config = $config;
    }
    public function delete($filename,$dest=''){
        try{
            $this->url = Info::createUrl($filename,$this->config['url'],$this->config['login'],$dest);
            $status = Check::checkIfUploaded($this->url,$this->config['login'],$this->config['password'],$this->config['httpheader']);
            if($status === FALSE){
                MyLog::info("Can not find file to delete - ".$dest.$filename);
            }else{
                return $this->deleteFileFromNext($filename,$dest);
            }
        }Catch(\Exception $e){
            MyLog::critical('['.string_split_last(get_class($e)).'] '.$e->getMessage());
            return False;
        }
    }
    private function deleteFileFromNext($filename,$dest=''){
        MyLog::info("Deleting File - ".$filename);
        Action::phpCurl($this->url,"DELETE",$this->config['httpheader'],$this->config['login'],$this->config['password']);
        $status = Check::checkIfUploaded($this->url,$this->config['login'],$this->config['password'],$this->config['httpheader']);
        if($status !== FALSE){
             MyLog::error($filename." not deleted");
             return FALSE;
        }
        MyLog::info($filename." was deleted ");
        return TRUE;
    }
}
