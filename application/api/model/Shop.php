<?php

namespace app\api\model;

use app\common\model\Shop as CoreShop;

class Shop extends CoreShop
{
    public function saveData($data = [])
    {
        $this->data($data);
        $this->save();
        return $this;
    }

    public function selectShop()
    {

    }
}