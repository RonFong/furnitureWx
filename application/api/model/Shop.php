<?php

namespace app\api\model;

use app\common\model\Shop as CoreShop;

class Shop extends CoreShop
{
    public function saveData($data = [])
    {
        $result = $this->save($data);
        return $result;
    }
}