<?php
class QueryFields
{
    protected $queryFields = [];  //列表搜索页 where 查询条件

    /*
     * 组装列表搜索页 where 查询条件
     */
    public function setSearchFiled($fields = [])
    {
        foreach ($fields as $k => $v) {
            if (!is_array($v)) {
                //普通查询
                if ($this->request->has($v, 'param', true)) {
                    $this->queryFields[$v] = $this->request->param($v);
                }
            } else {
                //表达式查询
                foreach ($v as $key => $val) {
                    //是否有传值
                    if ($this->request->has($val[1], 'param', true)) {
                        if ($val[0] == 'like' || $val[0] == 'LIKE') {
                            //key 是 xx|xx|xx '或' 格式时
                            $tableFields = $this->currentModel->getTableFields();
                            if (!in_array($key, $tableFields)) {
                                $key = $this->request->param($key);
                            }
                            $val[1] = '%' . $this->request->param($val[1]) . '%';
                        } else {
                            $val[1] = $this->request->param($val[1]);
                        }
                        $this->queryFields[$key] = $val;
                    }
                }
            }
        }
        $this->queryFields = $this->queryFields ?? ['exp', '1=1'];
        return;
    }

    /**
     *用法
     */
    public function data()
    {
        //和搜索框 name 一一对应
        $fields = [
            ["key" => ['like', 'value']],
            ['type' => ['in', 'type']],
            'login_mk', 'sex', 'academic', 'birthday'
        ];
        $this->setSearchFiled($fields);
    }
}