<?php

namespace app\common\model;


class FactoryProduct extends Model
{

    public function groupClassify()
    {
        return $this->hasOne('group_classify');
    }

}