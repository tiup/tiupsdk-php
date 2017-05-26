TiUP开放平台PHP SDK，使用前请先仔细阅读相关文档

### Installation

```shell
composer require tiup/tiupsdk 
```

### Usage

初始化

```php
$options = array(
	'auth_host' => 'https://test.tiup.cn/',
	'api_host' => 'https://test.tiup.cn/',
	'client_id' => 'client_id',
	'client_secret' => 'client_secret'
	);
$tiup = new Tiup\Tiup($options);
```

获取登录链接

```php
$url = $tiup->getAuthorizationUrl('http://localhost/callback.php');
echo '<a target="_blank" href="'.$url.'">使用TiUP账号登录</a>';
```

登录成功回调页面获取用户信息

```php

$token = $tiup->getAccessToken();
$user = $tiup->me($token);
```

更多SDK文档请访问

[https://github.com/tiup/tiupsdk-php/wiki](https://github.com/tiup/tiupsdk-php/wiki)

#### 帮助和反馈

在使用中有疑问可加入我们的开发者讨论群 ```628071204```


