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

namespace app\api\model;

use app\common\model\StoreClassify as CoreStoreClassify;

class StoreClassify extends CoreStoreClassify
{
    public function storeClassifyList()
    {
        $fields = ['id','parent_id','name'];
        $all = $this
            ->field($fields)
            ->where('state',1)
            ->select();
//        dump($all);die;
        return array_values($this->formatTree($all,0));
    }

    public function formatTree($arr,$pid=0){
        foreach($arr as $k => $v){
            if($v['parent_id']==$pid){
                $data[$v['id']]=$v;
                $data[$v['id']]['son']=$this->formatTree($arr,$v['id']);
            }
        }
        return isset($data)?$data:array();
    }
}