<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/2/21 
// +----------------------------------------------------------------------

namespace app\common\db;

use think\db\Query as CoreQuery;
class Query extends CoreQuery
{
    public function dataTable($append=[],$hidden=[],$visible=[])
    {
        $tmpOption = $this->options;
        //得到总记录数
        $filteredCount = $Total = $this->count();
        $this->options = $tmpOption;

        $request = \think\Request::instance();
        //得到标记
        $draw = $request->param('draw');
        $page = $request->param('start');
        $rows = $request->param('length');
        //排序
        $order_column = $request->param('order');

        if (empty($order_column)) {
            $order = array_key_exists('order', $this->options) ? $this->options['order'] : '';
        } else {
            if (array_key_exists('order', $this->options)) {
                $order = $order_column . ',' . implode(',',$this->options['order']);
            } else {
                $order = $order_column;
            }
        }

        if ($rows > 0) {
            $list = $this->order($order)->limit($page, $rows)->select();
        } else {
            $list = $this->order($order)->select();
        }

        if (empty($list)) {
            $list = [];
        }
        else
        {
            $list = collection($list)->append($append)->hidden($hidden)->visible($visible)->toArray();
        }
        return [
            "draw" => intval($draw),
            'data' => $list,
            'recordsTotal' => $Total,
            'recordsFiltered' => $filteredCount
        ];
    }

    public function select2()
    {
        $tmpOption = $this->options;
        //得到总记录数
        $total = $this->count();
        $this->options = $tmpOption;
        //得到参数
        $page = input('page');
        $row = input('row');
        $list = $this->page($page,$row)->select();
        return ['total'=>$total,'items'=>$list];

    }

}