<?php
namespace QuickCache;
/**
 * Class Cache
 * @package ExchangeRates
 */
class Cache
{
    /**
     * @var array
     */
    protected static $cachedDataArray = array();
    /**
     * @var bool|string
     */
    protected static $cachePath = false;
    /**
     * @var null|string
     */
    protected $rawData = null;
    /**
     * @var null|\stdClass
     */
    protected $data = null;

    /**
     * @return bool|string
     */
    public function getCachePath()
    {
        if (static::$cachePath) {
            return rtrim(static::$cachePath, '/') . '/';
        }
        return false;
    }

    /**
     * @param string $cachePath
     */
    public function setCachePath($cachePath)
    {
        static::$cachePath = $cachePath;
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
    public function getCacheData($filename, $ttlSeconds = 120000, $removeOld = true)
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
            } elseif ($removeOld) {
                unlink($filePath);
            }
        }
        return false;
    }

    /**
     * @param $filename
     * @return string
     */
    protected function prepareFilename($filename)
    {

        $filename = $this->normalizeString($filename);
        $filename = str_replace('.XchgeCache', '', $filename);

        return $filename . '.XchgeCache';
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
}