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

namespace app\lib\baiduAI;

/**
 * 内容审核
 */
class ContentCensor
{
    /**
     * 违禁命中点
     * @var array
     */
    protected $hit = [];

    /**
     * 违禁标签
     * @var array
     */
    protected $hitTag = [];

    /**
     * 违禁描述
     * @var array
     */
    protected $msg = [];

    /**
     * 文本违禁类型
     * @var array
     */
    protected $labels = [
        'text'  => [
            1	=> '暴恐违禁',
            2	=> '文本色情',
            3	=> '政治敏感',
            4	=> '恶意推广',
            5	=> '低俗辱骂'
        ],
        'img'   => [
            1   => '色情',
        ]

    ];

    /**
     * 每次审核的最大字符长度
     * 百度API 限制
     * @var array
     */
    protected $textMaxLength = 20000;

    /**
     * 图像审核，服务调用列表
     * @var array
     */
    protected $imgScenes = [
//         'politician',       //政治敏感识别
        'antiporn',         //色情识别
//         'terror',           //暴恐识别
//         'disgust',          //恶心图像识别
//        'watermark',        //广告检测
//         'quality'           //图像质量检测
    ];


    /**
     * 审核结果
     * @var array
     */
    protected $result = [
        'state'        => 0,              //结果 0 审核通过  1 违禁  2 人工复审
        'contentType'  => 1,              //内容类型   1 文本   2 图片
        'hitTag'       => 0,              //违禁类型标签
        'hit'          => '',             //命中内容点
        'msg'          => '审核通过'       //违禁描述
    ];

    /**
     * 百度AI实例
     * @var \app\api\service\ContentCensor
     */
    protected $apiModel;

    function __construct()
    {
        $this->apiModel = new AipImageCensor();
    }

    /**
     * 文本审核外部调用方法
     * @param $content string 单条文本内容审核 || array 多条文本批量审核
     * @return array|mixed
     * @throws \Exception
     */
    static public function text($content)
    {
        if ((is_array($content) && in_array('', $content)) || $content == '') {
            exception('审核内容不能是空');
        }
        $class = new self();
        $result = $class->result;
        if (is_array($content)) {
            foreach ($content as $text) {
                $state = $class->textCensor($text);
                if ($state === 0) {
                    continue;
                }
                $result['state'] = $state;
                //发现违禁，终止审核
                if ($state == 1)
                    break;
            }
        } else {
            $result['state'] = $class->textCensor($content);
        }

        if (!empty($class->hitTag))
            $result['hitTag'] = is_array($class->hitTag) ? implode(',', array_unique($class->hitTag)) : $class->hitTag;

        if (!empty($class->hit))
            $result['hit'] = is_array($class->hit) ? implode(',', array_unique($class->hit)) : $class->hit;

        if (!empty($class->msg))
            $result['msg'] = is_array($class->msg) ? implode(',', array_unique($class->msg)) : $class->msg;

        return $result;
    }

    /**
     * 图片审核外部调用方法
     * @param $content
     * @return mixed
     */
    static public function img($content)
    {
        $class = new self();
        return $class->imgCensor($content);
    }

    /**
     * 文本审核
     * @param $text   string  文本内容
     * @return mixed
     */
    protected function textCensor($text)
    {
        $check = function ($text) {
            $response = $this->apiModel->antiSpam($text);
            if (array_key_exists('error_code', $response)) {
                //AI调用失败
                return false;
            }
            //允许 恶意推广 类通过审核
            $tempArr = $response['result']['reject'];
            if (!empty($tempArr)) {
                foreach ($tempArr as $k => $v) {
                    if ($v['label'] == 4) {
                        unset($tempArr[$k]);
                    }
                }
            }
            $state = empty($tempArr) ? 0 : 1;
            $response['result']['reject'] = array_values($tempArr);
            // spam: 0  审核通过   1 违禁  2 人工复审
            if ($state !== 0) {
                //reject 违禁信息   review  复审信息
                $type = $state == 1 ? 'reject' : 'review';

                $labels = $response['result'][$type][0]['label'];
                $hitTag = $response['result'][$type][0]['label'];
                $hit = $response['result'][$type][0]['hit'];
                $msg = $this->labels['text'][$labels];

                //当前审核结果为 ‘违禁’，则仅返回 ‘违禁’ 信息
                $this->hitTag = $state == 1 ? $hitTag : array_merge($this->hitTag, [$hitTag]);
                $this->hit = $state == 1 ? $hit : array_merge($this->hit, $hit);
                $this->msg = $state == 1 ? $msg : array_merge($this->msg, [$msg]);
            }
            return $state;
        };

        //字符长度是否超过API限制
        $node = ceil(mb_strlen($text) / $this->textMaxLength);
        if ($node > 1) {
            for ($i = 1; $i <= $node; $i ++) {
                $fragment  = mb_substr($text, ($i - 1) * $this->textMaxLength, $this->textMaxLength, 'utf8');
                $state = $check($fragment);
                if ($state === 0) {
                    continue;
                }
                //发现违禁，终止审核
                if ($state == 1)
                    break;
            }
        } else {
            $state = $check($text);
        }
        return $state;
    }

    /**
     * 图片审核
     * @param $content  图片文件
     * @return mixed
     */
    protected function imgCensor($content)
    {
        $response = $this->apiModel->imageCensorComb(file_get_contents($content), $this->imgScenes);
        if (array_key_exists('error_code', $response)) {
            return false;
        }
        $conclusion = $response['result']['antiporn']['conclusion'];
        if (in_array($conclusion, $this->labels['img'])) {
            $this->result['state'] = 1;
            $this->result['hitTag'] = $conclusion;
            $this->result['hit'] = $conclusion;
            $this->result['msg'] = $conclusion;
        }
        return $this->result;
    }
}
