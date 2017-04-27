<?php
namespace QuickCache;
/**
 * Class Cache
 * @package QuickCache
 */
class Cache
{
    /**
     * @var array
     */
    protected static $cachedDataArray = array();
    /**
     * @var array
     */
    protected static $cachePathArray = array();
    /**
     * @var null|string
     */
    protected $rawData = null;
    /**
     * @var null|string
     */
    protected $oldRawData = null;
    /**
     * @var null|\stdClass
     */
    protected $data = null;

    /**
     * @return bool|string
     */
    public function getCachePath()
    {
        $class = get_class($this);
        if (isset(self::$cachePathArray[$class])) {
            return rtrim(self::$cachePathArray[$class], '/') . '/';
        }
        return false;
    }

    /**
     * @param string $cachePath
     */
    public function setCachePath($cachePath)
    {
        $class = get_class($this);
        self::$cachePathArray[$class] = $cachePath;

        if (!((file_exists($cachePath)) && is_writable($cachePath))) {
            $madeDir = mkdir($cachePath);
            if (!$madeDir) {
                throw new \Exception('Unable to create ' . $cachePath . ' Quick Cache Path!');
            }
        }
    }

    /**
     * @param null $filename
     * @param null $data
     * @return bool|int
     */
    public function saveToCache($filename = null, $data = null)
    {
        if ($this->getCachePath() && !empty($filename)) {
            $filePath = $this->getCachePath() . $this->prepareFilename($filename);
            $jsonData = json_encode($data);
            return file_put_contents($filePath, $jsonData);
        }

        return true;
    }

    /**
     * @param $filename
     * @param int $ttlSeconds
     * @param bool $removeOld
     * @return bool|mixed
     */
    public function getCacheData($filename, $ttlSeconds = 120000, $removeOld = true, $saveOldCache = true)
    {
        if ($this->getCachePath() && !empty($filename)) {
            $filePath = $this->getCachePath() . $this->prepareFilename($filename);

            if (isset(static::$cachedDataArray[$filePath])) {
                return $this->data = static::$cachedDataArray[$filePath];
            }

            if (!file_exists($filePath)) {
                return false;
            }

            if ((time() - filemtime($filePath)) <= $ttlSeconds) {
                $this->rawData = file_get_contents($filePath);
                return static::$cachedDataArray[$filePath] = $this->data = json_decode($this->rawData);
            }
            $this->oldRawData = file_get_contents($filePath);
            if ($saveOldCache) {
                $this->saveOldCache($filename);
            }
            if ($removeOld) {
                unlink($filePath);
            }
        }
        return false;
    }

    public function getOldCacheData($filename)
    {
        if ($this->getCachePath() && !empty($filename)) {
            $filePath = $this->getCachePath() . $this->prepareFilename($filename, true);

            if (isset(static::$cachedDataArray[$filePath])) {
                return $this->data = static::$cachedDataArray[$filePath];
            }

            if (!file_exists($filePath)) {
                return false;
            }

            $this->rawData = file_get_contents($filePath);
            return static::$cachedDataArray[$filePath] = $this->data = json_decode($this->rawData);
        }
        return false;
    }

    protected function saveOldCache($filename)
    {
        if ($this->getOldRawData() !== null) {
            $filePath = $this->getCachePath() . $this->prepareFilename($filename, true);
            file_put_contents($filePath, $this->getOldRawData());
        }
    }

    /**
     * @param $filename
     * @return string
     */
    protected function prepareFilename($filename, $old = false)
    {
        $ext = '.QuickCache';
        if ($old) {
            $ext = '.OldQuickCache';
        }
        $searchArray = array('.QuickCache', '.OldQuickCache');
        $filename = $this->normalizeString($filename);
        $filename = str_replace($searchArray, '', $filename);

        return $filename . $ext;
    }

    /**
     * @param string $str
     * @return mixed|string
     */
    public function normalizeString($str = '')
    {
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode($str, ENT_QUOTES, "utf-8");
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }

    /**
     * @return string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return null|string
     */
    public function getOldRawData()
    {
        return $this->oldRawData;
    }


}