# PhpNextcloud
Simple Php Nextcloud uploader

```
$config = array(
    'url' => 'https://nextcloud/',
    'login' => 'login',
    'password' => 'password',
    'httpheader' => array('OCS-APIRequest: true')
);

# Uploading file

$uploader = new Uploader($config);
if($uploader->uploadFile('1500mb.bin','/path/inside/nc')){
    #Share File for public
    $sharing = new Sharing($config);
    $respons = $sharing->share('1500mb.bin','/path/inside/nc');
}
# Delete file
$delete = new Delete($config);
$delete->delete('64mb.bin');

# List folder
$info = new Info($config);
$list = $info->getFolderList();
```