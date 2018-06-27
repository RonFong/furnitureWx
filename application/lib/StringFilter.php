<?php

namespace app\lib;

class StringFilter
{
    /**
     *  公共字符串过滤
     */

    /**
     * 手机号码验证
     * @param $phone
     * @return false|int
     */
    public static function checkPhone($phone)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        return preg_match($rule, $phone);
    }

    /**
     * 控制符过滤
     * @param $str      //需要过滤的字符串
     * @return string
     */
    public static function controlCharacter($str)
    {
        return preg_replace('/[^\P{C}]+/u', '', $str);
    }
}