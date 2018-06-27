<?php

namespace app\api\model;


class Shop extends BaseModel
{
    public function saveData($data = [])
    {
        $result = $this->save($data);
        return $result;
    }
}