<?php
namespace app\api\model;

use app\common\model\Factory as CoreFactory;

class Factory extends CoreFactory
{

    public function getFactoryList($data)
    {

        $result = $this->field('*')->page($data['page'], $data['row'])->select();

        return $result;
    }
}