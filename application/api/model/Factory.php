<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2019/2/19 
// +----------------------------------------------------------------------


namespace app\api\model;

use app\api\service\WXACodeUnlimit;
use app\common\model\Factory as CoreFactory;
use app\common\validate\BaseValidate;
use think\Db;
use think\Request;


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
            $saveData['admin_user'] = user_info('id');
            $result = $this->save($saveData);
            if ($result) {
                (new User())->where('id', user_info('id'))->update(['type' => 1, 'group_id' => $this->id]);
            }
            $this->createQrCodeImg($this->id);
            Db::commit();
            return $this->id;
        } catch (\Exception $e) {
            Db::rollback();
            (new BaseValidate())->error($e);
        }
    }

    /**
     * 生成门店首页小程序码并保存
     * 小程序环境要求： 已发布
     * @param $factoryId
     * @return bool
     */
    protected function createQrCodeImg($factoryId)
    {
        try {
            $img = WXACodeUnlimit::create('pages/factoryDetail/factoryDetail', $factoryId);
            $saveRqCode['qr_code_img'] = $img;
            $saveRqCode['qr_code_img_thumb'] = $img;
            $this->where('id', $factoryId)->update($saveRqCode);
        } catch (\Exception $ex) {
            Db::table('error_log')->insert([
                'url' => Request::instance()->url(),
                'params' => json_encode(Request::instance()->param()),
                'msg' => '门店首页小程序码生成失败：' . $ex->getMessage()
            ]);
            return false;
        }
        return true;
    }


    public function supplementInfo($saveData)
    {

    }
}