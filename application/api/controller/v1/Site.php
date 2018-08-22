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
        // 1 :厂家 2：商家
        $type = $this->data['type'] == 1 ? 1 : 2;

        $word = empty($this->data['w']) ? '' : $this->data['w'];
//        $table = $type == 1 ? 'factory' : 'shop';
        if($type == 1){
            $table = 'factory';
            $fields = ['id','factory_img','factory_name','province','city','district','town','address','lng','lat'];
        }else{
            $table = 'shop';
            $fields = ['id','shop_img','shop_name','province','city','district','town','address','lng','lat'];
        }
        $longitude = sprintf("%.5f", $lng);
        $latitude  = sprintf("%.5f", $lat);
        $squares   = \app\api\service\Site::getaround(250, $latitude, $longitude);
        $w1        = $squares['right-bottom']['lat'] - 0.00001;
        $w2        = $squares['left-top']['lat'] + 0.00001;
        $w3        = $squares['left-top']['lng'] - 0.00001;
        $w4        = $squares['right-bottom']['lng'] + 0.00001;
        $store_data = Db::name($table)
            ->field($fields)
            ->where('lat','>',0)
            ->where('lat','>',$w1)
            ->where('lat','<',$w2)
            ->where('lng','>',$w3)
            ->where('lng','<',$w4)
            ->where(function ($query) use ($table,$word) {
                if(!empty($word)){
                    $query->where($table.'_name','like','%'.$word.'%');
                }
            })
            ->select();

        $result = [];
        if(!empty($store_data)){
            foreach ($store_data as $item){
                $tmp['id'] = $item['id'];
                $tmp['name'] = $type == 1 ? $item['factory_name'] : $item['shop_name'];
                $tmp['img'] = $type == 1 ? $item['factory_img'] : $item['shop_img'];
                if($item['province'] == $item['city']){
                    $tmp['address'] = $item['province'].$item['district'].$item['town'].$item['address'];
                }else{
                    $tmp['address'] = $item['province'].$item['city'].$item['district'].$item['town'].$item['address'];
                }
                $tmp['distance'] = \app\api\service\Site::getDistance($lng,$lat,$item['lng'],$item['lat']);
                $tmp['pop'] = rand(100,1000);
                $result[] = $tmp;
                $sort_pop[] = $tmp['pop'];
            }
            array_multisort($sort_pop,SORT_DESC,$result);
        }
        $this->result['data'] = $result;
        return json($this->result, 200);
    }
}