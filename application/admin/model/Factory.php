<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Factory as CoreFactory;
use traits\model\SoftDelete;

/**
 * 厂家表
 * Class Factory
 * @package app\admin\model
 */
class Factory extends CoreFactory
{
    use SoftDelete;

    public function getUserNameAttr($value, $data)
    {
        return User::where('id', $data['user_id'])->value('user_name');
    }

    public function getDistrictAttr($value, $data)
    {
        $district = (new District())->whereIn('id', [$data['province'], $data['city']])->column('name');
        return ($district[0] ?? '&emsp;') . (isset($district[1]) ? ' - ' . $district[1] : '');
    }

    public function getIntroduceTextAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    public function doSave($saveData)
    {
        $saveData['logo'] = empty($saveData['org_logo']) ? DEFAULT_LOGO : $saveData['org_logo'];
        if ($saveData['upload_logo']) {
            $logoPath = self::uploadImg('logo', 'logo');
            $saveData['logo'] = $logoPath ?? $saveData['logo'];
        }
        if (!empty($saveData['introduce'])) {
            $saveData['introduce'] = htmlspecialchars($saveData['introduce']);
        }
        if(self::save($saveData)) {
            return $this->id;
        }
        return false;
    }
}