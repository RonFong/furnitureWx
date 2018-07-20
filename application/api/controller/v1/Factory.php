<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Factory as factoryModel;
use app\lib\enum\Response;
use think\Request;

class Factory extends BaseController
{

    /**
     * 参数校验统一入口方法
     * @param string $scene 场景
     * @param array $rules  规则
     *                      Shop constructor.
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->currentModel    = new factoryModel();
        $this->currentValidate = validate('factory');
    }

    /**
     * @api      {get} /v1/factory/FactoryList 获取工厂列表
     * @apiGroup Factory
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
     *              "picture":
     *              "http://qukufile2.qianqian.com/data2/music/FC9FD728B566E6CB18F1025F05689832/253348925/253348925.jpg@s_1,w_90,h_90"
     *                //歌曲图像
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
    public function getFactoryList()
    {

        $getFactoryListData = [
            'page' => $this->page,
            'row'  => $this->row,
        ];
        $result             = $this->currentModel->getFactoryList($getFactoryListData);

        return json($result);
    }

    /**
     * @api      {get} /v1/factory/FactoryList 获取工厂列表
     * @apiGroup Factory
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
     *              "picture":
     *              "http://qukufile2.qianqian.com/data2/music/FC9FD728B566E6CB18F1025F05689832/253348925/253348925.jpg@s_1,w_90,h_90"
     *                //歌曲图像
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
    public function getFactoryProduct($factoryId)
    {

        $getFactoryProductData = [
            'page'      => $this->page,
            'row'       => $this->row,
            'factoryId' => $factoryId,
        ];
        $result                = $this->currentModel->getFactoryProduct($getFactoryProductData);

        return json($result);
    }

}