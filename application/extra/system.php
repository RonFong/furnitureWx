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
    'price_ratio'           => 1.3,
    //厂家质量保证金，额度与星级
    'margin_star'           => [
        1000    => 1,
        2000    => 2,
        3000    => 3,
        12000   => 4
    ],
];
