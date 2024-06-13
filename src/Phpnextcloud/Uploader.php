<?php
namespace edrard\Phpnextcloud;

use edrard\Log\MyLog;
use edrard\Log\Timer;
use edrard\ExcPhpnextcloud\NoFileException;


class Uploader
{
    protected $config;
    protected $url;

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
            $this->createUrl($filename,$dest);

            $this->uploadFileToNextAlternative($this->url,$filepath,$this->config['login'],$this->config['password']);
            $status = Check::checkIfUploaded($this->url,$this->config['login'],$this->config['password'],$this->config['httpheader']);
            if($status !== FALSE){
                MyLog::info("Ending uploading file - ".$filename);
            }else{
                $data = $this->readFileIn($filepath);
                $this->uploadFileToNext($filename,$data);
                unset($data);
            }
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
    private function uploadFileToNext($filename,$data){
        MyLog::info("Starting uploading file - ".$filename);
        $options = array(
            CURLOPT_SAFE_UPLOAD => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => $this->url,
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
    }

    private function createUrl($filename,$dest=''){
        $this->url = $this->config['url'].'remote.php/dav/files/'.$this->config['login'].'/'.( !$dest ? '' : $dest.'/' ).$filename;
    }
    private function uploadFileToNextAlternative($url,$filepath,$login,$pass){
        MyLog::info("ALTERNATIVE!!! Start uploading file - ".pathinfo($filepath)['basename']);
        $run = 'curl -k -s -u "'.$login.':'.$pass.'" -T "'.$filepath.'" "'.$url.'"';
        exec($run);
        MyLog::info("ALTERNATIVE!!! Ending uploading file - ".pathinfo($filepath)['basename']);
    }
    private function readFileIn($filepath){
        $open = fopen($filepath, "rb");
        $data = fread($open, filesize($filepath));
        fclose($open);
        return $data;
    }
}
