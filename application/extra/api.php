<?php

return [
    'tencent_map' => [
        'key'   =>  ''
    ],

    //公众号 AppID
    'gzh_app_id'            => '',
    //公众号 AppSecret
    'gzh_app_secret'        => '',

    //小程序 AppID
    'xcx_app_id'            => '',
    //小程序 AppSecret
    'xcx_app_secret'        => '',

    //腾讯SMS短信服务
    'sms_app_id'            => '',
    'sms_app_key'           => '',

    //腾讯地图
    'map_app_id'            => '',
    'map_app_key'           => '',
    'map_app_secret'        => '',

    //百度AI
    //自然语言处理 (文本审核)
    'nlp_app_name'          => '99家-文本审核',
    'nlp_app_id'            => '',
    'nlp_app_key'           => '',
    'npl_app_secret'        => '',
    //内容审核     (图像)
    'img_app_name'          => '99家',
    'img_app_id'            => '',
    'img_app_key'           => '',
    'img_app_secret'        => '',

    //user token 缓存时间
    'token_valid_time'      => 7200,

    //用户默认头像
    'default_avatar'        => '',

    //阿里云存储
    'oss'                   => [
        'local'  => [
            'accessKeyId'       => '',
            'accessKeySecret'   => '',
            'endpoint'          => '',              //地域节点  上传
            'bucket'            => ''                        //存储空间名
        ],'test'  => [
            'accessKeyId'       => '',
            'accessKeySecret'   => '',
            'endpoint'          => '',     //地域节点  上传
            'bucket'            => ''                        //存储空间名
        ],
        'online'    => [
            'accessKeyId'       => '',
            'accessKeySecret'   => '',
            'endpoint'          => '',
            'bucket'            => ''
        ]
    ]

];
