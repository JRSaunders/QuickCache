# QuickCache
Quick Caching Library
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