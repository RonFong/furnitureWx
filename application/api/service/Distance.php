<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\api\service;

/**
 * 经纬度计算
 * Class Location
 * @package app\api\service
 */
class Distance
{
    /**
     * 计算距离内的 经纬度 取值范围
     * @param $lng  float  经度
     * @param $lat  float  纬度
     * @param int $distance 范围限制  （公里）
     * @return array
     */
    public function locationRange($lng, $lat, $distance = 10)
    {
        $earthRadius = 6378.137;//单位km
        $d_lng = 2 * asin(sin($distance / (2 * $earthRadius)) / cos(deg2rad($lat)));
        $d_lng = rad2deg($d_lng);
        $d_lat = $distance / $earthRadius;
        $d_lat = rad2deg($d_lat);
        return array(
            'lat_start' => $lat - $d_lat,//纬度开始
            'lat_end' => $lat + $d_lat,//纬度结束
            'lng_start' => $lng - $d_lng,//纬度开始
            'lng_end' => $lng + $d_lng//纬度结束
        );
    }

    /**
     * 两个经纬坐标的距离
     * @param $lng1  float  经度
     * @param $lat1  float  纬度
     * @param $lng2
     * @param $lat2
     * @return int   距离 （米）
     */
    public function calculationDistance($lng1, $lat1, $lng2, $lat2)
    {
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378137;
        return round($distance);
    }
}