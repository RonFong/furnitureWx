<?php

namespace app\common\model;


class GroupClassify extends Model
{
    public function getClassifyList($type,$id,$group_id,$group_type)
    {
        // type  1:只要一级分类 (带无) 2：一二级分类（不带无）
//        $group_id = 2;
//        $group_type = 1;
        $result = $this
            ->field(['id','parent_id','classify_name'])
            ->where('group_id',$group_id)
            ->where('group_type',$group_type)
            ->where(function ($query) use ($type){
                if($type == 1){
                    $query->where('parent_id',0);
                }
            })
            ->order(['sort' => 'desc'])
            ->select();

        if($type == 1){
            $tree =$this->formatTree($result,0);
            $data = $this->_picker_tree_data($type,$tree);
            if(!empty($id) && $type == 1){
                $edit_classify = $this
                    ->field(['classify_name'])
                    ->where('id',$id)
                    ->find();
                $data['edit_classify'] = $edit_classify;
            }
        }elseif ($type == 2){
            $data = array_values($this->formatTree($result,0));
        }

        return $data;
    }

    public function formatTree($arr,$pid=0){
        $data = [];
        foreach($arr as $k => $v){
            if($v['parent_id']==$pid){
                $data[$v['id']]=$v;
                $data[$v['id']]['son']= array_values($this->formatTree($arr,$v['id']));
            }
        }
        return isset($data)?$data:array();
    }

    public function _picker_tree_data($type,$tree)
    {
        $result_data = [];
        $result_obj = [];
        if($type == 1){
            $result_data = ['0' => '无'];
            $result_obj = ['0' => ['id' => 0,'classify_name' => '无']];
        }
        foreach ($tree as $id => $item){
            $tmp['id'] = $id;
            $tmp['parent_id'] = $item['parent_id'];
            $tmp['classify_name'] = $item['classify_name'];
            $result_data[] = $item['classify_name'];
            $result_obj[] = $tmp;
            if(!empty($item['son'])){
                foreach ($item['son'] as $son_id => $value){
                    $tmp['id'] = $son_id;
                    $tmp['parent_id'] = $value['parent_id'];
                    $tmp['classify_name'] = '|--'.$value['classify_name'];
                    $result_obj[] = $tmp;
                    $result_data[] = $value['classify_name'];
                }
            }
        }
        $result = ['array' => $result_data,'objArray' => $result_obj];
        return $result;
    }

    public function sortGroupClassify($sort_data,$group_id,$group_type)
    {
//        dump($sort_data);die;
        // 一级分类的长度
        $save_data = [];
        $one_length = count($sort_data['classifyList']);
        foreach ($sort_data['classifyList'] as $item){
            $tmp['id'] = $item['id'];
            $tmp['sort'] = $one_length;
            $one_length --;
            $save_data[] = $tmp;
            $two_length = count($sort_data);
            foreach ($item['son'] as $value){
                $tmp['id'] = $value['id'];
                $tmp['sort'] = $two_length;
                $two_length --;
                $save_data[] = $tmp;
            }
        }
        return $this->saveAll($save_data);
    }

    public function secondGroupClassify($group_id,$group_type)
    {
        $objGroup = $this
            ->field(['id','classify_name'])
            ->where('group_id',$group_id)
            ->where('group_type',$group_type)
            ->where('parent_id','>',0)
            ->order(['sort' => 'desc'])
            ->select();
        $arrGroup = array_column($objGroup,'classify_name');
        return ['obj' => $objGroup,'arr' => $arrGroup];
    }
}