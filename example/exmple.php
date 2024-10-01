<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);


require '../vendor/autoload.php';

use edrard\Phpnextcloud\Uploader;
use edrard\Phpnextcloud\Sharing;
use edrard\Phpnextcloud\Delete;
use edrard\Phpnextcloud\Info;

$config = include "config.php";


$uploader = new Uploader($config);
if($uploader->uploadFile('tt/64mb.bin')){
    $sharing = new Sharing($config);
    $sharing->share('64mb.bin');
}

$delete = new Delete($config);
$delete->delete('64mb.bin');

$info = new Info($config);
$list = $info->getFolderList();