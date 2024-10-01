# PhpNextcloud
Simple Php Nextcloud uploader

Attention!!! Package using shell curl to upload files, becouse sometime php curl have problems to upload big files.

```
$config = array(
    'url' => 'https://nextcloud/',
    'login' => 'login',
    'password' => 'password',
    'httpheader' => array('OCS-APIRequest: true')
);

# Uploading file

$uploader = new Uploader($config);

# In fucntion $uploader->uploadFile is third parametr is False, then its not using shell Curl

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