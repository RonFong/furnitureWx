<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 根据键获取session中用户信息
 */
if (!function_exists('user_info')) {
    function user_info($key) {
        return session('user_info.' . $key);
    }
}

/**
 * @param string $url get请求地址
 * @param int $httpCode 返回状态码
 * @return mixed
 */
if (!function_exists('curl_get')) {
    function curl_get($url, &$httpCode = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //不做证书校验,部署在linux环境下请改为true
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }
}


/**
 * @param string $url post请求地址
 * @param array $params
 * @return mixed
 */
if (!function_exists('curl_post')) {
    function curl_post($url, array $params = array())
    {
        $data_string = json_encode($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );
        $data = curl_exec($ch);
        curl_close($ch);
        return ($data);
    }
}

if (!function_exists('curl_post_raw')) {
    function curl_post_raw($url, $rawData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: text'
            )
        );
        $data = curl_exec($ch);
        curl_close($ch);
        return ($data);
    }
}

/**
 * 生成盐值
 */
if (!function_exists('create_salt_value')) {
    function create_salt_value() {
        $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        str_shuffle($str);
        return substr(str_shuffle($str), 0, 6);
    }
}

/**
 * 删除文件
 */
if (!function_exists('delete_file'))
{
    /**
     * 删除文件资源
     * @param $url
     * @return bool
     */
    function delete_file($url){
        $path = PUBLIC_PATH;
        if(!empty($url)){
            if (file_exists($path.$url)) {
                $status = unlink($path.$url);
            } else {
                $status = true;
            }
        } else {
            $status = false;
        }
        return $status;
    }
}

if(!function_exists('array_mulit_exists'))
{
    /**
     * @param $array 待搜索多维数组
     * @param $value 查找的值
     * @param string $find_key 查找的键(为空代表搜索所有键)
     * @param bool $find 是否已经找到
     * @return bool
     */
    function array_mulit_exists($array,$value,$find_key='',&$find = false)
    {
        if(!$find && is_array($array))
        {
            foreach ($array as $key => $item)
            {
                if($find)
                {
                    break;
                }
                if(is_array($item))
                {
                    array_mulit_exists($item,$value,$find_key,$find);
                }
                else
                {
                    if(empty($find_key) && $value === $item)
                    {
                        $find = true;
                        break;
                    }
                    elseif($value === $item && $key === $find_key )
                    {
                        $find = true;
                        break;
                    }
                }
            }
        }
        return $find;
    }
}

if (!function_exists('set_map'))
{
    /**
     * 组装数据查找 map/where 字段条件
     * @param $params   array   传参
     * @param $fields   string  需组装的字段
     * @return array
     */
    function set_map(array $params, string $fields) :array
    {
        $fields = explode(',', $fields);
        $map = [];
        foreach ($params as $k => $v) {
            if (in_array($k, $fields)) {
                $map[$k] = $v;
            }
        }
        return $map;
    }
}
