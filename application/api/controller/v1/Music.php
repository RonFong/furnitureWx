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
use app\api\model\MusicCategory;
use app\api\service\Music as MusicService;
use app\lib\enum\Response;
use think\Request;
use app\api\model\Music as MusicModel;
use app\common\validate\Music as MusicValidate;

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
        $this->currentModel = new MusicModel();
        $this->currentValidate = new MusicValidate();
    }

    /**
     * @api {get} /v1/music/getCategoryList 获取音乐库音乐分类
     * @apiGroup Music
     *
     * @apiParamExample  {string} 请求参数格式：
     * 见接口地址
     *
     * @apiSuccessExample {json} 成功时的返回：
     *  {
     *      "state": 1,
     *      "msg": "success",
     *      "data": [
     *          {
     *              "id": 1,
     *              "category_name": "天籁之音",        //分类名
     *              "quantity": 2                       //音乐数量
     *          },
     *          {
     *              "id": 2,
     *              "category_name": "青葱校园",
     *              "quantity": 2
     *          },
     *          {
     *              "id": 3,
     *              "category_name": "生活正能量",
     *              "quantity": 3
     *          }
     *      ]
     *  }
     */
    public function getCategoryList(MusicCategory $musicCategory)
    {
        try {
            $this->result['data'] = $musicCategory->getCategoryList();
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }



    /**
     * @api {get} /v1/music/getByCategory 根据分类获取音乐列表
     * @apiGroup Music
     *
     * @apiParam {number} category_id 分类id
     *
     * @apiParamExample  {string} 请求参数格式：
     * 略
     *
     * @apiSuccessExample {json} 成功时的返回：
     *{
     *    "state": 1,
     *    "msg": "success",
     *    "data": [
     *        {
     *            "id": 1,
     *            "name": "あの日の川へ",             //音乐名
     *            "author": "久石让",
     *            "link": "http://zhangmen28ebc34.mp3",   //音乐文件地址
     *            "img": "http://qukufile2.qianqian.com/data2/pic/763021c8b43/596773143.jpg@s_1,w_90,h_90"  //缩略图
     *            },
     *        {
     *            "id": 2,
     *            "name": "あの日の川へ",
     *            "author": "久石让",
     *            "link": "http://zhangmen28ebc34.mp3",   //音乐文件地址
     *            "img": "http://qukufile2.qianqian.com/data2/pic/763021c8b43/596773143.jpg@s_1,w_90,h_90"  //缩略图
     *        }
     *    ]
     *}
     */
    public function getByCategory()
    {
        try {
            $this->currentValidate->goCheck('getByCategory');
            $this->result['data'] = $this->currentModel->getByCategory($this->data['category_id']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }

    /**
     * @api {get} /v1/music/query 根据音乐名或艺术家名模糊查找音乐
     * @apiGroup Music
     *
     * @apiParam {string} query 音乐名或艺术家名
     *
     * @apiParamExample  {string} 请求参数格式：
     * 略
     *
     * @apiSuccessExample {json} 成功时的返回：
     *{
     *    "state": 1,
     *    "msg": "success",
     *    "data": [
     *        {
     *            "id": 1,
     *            "name": "あの日の川へ",             //音乐名
     *            "author": "久石让",
     *            "link": "http://zhangmen28ebc34.mp3",   //音乐文件地址
     *            "img": "http://qukufile2.qianqian.com/data2/pic/763021c8b43/596773143.jpg@s_1,w_90,h_90"  //缩略图
     *            },
     *        {
     *            "id": 2,
     *            "name": "あの日の川へ",
     *            "author": "久石让",
     *            "link": "http://zhangmen28ebc34.mp3",   //音乐文件地址
     *            "img": "http://qukufile2.qianqian.com/data2/pic/763021c8b43/596773143.jpg@s_1,w_90,h_90"  //缩略图
     *        }
     *    ]
     *}
     */
    public function query()
    {
        try {
            $this->currentValidate->goCheck('query');
            $this->result['data'] = $this->currentModel->query($this->data['query']);
        } catch (\Exception $e) {
            $this->response->error($e);
        }
        return json($this->result, 200);
    }




    /**
     * @api {get} /v1/music/recommend/:page/:row 获取推荐音乐
     * @apiGroup Music
     * @apiParam {number} page 页码 （当前只有1页）
     * @apiParam {number} row  条目数 （当前只有10条数据）
     *
     * @apiParamExample  {string} 请求参数格式：
     * 见接口地址
     *
     * @apiSuccessExample {json} 成功时的返回：
     * {
     *      "state": 1,
     *      "msg": "success",
     *      "data": {
     *      "row": 10,
     *      "song_list": [
     *          {
     *              "id": "1990049",                   //音乐ID
     *              "name": "小步舞曲",                //音乐名
     *              "author": "贝多芬",                //艺术家名
     *              "picture": "http://qukufile2.qianqian.com/data2/music/FC9FD728B566E6CB18F1025F05689832/253348925/253348925.jpg@s_1,w_90,h_90"   //歌曲图像
     *              },
     * }
     *
     * @apiErrorExample {json} 错误返回值：
     * {
     *      "state":0,
     *      "msg":"错误信息",
     *      "data":[]
     * }
     */
    public function getRecommendList()
    {
        $this->result['data'] = $this->service->getRecommendList($this->data['page'] ?? 0, $this->data['row'] ?? 10);
        return json($this->result, 200);
    }

    /**
     * @api {get} /v1/music/search/:query 查找音乐
     * @apiGroup Music
     * @apiParam {string} query 搜索条件
     *
     * @apiParamExample  {string} 请求参数格式：
     * 见接口地址
     *
     * @apiSuccessExample {json} 成功时的返回：
     * {
     *      "state": 1,
     *      "msg": "success",
     *      "data": {
     *      "row": 10,
     *      "song": [
     *          {
     *              "id": "596779222",        //音乐ID
     *              "name": "あの日の川へ",    //音乐名
     *              "author": "久石让",        //艺术家名
     *          },
     * }
     *
     * @apiErrorExample {json} 错误返回值：
     * {
     *      "state":0,
     *      "msg":"错误信息",
     *      "data":[]
     * }
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
     * @api {get} /v1/music/getLink/:id 获取音乐文件地址
     * @apiGroup Music
     * @apiParam {number} id 音乐的ID
     *
     * @apiParamExample  {string} 请求参数格式：
     * 见接口地址
     *
     * @apiSuccessExample {json} 成功时的返回：
     * {
     *      "state": 1,
     *      "msg": "success",
     *      "data": {
     *              "name": "あの日の川へ",    //音乐名
     *              "author": "久石让",        //艺术家名
     *              "link": "http://zhangmenshiting.qianqian.com/data2/music/f8d718a910550e85d0a3f7053488c221/596779534/596779534.mp3?xcode=b559392c4cf83addb6f28ebc1a3b5868",        //文件在线地址
     *              "picture": "http://qukufile2.qianqian.com/data2/pic/763021e4882c8b773dc9c748d94d38df/596773143/596773143.jpg@s_1,w_90,h_90",        //音乐图像
 *          }
     * }
     *
     * @apiErrorExample {json} 错误返回值：
     * {
     *      "state":0,
     *      "msg":"错误信息",
     *      "data":[]
     * }
     */
    public function getLink()
    {
        if (!array_key_exists('id', $this->data) || empty($this->data)) {
            $this->response->error(Response::QUERY_CANT_EMPTY);
        }
        $this->result['data'] = $this->service->getMusicBySongId($this->data['id']);
        return json($this->result, 200);
    }
}