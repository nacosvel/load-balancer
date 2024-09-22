# Implementing Several Load Balancing Scheduling Algorithms with PHP

[![GitHub Tag](https://img.shields.io/github/v/tag/nacosvel/load-balancer)](https://github.com/nacosvel/load-balancer/tags)
[![Total Downloads](https://img.shields.io/packagist/dt/nacosvel/load-balancer?style=flat-square)](https://packagist.org/packages/nacosvel/load-balancer)
[![Packagist Version](https://img.shields.io/packagist/v/nacosvel/load-balancer)](https://packagist.org/packages/nacosvel/load-balancer)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/nacosvel/load-balancer)](https://github.com/nacosvel/load-balancer)
[![Packagist License](https://img.shields.io/github/license/nacosvel/load-balancer)](https://github.com/nacosvel/load-balancer)

## 安装

推荐使用 PHP 包管理工具 [Composer](https://getcomposer.org/) 安装：

```bash
composer require nacosvel/load-balancer
```

## 文档

### Create Server Instance

```php
<?php
$instance1 = new ServerInstance('http://nacos:nacos@192.168.3.1:8848/nacos');
$instance2 = new ServerInstance('http://nacos:nacos@192.168.3.2:8848/nacos', 1);
$instance3 = new ServerInstance('http://nacos:nacos@192.168.3.3:8848/nacos', 1, false);

// $instance->setScheme('https');
// $instance->setUser('user');
// $instance->setPass('pass');
// $instance->setHost('127.0.0.1');
// $instance->setPort(8848);
// $instance->setPath('/nacos');
// var_dump($instance->getScheme());
// var_dump($instance->getUser());
// var_dump($instance->getPass());
// var_dump($instance->getHost());
// var_dump($instance->getPort());
// var_dump($instance->getPath());
// $instance->setAlive(false);
// $instance->setWeight(5);

var_dump([
    $instance1->getURI(),
    $instance2->getURI(),
    $instance3->getURI(),
]);
// array(3) {
//     [0]=>
//   string(29) "http://192.168.3.1:8848/nacos"
//     [1]=>
//   string(29) "http://192.168.3.2:8848/nacos"
//     [2]=>
//   string(29) "http://192.168.3.3:8848/nacos"
// }

$instanceWithAuth = [
    $instance1->getURI(true),
    $instance2->getURI(true),
    $instance3->getURI(true),
];
var_dump($instanceWithAuth);
// array(3) {
//     [0]=>
//   string(41) "http://nacos:nacos@192.168.3.1:8848/nacos"
//     [1]=>
//   string(41) "http://nacos:nacos@192.168.3.2:8848/nacos"
//     [2]=>
//   string(41) "http://nacos:nacos@192.168.3.3:8848/nacos"
// }
```

### Create ServerIterator

```php
<?php
// Create ServerIterator with Array
$instances               = new ServerIterator($instanceWithAuth);
// Create ServerIterator with Iterator
$instances               = new ServerIterator([$instance1, $instance2, $instance3]);
// Create ServerIterator with Weight
$serverAddressWithWeight = [
    'http://nacos:nacos@192.168.3.1:8848/nacos' => 1,
    'http://nacos:nacos@192.168.3.2:8848/nacos' => 5,
    'http://nacos:nacos@192.168.3.3:8848/nacos' => 10,
];
$instances               = new ServerIterator($serverAddressWithWeight);
// Create ServerIterator with String
$serverAddress           = 'http://nacos:nacos@192.168.3.1:8848/nacos,http://nacos:nacos@192.168.3.2:8848/nacos,http://nacos:nacos@192.168.3.3:8848/nacos';
$instances               = new ServerIterator($serverAddress);

var_dump($instances->getReachableServers());
var_dump($instances->getAllServers());

while ($instances->valid()) {
    var_dump($instances->current()->getURI());
    $instances->next();
}
// string(29) "http://192.168.3.1:8848/nacos"
// string(29) "http://192.168.3.2:8848/nacos"
// string(29) "http://192.168.3.3:8848/nacos"
```

### Create LoadBalancer

```php
<?php
$rule = new RandomRule();
$rule = new RoundRule();
// $rule = new WeightedResponseTimeRule();
$rule = new ZoneLimitationRule();
$rule->setZoneAvoidance($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
$rule = new BestAvailableRule();

// with default rule
$loadBalancer = new LoadBalancer($instances);
var_dump($loadBalancer->getReachableServers());
var_dump($loadBalancer->getAllServers());

// with client config rule
$loadBalancer = new LoadBalancer();
$loadBalancer->setServerAddresses($instances);
$loadBalancer->setRule($rule);

var_dump($loadBalancer->chooseServer()->getURI());
string(29) "http://192.168.3.3:8848/nacos"
```

## License

Nacosvel LoadBalancer is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
