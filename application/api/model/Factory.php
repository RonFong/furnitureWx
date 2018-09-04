<?php
namespace app\api\model;

use app\api\service\Site;
use app\common\model\Factory as CoreFactory;
use think\Db;

class Factory extends CoreFactory
{

    public function saveData($data)
    {

        // 获取经纬度
        if ($data['province'] == $data['city']) {
            $address       = $data['province'] . $data['district'] . $data['town'] . $data['address'];
            $vague_address = $data['province'] . $data['district'];
        } else {
            $address       = $data['province'] . $data['city'] . $data['district'] . $data['town'] . $data['address'];
            $vague_address = $data['province'] . $data['city'] . $data['district'];
        }
        $site    = new Site();
        $lat_lng = $site->getLatLngDetail($address, $data['province']);
        if (empty($lat_lng)) {
            // 模糊搜索
            $lat_lng = $site->getLatLngDetail($vague_address, $data['province']);
            if (empty($lat_lng)) {
                return ['success' => false, 'msg' => '地址不清晰', 'data' => []];
            }
        }
        $data['lat']        = $lat_lng['lat'];
        $data['lng']        = $lat_lng['lng'];
        $data['admin_user'] = user_info('id');
        // 审核暂不审核
        $data['audit_state'] = 1;
        if (!$data['editState']) {
            unset($data['editState']);
            $registerRes = $this->save($data);
            $factory_id  = $this->id;
        } else {
            unset($data['editState']);
            $factory_id          = user_info('group_id');
            $data['update_time'] = time();
            $registerRes         = $this->save($data, ['id' => $factory_id]);
        }
        if ($registerRes) {
            Db::name('user')
                ->where('id', $data['admin_user'])
                ->update([
                    'type'       => 1,
                    'group_id'   => $factory_id,
                    'wx_account' => $data['factory_wx'],
                ]);
        }
        $result = [
            'user_info' => User::get(['id' => $data['admin_user']]),
        ];

        return ['success' => true, 'msg' => '', 'data' => $result];
    }

    public function getFactoryList($data)
    {

        $field  = [
            'id',
            'factory_contact',
            'factory_phone',
            'factory_wx',
            'wx_code',
            'province',
            'city',
            'district',
            'town',
            'address',
            'factory_name',
            'factory_address',
            'category_id',
            'category_child_id',
            'user_name',
            'phone',
            'license_code',
            'factory_img',
        ];
        $where  = [
            'state' => 1,
        ];
        $result = $this->field($field)->where($where)->page($data['page'], $data['row'])->select();

        return $result;
    }

    public function getFactoryProduct($data)
    {

        $result      = [];
        $sql         = "SELECT p.id,p.music,record,classify_name FROM `factory_product` AS p 
                JOIN `group_classify` AS c ON c.id = p.classify_id
                WHERE p.state = 1 AND p.factory_id = {$data['factoryId']}
                LIMIT 1";
        $productInfo = Db::query($sql);
        if (!empty($productInfo)) {
            $result['info']     =
            $sql = "SELECT * FROM `factory_product_content`
                WHERE product_id = {$productInfo['id']}
                ORDER BY sort DESC";
            $productContentList = Db::query($sql);

        } else {

        }

        return $result;

    }

    public function factoryInfo($data)
    {

        $field                 = [
            '*',
        ];
        $where                 = [
            'admin_user' => $data['admin_user'],
        ];
        $result                = $this->field($field)
            ->where($where)
            ->find();
        $getContentData        = [
            'groupId'   => user_info('group_id'),
            'groupType' => user_info('type'),
            'editType'  => 0,
        ];
        $homeContent           = HomeContentItem::getContent($getContentData);
        $result['homeContent'] = $homeContent;

        return $result;
    }

    public function editFactoryInfo($data)
    {

        $where = [
            'admin_user' => $data['admin_user'],
        ];
        $this->where($where)->update($data);

        return $this->factoryInfo($where);
    }

    public function getNearByFactory($data)
    {

        $factory_data = $this
            ->field(['id', 'factory_img', 'factory_name', 'province', 'city', 'district', 'town', 'address', 'lng', 'lat'])
            ->with([
                'pop' => function ($query) {

                    $query->where('object_type', 1);
                },
            ])
            ->where('lat', '>', 0)
            ->where('lat', '>', $data['w1'])
            ->where('lat', '<', $data['w2'])
            ->where('lng', '>', $data['w3'])
            ->where('lng', '<', $data['w4'])
            ->where(function ($query) use ($data) {

                if (!empty($data['word'])) {
                    $query->where('factory_name', 'like', '%' . $data['word'] . '%');
                }
            })
            ->where(function ($query) use ($data) {

                if ($data['user_store_type'] == 1) {
                    $query->whereNotIn('id', [$data['user_store_id']]);
                }
            })
            ->where('state', 1)
            ->select();

        return $factory_data;
    }

    public function pop()
    {

        return $this->hasMany('Popularity', 'object_id', 'id');
    }
}