<?php

return [
    'tencent_map' => [
        'key'   =>  'NOIBZ-TQXCW-2QRR2-O6LX7-5GV7H-B7BWK'
    ],
    //公众号 AppID
    'gzh_app_id'            => 'wxa6d691299093f1a4',
    'gzh_app_id'            => '',
    //公众号 AppSecret
    'gzh_app_secret'        => 'e595b441429a15c3d5526e4accf7cf7f',
//    'gzh_app_secret'        => '7a91a512262ec4a9dea5a507612e48a0',
    'gzh_app_secret'        => '',
//    'gzh_app_secret'        => '',
    //小程序 AppID
    'xcx_app_id'            => 'wx195a5e8ed1a55ead',
    'xcx_app_id'            => '',
    //小程序 AppSecret
    'xcx_app_secret'        => 'd0a065f66e34734712f8b4310691b5c3',
    'xcx_app_secret'        => '',
    //腾讯SMS短信服务
    'sms_app_id'            => '1400108281',
    'sms_app_key'           => 'fe504050bd4bcaed5651ed2f1a093611',
    'sms_app_id'            => '',
    'sms_app_key'           => '',
    //腾讯地图
    'map_app_id'            => '1400108281',
    'map_app_key'           => 'fe504050bd4bcaed5651ed2f1a093611',
    'map_app_secret'        => 'NOIBZ-TQXCW-2QRR2-O6LX7-5GV7H-B7BWK',
    'map_app_id'            => '',
    'map_app_key'           => '',
    'map_app_secret'        => '',
    //百度AI
    //自然语言处理 (文本审核)
    'nlp_app_name'          => '99家-文本审核',
    'nlp_app_id'            => '11485274',
    'nlp_app_key'           => '3UkfhG9Nay8lpjCU8B4hQjoG',
    'npl_app_secret'        => '4BUv5I8TMMFHWChfyUal8jNN95rnMZEe',
    'nlp_app_id'            => '',
    'nlp_app_key'           => '',
    'npl_app_secret'        => '',
    //内容审核     (图像)
    'img_app_name'          => '99家',
    'img_app_id'            => '11471899',
    'img_app_key'           => 'kyUfUb38Wgr2Fic8BVjf41cp',
    'img_app_secret'        => 'damBetFzgbXKVDG9czvEtqgHj5TWEOSO',
    'img_app_id'            => '',
    'img_app_key'           => '',
    'img_app_secret'        => '',
    //user token 缓存时间
    'token_valid_time'      => 7200,
    //用户默认头像
    'default_avatar'        => '/static/img/user_icon/default.jpg',
    //阿里云存储
    'oss'                   => [
        'local'  => [
            'accessKeyId'       =>  'LTAI0ZISMkC8V3QE',
            'accessKeySecret'   => '80mxDCVBzNwXhbvzFQE5CiZIX8uF7j',
            'endpoint'          => 'oss-cn-hangzhou.aliyuncs.com',              //地域节点  上传
            'bucket'            => 'test-api-multimedia',                        //存储空间名
            'accessKeyId'       =>  '',
            'accessKeySecret'   => '',
            'endpoint'          => '',              //地域节点  上传
            'bucket'            => ''                        //存储空间名
        ],'test'  => [
            'accessKeyId'       =>  'LTAI0ZISMkC8V3QE',
            'accessKeySecret'   => '80mxDCVBzNwXhbvzFQE5CiZIX8uF7j',
            'endpoint'          => 'oss-cn-hangzhou-internal.aliyuncs.com',     //地域节点  上传
            'bucket'            => 'test-api-multimedia',                        //存储空间名
            'accessKeyId'       =>  '',
            'accessKeySecret'   => '',
            'endpoint'          => '',     //地域节点  上传
            'bucket'            => '',                        //存储空间名
        ],
        'online'    => [
            'accessKeyId'       =>  'LTAIAaYdblbcmeSY',
            'accessKeySecret'   => 'gx0I7OkRGSxFgA9fKpbs00r8wkWTI1',
            'endpoint'          => 'oss-cn-shenzhen-internal.aliyuncs.com',
            'bucket'            => 'api-multimedia',
            'accessKeyId'       =>  '',
            'accessKeySecret'   => '',
            'endpoint'          => 'm',
            'bucket'            => '',
        ]
    ]
];

