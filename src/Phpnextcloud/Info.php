<?php
namespace edrard\Phpnextcloud;

use edrard\Log\MyLog;
use edrard\ExcPhpnextcloud\BadXMLException;


class Info
{
    protected $config;
    protected $url;

    function __construct(array $config){
        MyLog::init();
        MyLog::info("Init Phpnextcloud Info with config",$config);
        $this->config = $config;
    }
    static public function createUrl($filename,$base,$login,$dest=''){
        return $base.'remote.php/dav/files/'.$login.'/'.( !$dest ? '' : $dest.'/' ).$filename;
    }
    public function getFolderList($dest=''){
        MyLog::info("Getting info fo directory - ",$dest);
        $this->url = Info::createUrl('',$this->config['url'],$this->config['login'],$dest);
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
        $response = Action::phpCurl($this->url,"PROPFIND",$this->config['httpheader'],$this->config['login'],$this->config['password'],$data);
        $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
        $ns = $xml->getNamespaces(true);
        $json = json_encode($xml->children($ns['d']));
        $array = json_decode($json,TRUE);
        MyLog::info("Listing ended for folder - ",$dest);
        return $array;
    }
}
