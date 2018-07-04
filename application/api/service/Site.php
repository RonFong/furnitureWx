<?php

namespace app\api\service;

/**
 * 腾讯地图API
 * Class Site
 * @package app\api\service
 */
class Site
{
    protected $url = 'https://apis.map.qq.com/ws/geocoder/v1/';
    protected $tencent_map = '';

    public function __construct()
    {
        $this->tencent_map = config('api.tencent_map');
    }

    // 逆地址解析
    public function getGeocoder($location,$get_poi = 0,$poi_options = 'address_format=short', $output = 'json' , $callback = '')
    {
        $uri = "?location={$location}&key={$this->tencent_map['key']}&get_poi={$get_poi}&poi_options={$poi_options}&output={$output}&callback={$callback}";
        $result = curl_get($this->url.$uri);
        echo "<pre>";
        print_r($result);
        // TODO 格式化结果
    }
}