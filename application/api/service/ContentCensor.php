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

use app\lib\baiduAI\ContentCensor as CoreContentCensor;

/**
 * 内容审核   (百度AI)
 * Class ContentCensor
 * @package app\api\service
 */
class ContentCensor
{
    /**
     * 文本内容违禁阈值
     * 在此区间的，建议人工审核, 大于此值则拦截
     * @var float
     */
    protected $textScore = [
        'min' => 0.6,
        'max' => 0.8
    ];

    /**
     * 文本违禁类型
     * @var array
     */
    protected $textLabels = [
        1	=> '暴恐违禁',
        2	=> '文本色情',
        3	=> '政治敏感',
        4	=> '恶意推广',
        5	=> '低俗辱骂'
    ];

    /**
     * 请求中是否包含违禁
     * @var array
     */
    protected $textSpam = [
        0   => '非违禁',
        1   => '违禁',
        2   => '建议人工复审'
    ];

    /**
     * 每次审核的最大字符长度
     * @var array
     */
    protected $textMaxLength = 20000;

    /**
     * 百度AI实例
     * @var \app\api\service\ContentCensor
     */
    protected $contentCensor;

    function __construct()
    {
        $this->contentCensor = new CoreContentCensor();
    }

    /**
     * 文本审核
     * @param $text  string  文本内容
     * @return mixed
     */
    public function text($text)
    {
        $check = function ($text) {
            $result = [];
            $response = $this->contentCensor->spam($text);
            if (array_key_exists('error_code', $response)) {
                //AI调用失败
                return false;
            }
            switch ($response['result']['spam']) {
                case 0:
                    //审核通过
                    $result = true;
                    break;
                case 1:
                    //违禁
                    if ($response['result']['reject']) {
                        $labels = $response['result']['reject'][0]['label'];
                        $result['spam'] = 1;
                        $result['labels'] = $labels;
                        $result['msg'] = $this->textLabels[$labels];
                    }
                    break;
                case 2:
                    //人工审核
                    if ($response['result']['review']) {
                        $labels = $response['result']['review'][0]['label'];
                        $result['spam'] = 2;
                        $result['labels'] = $labels;
                        $result['msg'] = $this->textLabels[$labels];
                    }
                    break;
                default:
                    break;
            }
            return $result;
        };
        $result = [];
        //字符长度是否超过API限制
        $node = ceil(strlen($text) / $this->textMaxLength);

        if ($node > 1) {
            $collection = [];
            for ($i = 1; $i <= $node; $i ++) {
                $collection[$i-1] = substr($text, ($i - 1) * $this->textMaxLength, $i * $this->textMaxLength);
            }
            foreach ($collection as $v) {
                $result = $check($v);
                if ($result['spam'] == 1) {
                    break;
                }
            }
        } else {
            $result = $check($text);
        }
       return $result;
    }
}