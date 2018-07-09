<?php

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\District as siteDistrict;
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
        // TODO 验证
        // 纬度
        $lat = $this->data['lat'] ?? '' ;
        // 经度
        $lng = $this->data['lng'] ?? '';
        $location = $lat.','.$lng;
        $map = new MapSite();
        $this->result['data']['address'] = $map->getGeocoder($location);
        return json($this->result, 200);
    }

}