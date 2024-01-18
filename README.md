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
if($uploader->uploadFile('1500mb.bin')){
    #Share File for public
    $sharing = new Sharing($config);
    $respons = $sharing->share('1500mb.bin');
}
```