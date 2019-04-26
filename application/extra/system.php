<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/8/5 
// +----------------------------------------------------------------------

return [
    //管理员用户密码盐值
    'default_salt'          => '99jia',
    //零售价比例
    'price_ratio'           => 2,
    //厂家质量保证金，额度与星级
    'margin_star'           => [
        1000    => 1,
        2000    => 2,
        3000    => 3,
        12000   => 4
    ],
    //收藏、关注、点赞
    'msg_for_frequently' => '点太快了，别急',

    //个人贡献人气值增长间隔时间   1 小时
    'popularity_time'   => 0,

    //个人贡献文章阅读量值增长间隔时间   1 小时
//    'read_interval_time'   => 3600,
    'read_interval_time'   => 0,

    //视频截帧
    //    t	截图时间	单位ms，[0,视频时长]
    //    w	截图宽度，如果指定为0则自动计算	像素值：[0,视频宽度]
    //    h	截图高度，如果指定为0则自动计算，如果w和h都为0则输出为原视频宽高	像素值：[0,视频高度]
    //    m	截图模式，不指定则为默认模式，根据时间精确截图，如果指定为fast则截取该时间点之前的最近的一个关键帧	枚举值：fast
    //    f	输出图片格式	枚举值：jpg、png
    'video_snapshot'       => '?x-oss-process=video/snapshot,t_3000,f_jpg,w_480,h_360,m_fast',
    //高度自适应
    'video_snapshot_auto'  => '?x-oss-process=video/snapshot,t_3000,f_jpg,w_500,h_0,m_fast',
];
