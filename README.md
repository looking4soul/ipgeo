#IpGeo

Find geo location of ip.

##Usage

###Install

```bash
composer install
```

###Set Up Redis

start up redis on 127.0.0.1:6379

###Download GeoLite2

http://dev.maxmind.com/geoip/geoip2/geolite2/

###Import Ip Geo Into Redis

```php
<?php
require 'vendor/autoload.php';
$block = 'path/to/GeoLite2-City-Blocks-IPv4.csv';
$location = 'path/to/GeoLite2-City-Locations-zh-CN.csv';
$ipgeo = new \Looking4soul\IpGeo\IpGeo();
$ipgeo->import($block, $location);

```

###Search City by Ip

```php
<?php
require 'vendor/autoload.php';
$ipgeo = new \Looking4soul\IpGeo\IpGeo();
var_dump($ipgeo->find_city_by_ip('140.237.24.176'));
/*expected output is array of city and country in zh-CN.
array(2) {
  [0]=>
  string(0) ""
  [1]=>
  string(6) "中国"
}
*/

```

##License

The MIT License (MIT).