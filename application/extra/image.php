<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/12/16 
// +----------------------------------------------------------------------


/**
 * 图片上传，尺寸配置
 */
return [
    //门店
    'shop'      => [
        'small'   => [           //缩略图中的小号图
            'w'     => [          //宽度尺寸
                'ratio' => 4,     //宽高比例
                'value' => 480    //值
            ],
            'h'     => [
                'ratio' => 3,
                'value' => 360
            ],
        ],
        'large'  => [           //缩略图中的大号图
            'w'     => [
                'ratio' => 16,
                'value' => 1080
            ],
            'h'     => [
                'ratio' => 9,
                'value' => 607
            ]
        ]
    ],
    //文章
    'article'   => [
        'small'  => [
            'w'     => [
                'ratio' => 4,
                'value' => 500
            ],
            'h'     => [
                'ratio' => 3,
                'value' => 375
            ]
        ],
        'large'   => [
            'w'     => [
                'ratio' => 0,       // 等比缩放
                'value' => 1000
            ],
            'h'     => [
                'ratio' => 0,
                'value' => 0
            ]
        ]
    ],
    //默认
    'default'   => [
        'small'  => [
            'w'     => [
                'ratio' => 1,
                'value' => 400
            ],
            'h'     => [
                'ratio' => 1,
                'value' => 400
            ]
        ],
        'large'  => [
            'w'     => [
                'ratio' => 4,
                'value' => 400
            ],
            'h'     => [
                'ratio' => 3,
                'value' => 300
            ]
        ]
    ]
];