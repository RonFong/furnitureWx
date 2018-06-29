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

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Music as MusicService;
use app\lib\enum\Response;
use think\Request;

/**
 * 音乐
 * Class Music
 * @package app\api\controller\v1
 */
class Music extends BaseController
{
    protected $service;

    function __construct(Request $request = null, MusicService $service)
    {
        parent::__construct($request);
        $this->service = $service;
    }

    /**
     * 获取推荐音乐列表
     */
    public function getRecommendList()
    {
        $this->result['data'] = $this->service->getRecommendList($this->data['page'] ?? 0, $this->data['row'] ?? 10);
        return json($this->result, 200);
    }

    /**
     * 查找音乐
     * @param query string  搜索关键字  音乐名|歌手名
     * @return array
     */
    public function searchMusic()
    {
        if (!array_key_exists('query', $this->data) || empty($this->data)) {
            $this->response->error(Response::QUERY_CANT_EMPTY);
        }
        $this->result['data'] = $this->service->searchMusic($this->data['query']);
        return json($this->result, 200);
    }


    /**
     * 通过id获取音乐文件地址
     * @param songId string  音乐id  通过 searchMusic接口获取
     * @return \think\response\Json
     */
    public function getMusic()
    {
        $this->result['data'] = $this->service->getMusicBySongId($this->data['id']);
        return json($this->result, 200);
    }
}