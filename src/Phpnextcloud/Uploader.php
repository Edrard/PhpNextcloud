<?php
namespace edrard\Phpnextcloud;

use edrard\Log\MyLog;
use edrard\Log\Timer;
use edrard\ExcPhpnextcloud\NoFileException;


class Uploader
{
    protected $config;

    function __construct(array $config){
        MyLog::init();
        MyLog::info("Init Phpnextcloud Upload library with config",$config);
        $this->config = $config;
    }
    public function uploadFile($filepath,$dest=''){
        Timer::startTime('uploadfile');
        try{
            $f = pathinfo($filepath);

            $filename = $f['basename'];
            $path = $f['dirname'];

            $this->checkFile($filepath);
            $data = $this->readFileIn($filepath);
            $this->uploadFileToNext($filename,$data,$dest);

        }Catch(\Exception $e){
            MyLog::critical('['.string_split_last(get_class($e)).'] '.$e->getMessage());
            return False;
        }
        MyLog::info("File uploaded in: ".Timer::getTime('uploadfile'));
        return True;
    }
    private function checkFile($filepath){
        if (!file_exists($filepath)) {
            throw new NoFileException("Cant find file: ".$filepath);
        }
        return;
    }
    private function uploadFileToNext($filename,$data,$dest=''){
        MyLog::info("Starting uploading file - ".$filename);
        $url = $this->config['url'].'remote.php/dav/files/'.$this->config['login'].'/'.( !$dest ? '' : $dest.'/' ).$filename;
        $options = array(
            CURLOPT_SAFE_UPLOAD => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data,
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
        MyLog::info("Ending uploading file - ".$filename);
    }
    private function readFileIn($filepath){
        $open = fopen($filepath, "rb");
        $data = fread($open, filesize($filepath));
        fclose($open);
        return $data;
    }
}
