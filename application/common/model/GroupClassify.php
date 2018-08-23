<?php

namespace app\common\model;


class GroupClassify extends Model
{
    public function getClassifyList($group_id,$group_type)
    {
//        $group_id = 2;
//        $group_type = 1;
        $result = $this
            ->field(['id','parent_id','classify_name'])
            ->where('group_id',$group_id)
            ->where('group_type',$group_type)
            ->order(['sort' => 'desc'])
            ->select();
        $three = array_values($this->formatTree($result,0));
        $classify_data = [
            '0' => [
                'id' => 0,
                'parent_id' => 0,
                'classify' => '无',
                'son' => []
            ]
        ];
        $data = array_merge($classify_data,$three);
        return $data;
    }

    public function formatTree($arr,$pid=0){
        foreach($arr as $k => $v){
            if($v['parent_id']==$pid){
                $data[$v['id']]=$v;
                $data[$v['id']]['son']= array_values($this->formatTree($arr,$v['id']));
            }
        }
        return isset($data)?$data:array();
    }
}