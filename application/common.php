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

use think\Db;
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

if (!function_exists('move_tmp_img'))
{
    /**
     * 将临时保存的图片转存到指定地址
     * @param string $tmpImg            临时图片地址
     * @param $folder   string          目标文件夹
     * @return mixed
     */
    function move_tmp_img($tmpImg, $folder)
    {
        //此图片非临时文件夹中的图片
        if (strpos($tmpImg, $folder) !== false)
            return ['img' => $tmpImg];

        //临时图片绝对地址
        $tmpPath = str_replace(VIEW_IMAGE_PATH, IMAGE_PATH, $tmpImg);
        //获取图片名
        $imgName = substr($tmpImg, strlen(VIEW_IMAGE_PATH) + 14);
        //目标转存地址
        $path = IMAGE_PATH . $folder . $imgName;

        $result = rename($tmpPath, $path);
        if (!$result)
            return false;

        //转存缩略图
        $getThumbPath = function ($tmpPath) {
            //获取缩略图绝对地址
            $array = explode('.', $tmpPath);
            $array[count($array) - 2] = $array[count($array) - 2] . '_thumb.';
            return implode('', $array);
        };
        $thumbImg = $getThumbPath($tmpPath);
        if (file_exists($thumbImg)) {
            if (!rename($thumbImg, $getThumbPath($path)))
                return false;
        }
        return VIEW_IMAGE_PATH . $folder . $imgName;
    }
}

if (!function_exists('unlink_img'))
{
    /**
     * 删除图片及其缩略图
     * @param $img  string||array  一张或一组图片的路径
     * @return bool
     */
    function unlink_img($img)
    {
        $unlinkImg = function ($path) {
            if (file_exists(PUBLIC_PATH . $path)) {
                unlink(PUBLIC_PATH . $path);
                //获取缩略图绝对地址
                $array = explode('.', $path);
                $array[count($array) - 2] = $array[count($array) - 2] . '_thumb.';
                $thumbImg = implode('', $array);
                if (file_exists(PUBLIC_PATH . $thumbImg))
                    unlink(PUBLIC_PATH . $thumbImg);
            }
        };
        if (is_array($img)) {
            foreach ($img as $v) {
                $unlinkImg($v);
            }
        } else {
            $unlinkImg($img);
        }
        return true;
    }
}


if (!function_exists('get_thumb_img'))
{
    /**
     * 如果存在缩略图，则返回缩略图地址，否则返回原地址
     * @param $img  string||array  一张或一组图片的路径
     * @return bool
     */
    function get_thumb_img($img)
    {
        if (!$img) {
            return '';
        }
        if (file_exists(PUBLIC_PATH . $img)) {
            $array = explode('.', $img);
            $array[count($array) - 2] = $array[count($array) - 2] . '_thumb.';
            return implode('', $array);
        } else {
            return $img;
        }
    }
}


if (!function_exists('has_field')) {
    /**
     * 判断数据表是否存在该字段
     * @param $table_name
     * @param $field
     * @return bool
     */
    function has_field($table_name, $field)
    {
        $field_list = Db::table($table_name)->getTableFields();
        return in_array($field, $field_list);
    }
}

if (!function_exists('reset_sort')) {
    /**
     * 重新排序
     * @param $id
     * @param $table_name
     * @param string $type
     * @param string $field_sort
     * @return array|false|PDOStatement|string|\think\Collection
     */
    function reset_sort($id, $table_name, $type = 'asc', $field_sort = 'sort_num')
    {
        $pk = Db::name($table_name)->getPk();//获取当前表主键字段名
        //判断是否存在'pid'，若存在，则只取同级别数据
        if (has_field($table_name, 'pid')) {
            $map['pid'] = Db::table($table_name)->where($pk, $id)->value('pid');
        } elseif (has_field($table_name, 'cat_id')) {
            $map['cat_id'] = Db::table($table_name)->where($pk, $id)->value('cat_id');
        } elseif (has_field($table_name, 'language')) {
            $map['language'] = session('config.language');
        } else {
            $map[] = ['exp', '1=1'];
        }
        $data = Db::table($table_name)->where($map)->field($pk . ',' . $field_sort)->order($field_sort . ' asc,' . $pk . ' desc')->select();

        //将序号重新按1开始排序
        foreach ($data as $key => $val) {
            $data[$key][$field_sort] = $key + 1;
        }
        //处理更改排序操作
        foreach ($data as $key => $val) {
            if ($type == 'asc') {
                if (($key == '0') && $val[$pk] == $id) {
                    break;//首位菜单 点升序，直接中断
                }
                //升序操作：当前菜单序号减一，前一位的序号加一
                if ($val[$pk] == $id) {
                    $data[$key - 1][$field_sort]++;
                    $data[$key][$field_sort]--;
                    break;
                }
            } elseif ($type == 'desc') {
                if (($key == count($data)) && $val[$pk] == $id) {
                    break;//末位菜单 点降序，直接中断
                }
                //降序操作：当前菜单序号加一，后一位的序号减一
                if ($val[$pk] == $id && isset($data[$key + 1])) {
                    $data[$key][$field_sort]++;
                    $data[$key + 1][$field_sort]--;
                    break;
                }
            }
        }
        return !empty($data) ? $data : [];
    }

    if (!function_exists('get_retail_price'))
    {
        /**
         * 获取商城商品默认零售价
         * @param $price
         * @param $ratio
         * @return float
         */
        function get_retail_price($price, $ratio)
        {
            //TODO 零售价计算规则
            return ceil($price * $ratio);
        }
    }
}

if (!function_exists('imgTempFileMove')) {
    /**
     * 处理图片，从临时文件夹转移到 img/** 文件夹
     * @param array $img temp文件夹中的图片路径集
     * @param string $folder
     * @return array
     */
    function imgTempFileMove($img = [], $folder = '')
    {
        $request = \think\Request::instance();
        $folder = !empty($folder) ? $folder : 'img/user/';//文件新目录
        foreach ($img as $k => $v) {
            //内容信息不为空，且确定为temp文件夹
            $v = str_replace($request->domain(), '', $v);
            $v = str_replace('//', '/', $v);
            if (!empty($v) && strpos($v, '/temp/') !== false) {
                $img[$k] = str_replace('/img/temp/', '/' . $folder, $v);

                if (file_exists(PUBLIC_PATH . $v)) {
                    if (!is_dir(PUBLIC_PATH . dirname($img[$k]))) {
                        // 创建目录
                        mkdir(PUBLIC_PATH . dirname($img[$k]), 0777, true);
                    }

                    //转移图片文件，从 img/temp 文件夹，移到 img/** 文件夹中
                    copy(PUBLIC_PATH . $v, PUBLIC_PATH . $img[$k]);

                    //删除 img/temp 文件夹中对应的图片
                    delete_file($v);
                }
            }
        }
        return $img;
    }
}


if (!function_exists('get_parent_ids')) {
    /**
     * 递归获取上级资料id集合
     * @param $id
     * @param $table_name
     * @param bool $merge
     * @param array $res
     * @param array $map
     * @return array
     */
    function get_parent_ids($id, $table_name, &$map = [], $merge = true, &$res=[])
    {
        $pk = Db::name($table_name)->getPk();//获取当前表主键
        $map[$pk] = $id;
        if (has_field($table_name, 'state')) {
            $map['state'] = 1;//启用状态
        }
        $pid = Db::name($table_name)->where($map)->value('parent_id');
        if (!empty($pid)){
            $res[] = $pid;
            get_parent_ids($pid, $table_name, $map, false, $res);
        }
        krsort($res);//进行升序排序
        if ($merge) array_push($res, $id);
        return $res;
    }
}