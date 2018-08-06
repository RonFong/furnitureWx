<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-{2018} http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/8/4 19:59
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class Role extends Validate
{
    /*字段规则*/
    protected $rule = [
        'role_name' => 'require|unique:role'
        ,'menu_list' => 'require'
    ];

    /*返回错误信息*/
    protected $message = [
        "role_name.require" => '角色名称不能为空！'
        ,"role_name.unique" => '角色名称已被使用，不能重复！'
        ,"menu_list.require" => '至少选择一项权限！'
    ];

    protected $scene = [
        'updateField' => ['role_name'=>'unique:role']
    ];
}