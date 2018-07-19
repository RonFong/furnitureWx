<?php

namespace app\api\model;

use app\common\model\Category as CoreCategory;


class Category extends CoreCategory
{
    protected $table='shop_category';

    public function getAllCategory()
    {
        $first = $this->where('parent_id',0)->select();
        dump(collection($first)->toArray());
    }
}