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
            ->order(['sort' => 'desc'])
            ->select();
//        dump(collection($result)->toArray());die;
//        $data = \Tree::getTree($result,$name='classify_name',$id='id',$pid='parent_id');
//        dump($data);die;
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
        $result = ['0' => '无'];
        foreach ($tree as $id => $item){
            $result[$id] = $item['classify_name'];
            if(!empty($item['son'])){
                foreach ($item['son'] as $son_id => $value){
                    $result[$son_id] = '|--'.$value['classify_name'];
                }
            }
        }
        return $result;
    }
}