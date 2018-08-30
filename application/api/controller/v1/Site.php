<?php

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\District as siteDistrict;
use think\Db;
use think\Request;
use app\api\service\Site as MapSite;

class Site extends BaseController
{
    /**
     * 参数校验统一入口方法
     * @param string $scene     场景
     * @param array $rules      规则
     * Shop constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null) {

        parent::__construct($request);
    }

    public function getRegion()
    {
        $this->currentModel = new siteDistrict();
        $parent_id = $this->data['parent_id'] ?? 0;
        $level = $this->data['level'] ?? 1;
        $this->result['data']['region'] = $this->currentModel->getRegionData($parent_id,$level);
        return json($this->result, 200);
    }

    public function getAddress()
    {
        // 纬度
        $lat = $this->data['lat'] ?? '' ;
        // 经度
        $lng = $this->data['lng'] ?? '';
        $location = $lat.','.$lng;
        $map = new MapSite();
        $this->result['data']['address'] = $map->getGeocoder($location);
        return json($this->result, 200);
    }

    public function getNearbyStore()
    {
        // 纬度
        $lat = $this->data['lat'] ?? '' ;
        // 经度
        $lng = $this->data['lng'] ?? '';

        $word = empty($this->data['w']) ? '' : $this->data['w'];
//        $table = $type == 1 ? 'factory' : 'shop';
        $longitude = sprintf("%.5f", $lng);
        $latitude  = sprintf("%.5f", $lat);
        $squares   = \app\api\service\Site::getaround(250, $latitude, $longitude);
        $w1        = $squares['right-bottom']['lat'] - 0.00001;
        $w2        = $squares['left-top']['lat'] + 0.00001;
        $w3        = $squares['left-top']['lng'] - 0.00001;
        $w4        = $squares['right-bottom']['lng'] + 0.00001;

        $user_store_id = user_info('group_id');
        $user_store_type = user_info('type');

        $shop = new \app\api\model\Shop();
        $factory = new \app\api\model\Factory();
        $select_data = [
            'w1' => $w1,
            'w2' => $w2,
            'w3' => $w3,
            'w4' => $w4,
            'word' => $word,
            'user_store_id' => $user_store_id,
            'user_store_type' => $user_store_type,
        ];
        $shop_data = $shop->getNearByShop($select_data);
        $factory_data = $factory->getNearByFactory($select_data);
        $store_data = array_merge($shop_data,$factory_data);
        $result = [];
        if(!empty($store_data)){
            foreach ($store_data as $item){

                $tmp['id'] = $item['id'];
                $tmp['store_type'] = isset($item['factory_name']) ? 1 : 2;
                $tmp['name'] = isset($item['factory_name']) ? $item['factory_name'] : $item['shop_name'];
                $tmp['img'] = isset($item['factory_img']) ? $item['factory_img'] : $item['shop_img'];
                if($item['province'] == $item['city']){
                    $tmp['address'] = $item['province'].$item['district'].$item['town'].$item['address'];
                }else{
                    $tmp['address'] = $item['province'].$item['city'].$item['district'].$item['town'].$item['address'];
                }
                $tmp['distance'] = \app\api\service\Site::getDistance($lng,$lat,$item['lng'],$item['lat']);
                $tmp['pop'] = 0;
                if(!empty($item['pop'])){
                    $pop = array_column($item['pop'],'value');
                    $tmp['pop'] = array_sum($pop);
                }
                $result[] = $tmp;
                $sort_pop[] = $tmp['pop'];
            }
            array_multisort($sort_pop,SORT_DESC,$result);
        }
        $this->result['data'] = $result;
        return json($this->result, 200);
    }
}