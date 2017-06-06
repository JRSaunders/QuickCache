# QuickCache
Quick Caching Library

[![Latest Stable Version](https://poser.pugx.org/jrsaunders/quickcache/v/stable)](https://packagist.org/packages/jrsaunders/quickcache)
[![Total Downloads](https://poser.pugx.org/jrsaunders/quickcache/downloads)](https://packagist.org/packages/jrsaunders/quickcache)
[![Latest Unstable Version](https://poser.pugx.org/jrsaunders/quickcache/v/unstable)](https://packagist.org/packages/jrsaunders/quickcache)
[![License](https://poser.pugx.org/jrsaunders/quickcache/license)](https://packagist.org/packages/jrsaunders/quickcache)

Install via composer

```$composer require jrsaunders/quickcache```

``` 
<?php

$setupCache = new \QuickCache\Cache();
$setupCache->setCachePath('/var/a/place/on/my/server/for/chachefiles');


$cache = new \QuickCache\Cache();
$cacheData = $cache->getCacheData('mycachefilename', 30);
if(!$cacheData){
    $cacheData = //get data from DB;
    $cache->saveToCache('mycachefilename', $cacheData);
}

// use the $cacheData somehow! 
```
