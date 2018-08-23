<?php

namespace app\common\model;


class GroupClassify extends Model
{
    public function getClassifyList($group_id,$group_type)
    {
        $group_id = 2;
        $group_type = 1;
        $result = $this
            ->field(['id','parent_id','classify_name'])
            ->where('group_id',$group_id)
            ->where('group_type',$group_type)
            ->where('parent_id',0)
            ->order(['sort' => 'desc'])
            ->select();
        $tree =$this->formatTree($result,0);
        $data = $this->_picker_tree_data($tree);
        return $data;
    }

    public function formatTree($arr,$pid=0){
        $data = [];
        foreach($arr as $k => $v){
            if($v['parent_id']==$pid){
                $data[$v['id']]=$v;
                $data[$v['id']]['son']= $this->formatTree($arr,$v['id']);
            }
        }
        return isset($data)?$data:array();
    }

    public function _picker_tree_data($tree)
    {
        $result_data = ['0' => '无'];
        $result_obj = ['0' => ['id' => 0,'classify_name' => '无']];
        foreach ($tree as $id => $item){
            $tmp['id'] = $id;
            $tmp['classify_name'] = $item['classify_name'];
            $result_data[] = $item['classify_name'];
            $result_obj[] = $tmp;
            if(!empty($item['son'])){
                foreach ($item['son'] as $son_id => $value){
                    $tmp['id'] = $son_id;
                    $tmp['classify_name'] = '|--'.$value['classify_name'];
                    $result_obj[] = $tmp;
                    $result_data[] = $item['classify_name'];
                }
            }
        }
        $result = ['array' => $result_data,'objArray' => $result_obj];
        return $result;
    }
}