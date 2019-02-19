<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/19 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\common\model\Factory as CoreFactory;
use app\common\validate\BaseValidate;
use think\Db;


class Factory extends CoreFactory
{

    /**
     * 创建厂家
     * @param $saveData
     * @return $this|bool
     * @throws \app\lib\exception\BaseException
     */
    public function createFactory($saveData)
    {
        try {
            Db::startTrans();
            $result = $this->save($saveData);
            if ($result) {
                (new User())->where('id', user_info('id'))->update(['type' => 1, 'group_id' => $this->id]);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
    }
}