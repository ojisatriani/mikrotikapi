## Mikrotik APi Laravel
Client API for RouterOS/Mikrotik

Instalation
----

Via composer:
```
composer require ojisatriani/mikrotikapi
```

Manual composer.json in require section:
```
"require": {
    "ojisatriani/mikrotikapi": "dev-master", // <- this line
}
```

Basic Usage:
----

```$php
use OjiSatriani\MikrotikApi\Mikroji;
$router = Mikroji::connect('192.168.3.1','user-api','user-api');
echo $router->ram() .'<br />'; // 1652.2
echo $router->hdd() .'<br />'; // 915.8
echo $router->cpu() .'<br />'; // 0
echo $router->rx('ether1') .'<br />'; // 1.85Kb/Mb/Gb/Tb
echo $router->tx('ether1') .'<br />'; // 47.7Kb/Mb/Gb/Tb
echo $router->rxInMb('ether1') .'<br />'; // 1.85 (view chart or graph) 
echo $router->txInMb('ether1') .'<br />'; // 47.70 (view chart or graph)
echo json_encode($router->getInterface()) .'<br />'; //  {"ether1":"ether1","ether2:ether2","ether3:ether3"}
echo $router->command('log/print'); //  return array()
```

Mikrotik Wiki page at http://wiki.mikrotik.com/wiki/API_PHP_class

https://github.com/BenMenking/routeros-api


## Configure API credentials

*RouterOS Winbox*

1. Enabling API.

Go to **IP** -> **Services** and enable **api** service.

2. Creating API user. 

Go to **System** -> **Users**, tab **Groups**. Create new group with permissions: ```api```, ```read```, ```write```.

Then after group created go to tab ```Users``` and create new. Associate this user with group previously created.

Done.