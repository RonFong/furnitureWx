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

    /**
     * 逆地址解析
     * @param $location
     * @param int $get_poi
     * @param string $poi_options
     * @param string $output
     * @param string $callback
     * @return array
     */
    public function getGeocoder($location,$get_poi = 0,$poi_options = 'address_format=short', $output = 'json' , $callback = '')
    {
        $uri = "?location={$location}&key={$this->tencent_map['key']}&get_poi={$get_poi}&poi_options={$poi_options}&output={$output}&callback={$callback}";
        $record = [
            'nation' => '',
            'province' => '',
            'city' => '',
            'district' => '',
            'street' => '',
            'street_number' => '',
            'address' => ''
        ];
        try{
            $data = json_decode(curl_get($this->url.$uri),true);


            if($data['status'] == 0){
                if(!empty($data['result']['address_component'])){
                    $record = $data['result']['address_component'];
                }
                if(!empty($data['result']['address'])){
                    $record['address'] = $data['result']['address'];
                }
            }
            return $record;
        }catch (\Exception $e) {
            return $record;
        }
    }

    /**
     * 获取地理位置
     * @param $address
     * @param string $region
     * @return array
     */
    public function getLatLngDetail($address,$region = '')
    {
        $uri = "?address={$address}&key={$this->tencent_map['key']}&region={$region}";
        try{
            $data = json_decode(curl_get($this->url.$uri),true);
            return $data['result']['location'];
        }catch (\Exception $e){
            return [];
        }
    }

    /**
     * 附近的经纬度
     * @param int $distance 直径范围
     * @param $lat          纬度
     * @param $lng          经度
     * @return array
     */
    public static function getaround($distance = 250, $lat, $lng)
    {
        //获取该点周围的4个点
        //$distance = 1;//范围（单位千米)
        define('EARTH_RADIUS', 6371);//地球半径，平均半径为6371km
        $dlng = 2 * asin(sin($distance / (2 * EARTH_RADIUS)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance / EARTH_RADIUS;
        $dlat = rad2deg($dlat);
        $squares = [
            'left-top' => ['lat' => $lat + $dlat, 'lng' => $lng - $dlng],
            'right-top' => ['lat' => $lat + $dlat, 'lng' => $lng + $dlng],
            'left-bottom' => ['lat' => $lat - $dlat, 'lng' => $lng - $dlng],
            'right-bottom' => ['lat' => $lat - $dlat, 'lng' => $lng + $dlng],
        ];

        return $squares;
    }

    /**
     * 计算两点地理坐标之间的距离
     * @param  Decimal $longitude1 起点经度
     * @param  Decimal $latitude1  起点纬度
     * @param  Decimal $longitude2 终点经度
     * @param  Decimal $latitude2  终点纬度
     * @param  Int $unit           单位 1:米 2:公里
     * @param  Int $decimal        精度 保留小数位数
     * @return Decimal
     */
    public static function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 1) {

        $EARTH_RADIUS = 6371; // 地球半径系数
        $PI           = 3.1415926;
        $radLat1      = $latitude1 * $PI / 180.0;
        $radLat2      = $latitude2 * $PI / 180.0;
        $radLng1      = $longitude1 * $PI / 180.0;
        $radLng2      = $longitude2 * $PI / 180.0;
        $a            = $radLat1 - $radLat2;
        $b            = $radLng1 - $radLng2;
        $distance     = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $distance     = $distance * $EARTH_RADIUS * 1000;
        if ($unit == 2) {
            $distance = $distance / 1000;
        }

        return round($distance, $decimal);
    }
}